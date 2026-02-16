<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    /**
     * Create a new support ticket.
     *
     * @group Tickets
     * @authenticated
     * @bodyParam subject string required Subject of the ticket. Example: where is driver?
     * @bodyParam message string required Message content. Example: my request's driver is so late
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "تیکت با موفقیت ایجاد شد لطفا تا پاسخ اپراتور صبور باشید",
     *   "data": {
     *       "id": 2
     *   }
     * }
     * @response 422 {
     *   "status": "error",
     *   "message": "پیام باید بین ۳ تا ۵۰۰۰ کاراکتر باشد",
     *   "data": []
     * }
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'subject' => 'required|string|max:255|required',
                'message' => 'required|string|min:3|max:5000',
            ],
            [
                'subject' => 'حداکثر عنوان ۲۵۵ کاراکتر است',
                'message' => 'پیام باید بین ۳ تا ۵۰۰۰ کاراکتر باشد',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $ticket = Contact::create([
            'user_id'      => $user->id,
            'name'         => $user->name . ' ' . $user->lastname,
            'email'        => $user->email,
            'subject'      => $request->subject,
            'message'      => $request->message,
            'ip'           => $request->ip(),
            'user_seen_at' => now(),
        ]);
        if($ticket){
            return sendJson('success','تیکت با موفقیت ایجاد شد لطفا تا پاسخ اپراتور صبور باشید', ['id' => $ticket->id]);
        }
        return sendJson('error','تیکت شما ثبت نشد لطفا دوباره امتحان کنید');
    }

    /**
     * List all tickets of authenticated user.
     *
     * @group Tickets
     * @authenticated
     * @response 200 {
     *   "status": "success",
     *   "message": "",
     *   "data": {
     *     "limit": 10,
     *     "list": [
     *       {
     *         "id": 2,
     *         "refId": 2,
     *         "title": "where is driver?",
     *         "date": {
     *           "day": "1404/11/21",
     *           "time": "09:54"
     *         },
     *         "seen": true
     *       }
     *     ]
     *   }
     * }
     */
    public function index()
    {
        $user = auth()->user();
        $paginate = 10;
        $data = ['limit' => $paginate, 'list' => []];
        $tickets = Contact::where('user_id',$user->id)->orderBy('updated_at', 'DESC')->paginate($paginate);
        foreach ($tickets as $ticket){
            $data['list'][] = [
                "id"    => $ticket->id,
                "refId" => $ticket->id,
                "title" => $ticket->subject,
                "date"  => [
                    "day" => verta()->instance($ticket->updated_at)->format('Y/m/d'),
                    "time" => verta()->instance($ticket->updated_at)->format('H:i'),
                ],
                'seen' => $ticket->user_seen_at == null ? false : true
            ];
        }
        return sendJson('success','', $data);
    }


    /**
     * Show a single ticket with all messages.
     *
     * @group Tickets
     * @authenticated
     * @urlParam contact required The ID of the ticket. Example: 2
     * @response 200 {
     *   "status": "success",
     *   "message": "",
     *   "data": {
     *     "id": 2,
     *     "refId": 2,
     *     "userId": 5,
     *     "title": "where is driver?",
     *     "date": {
     *       "day": "1404/11/21",
     *       "time": "10:01"
     *     },
     *     "messages": [
     *       {
     *         "type": "admin",
     *         "name": "اپراتور",
     *         "message": "what happend?????",
     *         "date": {
     *           "day": "1404/11/21",
     *           "time": "09:54"
     *         }
     *       },
     *       {
     *         "type": "user",
     *         "name": "dastan rab",
     *         "message": "my request's driver is so late",
     *         "date": {
     *           "day": "1404/11/21",
     *           "time": "10:01"
     *         }
     *       }
     *     ],
     *     "badgeCounters": {
     *       "tickets": 0
     *     }
     *   }
     * }
     */
    public function show(Contact $contact)
    {
        Gate::authorize('edit',$contact);
        $user = auth()->user();
        $contact->update(['user_seen_at' => now()]);
        $replies = $contact->contactReplies;
        $data = [
            "id" => $contact->id,
            "refId" => $contact->id,
            "userId" => $contact->user_id,
            "title" => $contact->subject,
            "date" => [
                "day" => verta()->instance($contact->updated_at)->format('Y/m/d'),
                "time" => verta()->instance($contact->updated_at)->format('H:i'),
            ],

        ];
        $data['messages'][] = [
            "type" => "user",
            "name" => $contact->name,
            "message" => $contact->message,
            "date" => [
                "day" => verta()->instance($contact->updated_at)->format('Y/m/d'),
                "time" => verta()->instance($contact->updated_at)->format('H:i'),
            ]
        ];
        foreach ($replies as $reply){
            $data['messages'][] = [
                "type" => $reply->user->getRole('name') == 'user' ? 'user' : 'admin',
                "name" => $reply->user->getRole('name') == 'user' ? $contact->name : 'اپراتور',
                "message" => $reply->message,
                "date" => [
                    "day" => verta()->instance($contact->created_at)->format('Y/m/d'),
                    "time" => verta()->instance($contact->created_at)->format('H:i'),
                ],
            ];
        }
        $data['messages'] = array_reverse($data['messages']);

        $data['badgeCounters']['tickets'] = Contact::where('user_seen_at',null)->where('user_id',$user->id)->count();

        return sendJson('success','', $data);
    }


    /**
     * Reply to a ticket.
     *
     * @group Tickets
     * @authenticated
     * @urlParam contact required The ID of the ticket to reply. Example: 2
     * @bodyParam message string required Reply message. Example: thank you, I received your message
     *
     * @response 200 {
     *   "status": "success",
     *   "message": "پیام شما ارسال شد",
     *   "data": {
     *       "badgeCounters": {
     *           "tickets": 0
     *       }
     *   }
     * }
     * @response 200 {
     *   "status": "error",
     *   "message": "پیام باید بین ۳ تا ۵۰۰۰ کاراکتر باشد",
     *   "data": []
     * }
     */
    public function update(Request $request, Contact $contact)
    {
        Gate::authorize('edit', $contact);
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'message' => 'required|string|min:3|max:5000',
            ],
            [
                'message' => 'پیام باید بین ۳ تا ۵۰۰۰ کاراکتر باشد',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $contact->contactReplies()->create([
            'user_id' => $user->id,
            'message' => $request->message,
        ]);
        $contact->update(['admin_seen_at' => null]);
        $data['badgeCounters']['tickets'] = Contact::where('user_seen_at',null)->where('user_id',$user->id)->count();
        return sendJson('success','پیام شما ارسال شد',$data);
    }
}
