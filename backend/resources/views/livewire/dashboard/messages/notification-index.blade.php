@php use Hekmatinasser\Verta\Verta; @endphp
<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>لیست پیام ها</strong>
                        </div>
                        @if(Gate::allows('notification_create',App\Models\Message::class))
                            <div class="cr-actions">
                                <a href="{{route('d.notifications.create')}}" class="cr-action cr-primary">
                                    <span>افزودن پیام</span>
                                    <i class="bx bx-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="cr-card-body p-0" id="paginated-list">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        <table class="cr-table table">
                            <thead>
                            <tr>
                                <th>عنوان</th>
                                <th>ارسال توسط</th>
                                <th>متن پیام</th>
                                <th>تاریخ ارسال</th>
                            </tr>
                            </thead>
                            @if($this->notifications->count())
                                <tbody>
                                @foreach($this->notifications as $notification)
                                    <tr>
                                        <td>{{$notification->title}}</td>
                                        <td>{{$notification->user->name.' '.$notification->user->lastname}}</td>
                                        <td><div class="cr-message">{{$notification->text}}</div></td>
                                        <td>{{\Verta::instance($notification->created_at)->format('Y/m/d H:i')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
                <div class="cr-card-footer">
                    {{$this->notifications->links(data: ['scrollTo' => '#paginated-list'])}}
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>

    {{toast($errors)}}
</div>
