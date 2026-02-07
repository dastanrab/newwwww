@php
    use App\Models\Submit;use Hekmatinasser\Verta\Verta;
 @endphp
<div>
    @foreach($submits as $i => $submit)
        @php
            $firstSubmit = Submit::where('user_id', $submit->user_id)->where('status', 3)->count();
        @endphp
        <!-- Modal -->
        <div class="modal fade modal-lg" id="modal-{{$name}}-{{$i}}" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true" wire:ignore>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            درخواست {{$submit->is_instant ? 'فوری' : ''}} {{$submit->user->name.' '.$submit->user->lastname}}
                            #{{$submit->id}}</h5>
                        <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="updateDriver('{{$submit->id}}')">
                        <div class="modal-body">
                            <div class="table-responsive text-center text-nowrap">
                                <div class="row">
                                    <div class="col-2 text-end"></div>
                                    <div class="col-10">
                                        @if($firstSubmit == 1)
                                            <div class="cr-rating">
                                                <i class="bx bxs-star"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-2 text-end"><i class='bx bxs-user'></i>نام</div>
                                    <div class="col-10">

                                        <a href="{{route('d.users.single',$submit->user->id)}}"
                                           target="_blank">{{$submit->user->name.' '.$submit->user->lastname}}</a>
                                        {{$submit->user->legal == 1 ? '(صنفی)':'(خانگی)'}}
                                    </div>
                                    @if(auth()->user()->getRoles(0) != 'financial_manager')
                                        <div class="col-2 text-end"><i class='bx bx-mobile'></i>شماره همراه</div>
                                        <div class="col-10">{{$submit->user->mobile}}</div>
                                    @endif
                                    <div class="col-2 text-end"><i class='bx bx-message-square-dots'></i>نوع درخواست
                                    </div>
                                    <div class="col-10">{{$submit->submit_phone ? 'تلفنی' : 'اپلیکیشن'}}</div>
                                    <div class="col-2 text-end"><i class='bx bxs-timer'></i>بازه درخواست</div>
                                    <div class="col-10">
                                        {{ \Verta::instance($submit->start_deadline)->format($submit->is_instant ? 'H:i' : 'g')}}
                                        تا
                                        {{ \Verta::instance($submit->end_deadline)->format($submit->is_instant ? 'H:i' : 'g') }}
                                    </div>
                                    <div class="col-2 text-end"><i class='bx bx-time-five'></i>زمان درخواست</div>
                                    <div
                                        class="col-10 dir-ltr">{{ \Verta::instance($submit->created_at)->format('Y/m/d H:i') }}</div>
                                    <div class="col-2 text-end"><i class='bx bx-current-location'></i>منطقه</div>
                                    <div
                                        class="col-10">{{ xDistrict([$submit->address->lat, $submit->address->lon]) }}</div>
                                    <div class="col-2 text-end"><i class='bx bx-location-plus'></i>آدرس</div>
                                    <div class="col-10">{{ $submit->address->address }}</div>
                                    @if(in_array($name,['active', 'done']) && $submit->drivers->first())
                                        <div class="col-2 text-end"><i class='bx bxs-car'></i>راننده</div>
                                        <div class="col-10">
                                            <a href="{{route('d.drivers.single',$submit->drivers->first()->user->id)}}"
                                               target="_blank">{{ $submit->drivers->first()->user->name.' '.$submit->drivers->first()->user->lastname }}</a>
                                        </div>
                                    @endif
                                    @if($submit->status == 3 && $submit->driver)
                                        <div class="col-2 text-end"><i class='bx bx-circle-three-quarter'></i>وزن کل
                                        </div>
                                        <div class="col-10">{{weightFormat($submit->driver->weights)}}</div>
                                        <div class="col-2 text-end"><i class='bx bx-time-five'></i>تاریخ جمع آوری</div>
                                        <div class="col-10">
                                            <span>{{Verta::instance($submit->driver->collected_at)->format('H:i')}} </span>
                                            <span>{{Verta::instance($submit->driver->collected_at)->format('Y/m/d')}}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            @if(!in_array($name,['active', 'done']))
                                <div class="cr-select">
                                    <select wire:model="driverId">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($this->drivers as $driver)
                                            <option
                                                value="{{$driver->id}}">{{$driver->name.' '.$driver->lastname}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="cr-button">
                                    {{button('انتقال درخواست')}}
                                </div>
                            @elseif($submit->drivers->first())
                                <a class="btn btn-success"
                                   href="{{route('d.track')."?driverId=".$submit->drivers->first()->id."&userId=".$submit->drivers->first()->user->id}}"
                                   target="_blank"><i class='bx bx-current-location'></i> ردیابی راننده</a>
                            @endif
                            @if($firstSubmit == 1)
                                <button wire:click.prevent="removeFirstSubmit('{{$submit->id}}')" class="btn btn-primary" wire:ignore.self wire:loading.attr="disabled"><span wire:loading.class="cr-hidden">تماس گرفته شد</span>
                                    <i class="bx bxs-phone-call" wire:loading.class="cr-hidden"></i>
                                    <span class="cr-hidden" wire:loading.class.remove="cr-hidden"><div class="cr-spinner"><div class="spinner-border spinner-border-sm" role="status"></div></div></span>
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
    {{toast($errors)}}
</div>
