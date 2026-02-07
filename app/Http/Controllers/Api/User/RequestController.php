<?php

namespace App\Http\Controllers\Api\User;

use App\Classes\RequestSuggestionV2;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\ArchivePhone;
use App\Models\City;
use App\Models\Day;
use App\Models\Fava;
use App\Models\Hour;
use App\Models\Percentage;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use App\Models\ReceiveArchive;
use App\Models\Submit;
use App\Models\SubmitTime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    public function scheduling(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'addressId' => 'required|exists:addresses,id,user_id,'.$user->id,
            ],
            [
                'addressId.required' => 'لطفا آدرس را ارسال نمایید',
                'addressId.exists' => 'آدرسی برای شما با این مشخصات ثبت نشده است.',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }

        $address = Address::find($request->addressId);
        $data = Submit::schedule($user,$address);
        if(isset($data['status']) && $data['status'] == 'error'){
            return sendJson('error',$data['message']);
        }
        $data['immediateText'] = 'جمع‌آوری ۱ الی ۲ ساعته پسماند، بین ساعت ۹ تا ۱۷ هر روز';

        return sendJson('success', '', $data);
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'addressId' => 'required|exists:addresses,id,user_id,'.$user->id,
                'cardId'    => $request->payMethod == 'card' ? 'required|exists:ibans,id,user_id,'.$user->id : 'nullable',
                'payMethod' => 'required|in:bazist,card',
                'scheduling.day' => $request->scheduling == 'immediate' ? 'nullable' : 'required',
                'scheduling.hour' => $request->scheduling == 'immediate' ? 'nullable' : 'required',
            ],
            [
                'addressId.required' => 'لطفا آدرس را ارسال نمایید',
                'addressId.exists' => 'آدرسی برای شما با این مشخصات ثبت نشده است.',
                'cardId' => 'شماره کارت انتخاب نشده است',
                'scheduling.day' => 'لطفا روز جمع آوری را انتخاب کنید',
                'scheduling.hour' => 'لطفا ساعت جمع آوری را انتخاب کنید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        elseif(Submit::where('user_id', $user->id)->where('status', 1)->first()){
            return sendJson('error','شما یک درخواست فعال دارید');
        }
        elseif($request->scheduling == 'immediate' && SubmitTime::find(1)->instant == 0){
            return sendJson('error','درحال حاضر ثبت درخواست فوری غیرفعال می باشد.');
        }
        elseif($request->scheduling == 'immediate' && !Submit::immediateValidate(verta()->format('G'))){
            return sendJson('error','درحال حاضر ثبت درخواست فوری در این بازه غیرفعال می باشد.');
        }
        elseif(isset($request->scheduling['day']) && !Submit::scheduleValidation($request->addressId,$request->scheduling['day'],$request->scheduling['hour'])){
            return sendJson('error','ثبت درخواست در زمان انتخاب شده امکان پذیر نمی باشد.');
        }

        Submit::add($user->id,$user,$request);

        return sendJson('success','درخواست شما با موفقیت ثبت شد', $user->currentSubmit());

    }

    public function single(Submit $submit)
    {
        $user = auth()->user();
        if($submit->user_id != $user->id){
            return sendJson('error', 'چنین درخواستی وجود ندارد');
        }
        $startDeadline = verta()->instance($submit->start_deadline);
        $endDeadline = verta()->instance($submit->end_deadline);
        $driver = $submit->drivers();
        $wasteItems = null;
        $data = [
            'id' => $submit->id,
            'requestDate' => [
                'day' => $startDeadline->format('Y/m/d'),
                'range' => $startDeadline->format('G').' الی '.$endDeadline->format('G'),
            ],
            'collectedDate' => $driver->exists() ? [
                'day' => verta()->instance($driver->first()->collected_at)->format('Y/m/d'),
                'time' => verta()->instance($driver->first()->collected_at)->format('G:i'),
            ] : null,
            'weight' => $driver->exists() ? $driver->first()->weights : 0,
            'amount' => $submit->total_amount,
            'status' => $submit->status(),
            'customer' => [
                'name' => $submit->user->name.' '.$submit->user->lastname,
                'mob'  => $submit->user->mobile,
                'address' => $submit->address->address,
                'location' => [
                    'lat' => $submit->address->lat,
                    'lng' => $submit->address->lon
                ],
            ],
            'driver' => $driver->exists() ? [
                'name' => $driver->first()->car->user->name.' '.$driver->first()->car->user->lastname,
                'plaque' => [
                    'part1' => $driver->first()->car->plaque_1,
                    'part2' => $driver->first()->car->plaque_2,
                    'part3' => $driver->first()->car->plaque_3,
                    'part4' => $driver->first()->car->plaque_4,
                ],
                'mob' => in_array($submit->status,[1,2]) ? $driver->first()->car->user->mobile : '',
                'avatar' => asset('assets/img/avatar/avatar-driver-3.png'),
            ] : null,
            'wasteItems' => $wasteItems,
            'actionButtons' => [
                'cancel' => $submit->status == 1 ? 'لغو': false,
                'review' => $submit->status == 3 && $submit->star == null ? 'ثبت نظر': false,
            ]
        ];
        if($driver->exists() && $driver->first()->receives()->exists()){
            $receives = $driver->first()->receives()->get();
            foreach ($receives as $receive) {
                $wasteItems[] = [
                    'id'     => $receive->id,
                    'title'  => $receive->title,
                    'weight' => weightFormat($receive->weight),
                    'amount' => tomanFormat($receive->price*$receive->weight),
                    'image'  => asset("assets/img/icons/recyclables/{$receive->fava_id}.png")
                ];
            }
            $data['wasteItems'] = $wasteItems;
        }
        return sendJson('success','', $data);
    }

    public function list()
    {
        $user = auth()->user();
        $paginate = 10;
        $submits = $user->submits()->with('drivers')->whereNotIn('status',[1,2])->orderBy('id', 'DESC')->paginate($paginate);
        $data = ['list' => []];
        foreach ($submits as $submit) {
            $startDeadline = verta()->instance($submit->start_deadline);
            $endDeadline = verta()->instance($submit->end_deadline);
            $driver = $submit->drivers();
            $data['list'][] = [
                'id' => $submit->id,
                'requestDate' => [
                    'day' => $startDeadline->format('Y/m/d'),
                    'range' => $startDeadline->format('G').' الی '.$endDeadline->format('G'),
                ],
                'collectedDate' => $driver->exists() ? [
                    'day' => verta()->instance($driver->first()->collected_at)->format('Y/m/d'),
                    'time' => verta()->instance($driver->first()->collected_at)->format('G:i'),
                ] : null,
                'weight' => $driver->exists() ? $driver->first()->weights : 0,
                'amount' => $submit->total_amount,
                'status' => $submit->status(),
                'driver' => $driver->exists() ? [
                    'name' => $driver->first()->car->user->name.' '.$driver->first()->car->user->lastname,
                    'plaque' => in_array($submit->status,[1,2]) ? $driver->first()->car->plaque : '',
                    'mob' => in_array($submit->status,[1,2]) ? $driver->first()->car->user->mobile : '',
                ] : null,
            ];
        }
        $data['limit'] = $paginate;
        return sendJson('success','', $data);
    }

    public function cancel()
    {
        $user = auth()->user();
        $submit = $user->submits->where('status',1)->first();
        if(!$submit){
            return sendJson('error', 'شما درخواست قابل لغو ندارید.');
        }
        $suggest=new RequestSuggestionV2(0);
        $suggest->cancelDriversSubmit($submit->id);
        $submit->status = 5;
        $submit->canceled_at = now()->format('Y-m-d H:i:s');
        $submit->save();
        $archive = ReceiveArchive::where('date', Carbon::parse($submit->start_deadline)->format('Y-m-d'))->where('type', 1)->where('city_id',1)->first();
        if($archive) {
            $archive->update([
                'submit_delete' => $archive->submit_delete + 1,
            ]);
        }
        else{
            ReceiveArchive::create([
                'date' => now()->format('Y-m-d'),
                'submit_delete' => 1,
                'type' => 1,
            ]);
        }

        if ($submit->user->legal) {
            ArchiveLegal::submitDelete($submit);
        } else {
            ArchiveNotLegal::submitDelete($submit);
        }

        if ($submit->submit_phone) {
            ArchivePhone::submitDelete($submit);
        } else {
            ArchiveApp::submitDelete($submit);
        }
        return sendJson('success', 'درخواست شما لغو شد');

    }

    public function review(Request $request, Submit $submit)
    {
        $user = auth()->user();
        if($user->id != $submit->user->id){
            return sendJson('error','امکان ثبت نظر را ندارید');
        }
        $validator = Validator::make(
            $request->all(),
            [
                'comment' => 'required|string',
                'rate'    => 'required|numeric',

                'tipValues' => 'required|array',

                'tipValues.good' => 'required|array',
                'tipValues.bad'  => 'required|array',
            ],
            [
                'comment.required' => 'لطفا نظر خود را وارد کنید.',
                'rate.required'    => 'لطفا امتیاز را انتخاب کنید.',
                'tipValues.good.required' => 'لطفا موارد مثبت را مشخص کنید.',
                'tipValues.bad.required'  => 'لطفا موارد منفی را مشخص کنید.',
            ]
        );

        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }

        $submit->comment = $request->comment;
        $submit->survey = 1;
        $submit->star = $request->rate;
        if($request->tipValues['good']){
            foreach ($request->tipValues['good'] as $good){
                $submit->surveys()->create(['title' => $good]);
            }
        }
        if($request->tipValues['bad']){
            foreach ($request->tipValues['bad'] as $bad){
                $submit->surveys()->create(['title' => $bad]);
            }
        }
        $submit->save();
        return sendJson('success','با تشکر از همکاری شما');
    }
}
