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
                                    <input type="text" id="mobile" value="{{$driver->mobile}}" readonly>
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
                            @if($driver->referral_code)
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-text cr-icon cr-md mb-3">
                                    <label for="referral">معرف</label>
                                    @if($this->presenter)
                                        <a class="btn btn-secondary" href="{{route('d.users.single',$this->presenter->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="برای دیدن اطلاعات این کاربر کلیک کنید">{{$this->presenter->name.' '.$this->presenter->lastname.' ('.$this->presenter->mobile.')'}}</a>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="cr-card-footer">
                    <div class="col-lg-2 col-md-6 col-12">
                        <div class="cr-button">
                            <label for=""></label>
                            <button wire:click="redirectTo('d.users.single','{{$driver->id}}')">
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
                <form wire:submit.prevent="update">
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
                                @if(Gate::allows('user_driver_single_edit',App\Models\User::class))
                                    <button>
                                        <span>ویرایش خودرو</span>
                                        <i class='bx bx-plus' ></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>
