<?php

namespace App\Http\Controllers\Api\Dashboard\Messages;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Firebase;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MessagesController extends Controller
{

    public function index(Request $request){
        $validator = Validator::make($request->all(),
            [
                'status' => [
                    'nullable',
                    Rule::in(['unread', 'read']),
                ],
            ],[
                'status' => 'وضعیت را به درستی وارد کنید',
            ]
        );
        if($validator->fails()){
            return error_response($validator->errors()->first());
        }
        $query = Contact::query()->with(['contactReplies'=>function($query)  {
            $query->select(['id','contact_id','created_at'])->latest('created_at')->first();}]);

        if($request->status == 'read'){
            $query = $query->whereNotNull('admin_seen_at');
        }
        elseif($request->status == '' || $request->status == 'unread'){
            $query = $query->whereNull('admin_seen_at');
        }

        return success_response($query->latest()->paginate(10));
    }
    public function store(Request $request,Contact $contact){
        $validator = Validator::make($request->all(),
            [
                'message' => 'required|string|min:3|max:5000'
            ],[
                'message' => 'متن پیام را به درستی وارد کنید',
            ]
        );
        if($validator->fails()){
            return error_response($validator->errors()->first());
        }
        $requester = User::find($contact->user_id);
        $contact->contactReplies()->create([
            'user_id' => auth()->id(),
            'message' => $this->message,
        ]);
        $contact->update(['user_seen_at' => null]);

        $data = [
            'title' => 'پیام از پشتیبانی',
            'message' => $requester->name.' عزیز، پیامی برای شما ارسال شد',
        ];
        Notification::send($requester, new UserNotification(Firebase::dataFormat($data)));
        return success_response(message: 'با موفقیت ایجاد شد');

    }
}
