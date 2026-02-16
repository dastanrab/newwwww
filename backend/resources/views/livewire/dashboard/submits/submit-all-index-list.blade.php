@php
use App\Models\Submit;use App\Models\SubmitMessage;use Hekmatinasser\Verta\Verta;
$gateChangeDriver = Gate::allows('submit_all_index_list_change_driver', Submit::class);
$gateCancelSubmit = Gate::allows('submit_all_index_list_cancel_submit', Submit::class);
@endphp
<div id="paginated-lis">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->submits->count())
                <table class="cr-table table">
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>وضعیت</th>
                        <th>شهر</th>
                        <th>اطلاعات</th>
                        <th>کاربر</th>
                        <th>نوع</th>
                        <th>شماره تماس</th>
                        @if($gateChangeDriver || $gateCancelSubmit)
                            <th>عملیات</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->submits as $submit)
                        <tr wire:key="{{$submit->id}}">
                            <td>
                                @if($submit->user->isFirstSubmit())
                                    <div class="cr-rating">
                                        <i class="bx bxs-star"></i>
                                    </div>
                                @elseif($submit->status == 3 && Submit::where('user_id', $submit->user_id)->where('status', 3)->count() == 1)
                                    <div class="cr-rating">
                                        <i class="bx bxs-star"></i>
                                    </div>
                                @endif
                                <div>{{$submit->id}}</div>
                            </td>
                            <td>
                                <div class="cr-circle
                                @if (\Carbon\Carbon::parse($submit->end_deadline) < now()->addHour())
                                    {{'cr-red'}}
                                @elseif (\Carbon\Carbon::parse($submit->end_deadline) < now()->addHours(4))
                                    {{'cr-yellow'}}
                                @endif"></div>
                            </td>
                            <td>{{city_name($submit->city_id)}}</td>
                            <td>
                                <div class="cr-info">
                                    <ul>
                                        @if($submit->is_instant)
                                        <li class="text-danger blink">
                                            <i class='bx bx-run'></i>
                                            <span>فوری</span>
                                        </li>
                                        @endif
                                        <li>
                                            <i class="bx bxs-calendar-event"></i>
                                            <span>{{ \Verta::instance($submit->start_deadline)->format('Y/n/j') }}</span>
                                        </li>
                                        <li>
                                            <i class="bx bxs-time"></i>
                                            <span> {{ \Verta::instance($submit->start_deadline)->format('H:i') }} الی {{ \Verta::instance($submit->end_deadline)->format('H:i')}}</span>
                                        </li>
                                        @if($district = xDistrict([$submit->address->lat, $submit->address->lon],$polygons))
                                            <li>
                                                <i class="bx bxs-map-pin"></i>
                                                <span>{{ $district }}</span>
                                            </li>
                                        @else
                                            <li class="blink text-danger">
                                                <i class="bx bxs-map-pin"></i>
                                                <span>خارج از منطقه</span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="cr-address">
                                    <a class="uk-link-reset" href="{{ route('d.address.edit', [$submit->address->id, 'city' => request()->query('city')]) }}"
                                       target="_blank">
                                        {{ $submit->address->address }}
                                    </a>
                                </div>
                                @if($submit->drivers->count())
                                    <div class="cr-driver">
                                        <strong>راننده:</strong><span>
                                            <a href="{{route('d.drivers.single',$submit->drivers->first()->user->id)}}">{{ $submit->drivers->first()->user->name }} {{ $submit->drivers->first()->user->lastname }}</a></span>
                                    </div>
                                @endif
                                @isset($submit->driver)
                                    @if($submit->status == 3)
                                        <div class="cr-weight">
                                            <strong>وزن کل بار:</strong><span> {{ weightFormat($submit->driver->weights) }}</span>
                                        </div>
                                        <div class="cr-weight">
                                            <strong>ساعت جمع آوری :</strong>
                                            <span> {{ Verta::instance($submit->driver->collected_at)->format('H:i') }} </span>
                                            <span> {{ Verta::instance($submit->driver->collected_at)->format('Y/m/d') }} </span>
                                        </div>
                                    @endif
                                @endisset
                            </td>
                            <td>

                                @if ($submit->user)
                                    <a href="{{route('d.users.single',$submit->user->id)}}" class="cr-name">
                                        @if ($submit->submit_phone)
                                            <i class='bx bxs-phone'></i>
                                        @endif
                                        @if($submit->user->level == 2)
                                            {!! levelIcon() !!}
                                        @endif
                                        {{$submit->user->name.' '.$submit->user->lastname}}</a>
                                @endif
                                @if($submit->registrant && $submit->registrant_id != $submit->user_id)
                                    <a href="#" class="cr-name">ثبت کننده: {{$submit->registrant->name.' '.$submit->registrant->lastname}}</a>
                                @endif
                            </td>
                            <td>
                                @if ($submit->user->legal)
                                    <i class="bx bxs-store"></i>
                                    <div>{{$submit->user->guild_title}}</div>
                                @else
                                    <i class="bx bxs-user-circle"></i>
                                @endif
                            </td>
                            <td>
                                {{$submit->user->mobile}}
                            </td>
                            @if($gateChangeDriver || $gateCancelSubmit)
                                <td>
                                    @if(in_array($submit->status,[1,2]))
                                        {{--modal--}}
                                        @if($gateChangeDriver)
                                            <div class="cr-modal">
                                                <div class="modal fade" tabindex="-1" id="Edit_Request-{{$submit->id}}"
                                                     wire:ignore>
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">انتقال درخواست</h5>
                                                                <button type="button" class="cr-close"
                                                                        data-bs-dismiss="modal" aria-label="Close">
                                                                    <i class="bx bx-x"></i>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form
                                                                    wire:submit.prevent="changeDriver('{{$submit->id}}')">
                                                                    <div class="cr-select">
                                                                        <p>
                                                                            <select name="" wire:model="toDriver">
                                                                                <option value="">راننده را انتخاب کنید
                                                                                </option>
                                                                                @foreach($this->drivers as $driver)
                                                                                    <option
                                                                                        value="{{$driver->id}}">{{$driver->name.' '.$driver->lastname}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </p>
                                                                    </div>
                                                                    <div class="cr-select">
                                                                        <p>
                                                                            <select name="" wire:model="isEmergency">
                                                                                <option value="0">درخواست اضطراری نیست</option>
                                                                                <option value="1">درخواست اضطراری است</option>
                                                                            </select>
                                                                        </p>
                                                                    </div>
                                                                    <div class="cr-button">
                                                                        {{button('ثبت')}}
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if($gateCancelSubmit)
                                            <div class="cr-modal">
                                                <div class="modal fade" tabindex="-1"
                                                     id="cancel_request-{{$submit->id}}" wire:ignore>
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">حذف درخواست</h5>
                                                                <button type="button" class="cr-close"
                                                                        data-bs-dismiss="modal" aria-label="Close">
                                                                    <i class="bx bx-x"></i>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="cr-select">
                                                                    <p>
                                                                        <select name="" class="cancel-submit-message">
                                                                            <option value="">پیام را انتخاب کنید
                                                                            </option>
                                                                            @foreach(SubmitMessage::operatorCancelMessages() as $id => $message)
                                                                                <option
                                                                                    value="{{$id}}">{{$message}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </p>
                                                                </div>
                                                                <div class="cr-button cancel-submit-btn">
                                                                    @php
                                                                        $property = "data-submitid='$submit->id'";
                                                                    @endphp
                                                                    {{button('ثبت','','','',$property)}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                    @if($submit->status > 1)
                                        <div class="cr-modal">
                                            <div class="modal fade" tabindex="-1" id="messages-{{$submit->id}}"
                                                 wire:ignore.self>
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">پیام ها</h5>
                                                            <button type="button" class="cr-close"
                                                                    data-bs-dismiss="modal" aria-label="Close">
                                                                <i class="bx bx-x"></i>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            @foreach($submit->messages as $message)
                                                                <div
                                                                    class="alert alert-{{$message->user->getRole('name') == 'driver' ? 'secondary' : 'light'}} text-end"
                                                                    role="alert">
                                                                    <span class="text-primary">{{$message->user->name.' '.$message->user->lastname}} ({{$message->user->getRole('label')}}):</span> {{$message->text}}
                                                                </div>
                                                            @endforeach
                                                            <form wire:submit.prevent="storeMessage('{{$submit->id}}')">
                                                                <div class="cr-select">
                                                                    <p>
                                                                        <select name="" wire:model="messageId">
                                                                            <option value="">متن آماده را انتخاب کنید
                                                                            </option>
                                                                            @foreach($this->messages as $id => $message)
                                                                                <option
                                                                                    value="{{$id}}">{{$message}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </p>
                                                                </div>
                                                                <div class="cr-textarea">
                                                                    <p>
                                                                        <textarea wire:model="text"></textarea>
                                                                    </p>
                                                                </div>
                                                                <div class="cr-button">
                                                                    {{button('ثبت')}}
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="cr-actions">
                                        <ul>
                                            @if($submit->status > 1)
                                                <li data-bs-toggle="modal" data-bs-target="#messages-{{$submit->id}}"
                                                    class="position-relative">
                                                    @php($messageCount = $submit->driver ? $submit->messages->where('user_id','=',$submit->driver->user_id)->where('admin_seen',0)->count() : 0)
                                                    @if($messageCount > 0)
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{$submit->messages->where('user_id','=',$submit->drivers->first()->user_id)->where('admin_seen',0)->count()}}</span>
                                                    @endif
                                                    <a data-bs-toggle="tooltip" title="پیام ها">
                                                        <i class='bx bxs-message-square-dots'></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(in_array($submit->status,[1,2]) && $gateChangeDriver)
                                                <li data-bs-toggle="modal"
                                                    data-bs-target="#Edit_Request-{{$submit->id}}">
                                                    <a data-bs-toggle="tooltip" title="انتقال درخواست">
                                                        <i class="bx bxs-copy-alt"></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if($submit->status != 3 && $gateCancelSubmit)
                                                <li data-bs-toggle="modal"
                                                    data-bs-target="#cancel_request-{{$submit->id}}">
                                                    <a class="cr-remove" data-bs-toggle="tooltip"
                                                       title="لغو درخواست" data-submit="{{$submit->id}}">
                                                        <i class="bx bxs-trash"></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            @endif

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                @include('livewire.dashboard.layouts.data-not-exists')
            @endif
        </div>
    </div>
    <div class="cr-card-footer">
        {{ $this->submits->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    @script
    <script>

        $wire.on('remove-modal', (event) => {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $('.modal').modal('hide');
        });

        $(document).ready(function () {
            $('[data-bs-toggle="tooltip"]').tooltip()
        })

        jQuery(document).ready(function ($){
            let messageId;
            $(document).on('change','.cancel-submit-message',function (e){
                messageId = $(this).val();
            });

            $(document).on('click', '.cancel-submit-btn button', function (e) {

                let submitId = $(this).data('submitid');
                Swal.fire({
                    title: 'لغو درخواست؟',
                    text: 'آیا می خواهید این درخواست را لغو کنید؟',
                    icon: 'error',
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: 'خیر',
                    confirmButtonText: 'بله',
                }).then((result) => {
                    if (result.isConfirmed) {
                        $wire.dispatch('submit-cancel', {submit: submitId, messageId: messageId});
                    }
                });
            });
        })
    </script>
    @endscript
    {{toast($errors)}}
</div>
