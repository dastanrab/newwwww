<div>
    <div class="cr-card-header">
        <div class="cr-title">
            <div>
                <strong>انتخاب راننده</strong>
            </div>
        </div>
    </div>
    <div class="cr-card-body p-0">
        <div class="container-fluid">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="cr-text cr-icon cr-md mb-3">
                            <label for="driver">نام و یا شماره همراه راننده را وارد نمایید</label>
                            <i class='bx bx-file-find'></i>
                            <input type="text" id="driver"  wire:model.live.debounce.500ms="search">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="cr-card-body p-0">
            <div class="table-responsive text-center text-nowrap">
                <div wire:loading.class="cr-parent-spinner">
                    {{spinner()}}
                </div>
                @if($this->drivers->count())
                    <table class="cr-table table">
                        <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام و نام خانوادگی</th>
                            <th>شماره همراه</th>
                            <th>کد معرف</th>
                            <th>کد معرفی کننده</th>
                            <th>گروه کاربری</th>
                            <th>انتخاب</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->drivers as $driver)
                            <tr wire:key="{{$driver->id}}">
                                <td>{{$driver->id}}</td>
                                <td>{{$driver->name || $driver->lastname ? $driver->name.' '.$driver->lastname : '-'}}</td>
                                <td>{{$driver->mobile}}</td>
                                <td>{{$driver->referral()}}</td>
                                <td>{{$driver->referral_code ?? '-'}}</td>
                                <td>{{$driver->getRoleName()}} - {{$driver->getLegalType()}}</td>
                                <td><a class="btn btn-success select-driver" wire:click="selectDriver('{{$driver->id}}')" data-user-id="{{$driver->id}}"><i class='bx bx-select-multiple' ></i></a></td>
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
    <div class="cr-card-footer">
    </div>
</div>
