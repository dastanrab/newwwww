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
                            <strong>لیست نظرات</strong>
                        </div>

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
                                <th>ارسال توسط</th>
                                <th>شماره موبایل</th>
                                <th>امتیاز</th>
                                <th>متن پیام</th>
                                <th>راننده</th>
                                <th>تاریخ ارسال</th>
                            </tr>
                            </thead>
                            @if($this->notifications->count())
                                <tbody>
                                @foreach($this->notifications as $notification)
                                    <tr>
                                        <td>{{$notification->user->name .' '.$notification->user->lastname}}</td>
                                        <td>{{$notification->user->mobile}}</td>
                                        <td>{{$notification->star}}</td>
                                        <td><div class="cr-message">{{$notification->comment}}</div></td>
                                        <td>{{@$notification->drivers[0]->user->name .' '.@$notification->drivers[0]->user->lastname}}</td>
                                        <td>{{\Verta::instance($notification->created_at)->format('Y/m/d H:i')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            @endif
                        </table>
                    </div>
                </div>
                <div class="cr-card-footer">
{{--                    {{$this->notifications->links(data: ['scrollTo' => '#paginated-list'])}}--}}
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>

    {{toast($errors)}}
</div>
