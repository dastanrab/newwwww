<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />


        <div class="cr-container-section">
            <div class="cr-card">
                @if($step == 1 && $userId == null)
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
                @elseif($step == 2 || $userId != null)
                    <div class="cr-card-header">
                        <div class="cr-title">
                            <div>
                                <strong>اطلاعات راننده</strong>
                            </div>
                        </div>
                    </div>
                    <div class="cr-card-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="name">نام</label>
                                        <i class='bx bx-user-circle'></i>
                                        <input type="text" id="name" wire:model="name" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="lastname">نام خانوادگی</label>
                                        <i class='bx bx-user-circle' ></i>
                                        <input type="text" id="lastname"  wire:model="lastname" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="gender">جنسیت</label>
                                        <i class="bx bx-user-circle"></i>
                                        <select name="" id="gender" wire:model="gender" disabled>
                                            <option value="">انتخاب کنید</option>
                                            @foreach($this->genders as $gender)
                                                <option value="{{$gender->name}}">{{$gender->label}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="mobile">شماره همراه</label>
                                        <i class="bx bx-user-circle"></i>
                                        <input type="text" id="mobile" value="{{$mobile}}" readonly>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="city">شهر</label>
                                        <i class="bx bx-user-circle"></i>
                                        <select name="" id="city" disabled wire:model="city">
                                            <option value="">انتخاب کنید</option>
                                            @foreach($this->cities as $item)
                                                <option value="{{$item->title}}">{{$item->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="referral">معرف</label>
                                        @if(!$referralCode)
                                            <i class="bx bx-user-circle"></i>
                                            <input type="text" id="referral" placeholder="شماره همراه معرف را کامل وارد نمایید" wire:model="referral" >
                                        @else
                                            <a class="btn btn-secondary" href="{{route('d.users.single',$this->presenter->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="برای دیدن اطلاعات این کاربر کلیک کنید">{{$this->presenter->name.' '.$this->presenter->lastname.' ('.$this->presenter->mobile.')'}}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cr-card-footer">
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="cr-button">
                                <label for=""></label>
                                <button wire:click="redirectTo('d.users.single','{{$userId}}')">
                                    <span>ورود به قسمت ویرایش</span>
                                    <i class='bx bxs-edit' ></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="cr-card-header">
                        <div class="cr-title">
                            <div>
                                <strong>اطلاعات خودرو</strong>
                            </div>
                        </div>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="cr-card-body p-0">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="cr-select cr-icon cr-md mb-3">
                                            <label for="type">نوع خودرو</label>
                                            <i class="bx bx-user-circle"></i>
                                            <select name="" id="type" wire:model="type">
                                                <option value="">انتخاب کنید</option>
                                                @foreach($this->types as $type)
                                                    <option value="{{$type->name}}">{{$type->label}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-12">
                                        <div class="cr-select cr-icon cr-md mb-3">
                                            <label for="status">وضعیت</label>
                                            <i class="bx bx-user-circle"></i>
                                            <select name="" id="status" wire:model="status">
                                                <option value="">انتخاب کنید</option>
                                                <option value="active">فعال</option>
                                                <option value="deactive">غیرفعال</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-1 col-12">
                                        <div class="cr-text">
                                            <input type="text" class="text-center" placeholder="11" wire:model="plaque4">
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-12">
                                        <div class="cr-text">
                                            <input type="text" class="text-center" placeholder="365" wire:model="plaque3">
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-12">
                                        <div class="cr-select">
                                            <select wire:model="plaque2">
                                                <option value="">انتخاب کنید</option>
                                                @foreach($this->alphabet as $alpha)
                                                    <option value="{{$alpha}}">{{$alpha}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-12">
                                        <div class="cr-text">
                                            <input type="text" class="text-center" placeholder="12" wire:model="plaque1">
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="cr-card-footer">
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="cr-button">
                                    <label for=""></label>
                                    {{button('اختصاص خودرو')}}
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>
