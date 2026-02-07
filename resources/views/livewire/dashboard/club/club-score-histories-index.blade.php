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
                            <strong>  امتیازات </strong>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-12">
                                <livewire:dashboard.club.club-offer-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="paginated-list">
                    <div class="cr-card-body p-0">
                        <div class="row">
                            <div class="col-5 col-lg-5 col-md-5 mx-4">
                                <div>
                                    <strong>بیشترین امتیازات</strong>
                                </div>
                                <div class="table-responsive text-center text-nowrap">
                                    <div wire:loading.class="cr-parent-spinner">
                                        {{spinner()}}
                                    </div>
                                    @if($this->users->count())
                                        <table class="cr-table table" >
                                            <thead>
                                            <tr>
                                                <th>شناسه</th>
                                                <th>کاربر</th>
                                                <th>امتیاز</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($this->users as $item)
                                                <tr wire:key="{{$item->id}}">
                                                    <td>{{$item->id}}</td>
                                                    <td> <a href="{{route('d.users.single',$item->id)}}">{{$item->name.' '.$item->lastname}}</a> </td>
                                                    <td>{{number_format($item->score)}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        @include('livewire.dashboard.layouts.data-not-exists')
                                    @endif

                                </div>
                            </div>
                            <div class="col-6 col-lg-6 col-md-6 mx-1">
                                <div>
                                    <strong>سابقه امتیاز کاربران</strong>
                                </div>
                                <div class="table-responsive text-center text-nowrap">
                                    <div wire:loading.class="cr-parent-spinner">
                                        {{spinner()}}
                                    </div>
                                    @if($this->scores->count())
                                        <table class="cr-table table" >
                                            <thead>
                                            <tr>
                                                <th>شناسه</th>
                                                <th>کاربر</th>
                                                <th>امتیاز</th>
                                                <th>نوع</th>
                                                <th>توضیحات</th>
                                                <th>تاریخ</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($this->scores as $item)
                                                <tr wire:key="{{$item->id}}">
                                                    <td>{{$item->id}}</td>
                                                    <td>@if(isset($item->user_id)) <a href="{{route('d.users.single',$item->user->id)}}">{{$item->user->name.' '.$item->user->lastname}}</a> @else انتصاب داده نشده @endif</td>
                                                    <td>{{number_format($item->score)}}</td>
                                                    <td>@if($item->type == 'granted')اعطا شده@else مصرف شده @endif </td>
                                                    <td>{{$item->detail}}</td>
                                                    <td>{{verta()->instance($item->created_at)->format('Y/m/d')}}</td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        @include('livewire.dashboard.layouts.data-not-exists')
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
