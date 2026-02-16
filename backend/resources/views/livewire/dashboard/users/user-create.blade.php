<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section" wire:ignore>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>اطلاعات کاربر</strong>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0">
                    <div class="container-fluid">
                        <form wire:submit.prevent="save('{{request()->submit}}')">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="role">نقش کاربر</label>
                                        <i class="bx bx-user-circle"></i>
                                        <select name="" id="role" wire:model="roleId">
                                            <option value="">انتخاب کنید</option>
                                            @foreach($this->roles as $role)
                                                <option value="{{$role->id}}">{{$role->label}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12 cr-hidden" id="passwordContainer">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="password">رمزعبور</label>
                                        <i class="bx bx-user-circle"></i>
                                        <input type="text" id="password" placeholder="رمزعبور را وارد نمایید" wire:model="password">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="gender">جنسیت</label>
                                        <i class="bx bx-user-circle"></i>
                                        <select name="" id="gender" wire:model="gender">
                                            <option value="">انتخاب کنید</option>
                                            <option value="1">آقا</option>
                                            <option value="2">خانم</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="name">نام</label>
                                        <i class="bx bx-user-circle"></i>
                                        <input type="text" id="name" placeholder="نام کاربر را وارد نمایید" wire:model="name">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="lastname">نام خانوادگی</label>
                                        <i class="bx bx-user-circle"></i>
                                        <input type="text" id="lastname" placeholder="نام خانوادگی کاربر را وارد نمایید" wire:model="lastname">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="mobile">شماره همراه</label>
                                        <i class="bx bx-user-circle"></i>
                                        <input type="text" id="mobile" placeholder="شماره همراه کاربر را وارد نمایید"  wire:model="mobile">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="referral">معرف</label>
                                        <i class="bx bx-user-circle"></i>
                                        <input type="text" id="referral" placeholder="کد معرف را وارد نمایید" wire:model="referral">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="city">شهر</label>
                                        <i class="bx bx-user-circle"></i>
                                        <select name="" id="city" wire:model="city">
                                            <option value="">انتخاب کنید</option>
                                            @foreach($this->cities as $city)
                                                <option value="{{$city->id}}">{{$city->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="userType">نوع کاربر</label>
                                        <i class="bx bx-user-circle"></i>
                                        <select name="" id="userType" wire:model="userType">
                                            <option value="">انتخاب کنید</option>
                                            <option value="0">شهروند</option>
                                            <option value="1">صنفی</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12 cr-hidden" id="guildTitleAttrs">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="guildTitle">نام صنف</label>
                                        <i class="bx bx-user-circle"></i>
                                        <input type="text" id="guildTitle" placeholder="عنوان صنف را وارد نمایید" wire:model="guildTitle">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12 cr-hidden" id="guildAttrs">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="guildType">نوع صنف</label>
                                        <i class="bx bx-user-circle"></i>
                                        <select name="" id="guildType" wire:model="guildType" disabled>
                                            <option value="">انتخاب کنید</option>
                                            @foreach($this->guilds as $guild)
                                                <option value="{{$guild->id}}">{{$guild->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-md-6 col-12">
                                    <div class="cr-button">
                                        <label for=""></label>
                                        <button>
                                            <span wire:loading.class="cr-hidden">{{$textButton}}</span>
                                            <i class="bx bx-plus" wire:loading.class="cr-hidden"></i>
                                            <span class="cr-hidden" wire:loading.class.remove="cr-hidden">{{spinner()}}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class=" cr-hidden" id="DriverInfoContainer">
                                <div class="cr-card-header">
                                    <div class="cr-title">
                                        <div>
                                            <strong>اطلاعات خودرو</strong>
                                        </div>
                                    </div>
                                </div>
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
                            </div>
                        </form>
                    </div>
                </div>
                <div class="cr-card-footer">
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @script
    <script>
        let passwordRequiredIds = '{{json_encode($this->passwordRequiredIds)}}';
        $(document).on('change','#role', function (e){
            if($.inArray(e.target.value,passwordRequiredIds) !== -1){
                $('#passwordContainer').removeClass('cr-hidden');
            }
            else{
                $('#passwordContainer').addClass('cr-hidden');
            }
            if (e.target.value == 9)
            {
                $('#DriverInfoContainer').removeClass('cr-hidden');
            }
            else {
                $('#DriverInfoContainer').addClass('cr-hidden');
            }
        });
        $(document).on('change','#userType', function (e){
            if(e.target.value == 1){
                $('#guildAttrs').removeClass('cr-hidden');
                $('#guildAttrs #guildType').prop('disabled',false);
                $('#guildTitleAttrs').removeClass('cr-hidden');
                $('#guildTitleAttrs #guildTitle').prop('disabled',false);
            }
            else{
                $('#guildAttrs').addClass('cr-hidden');
                $('#guildAttrs #guildType').prop('disabled',true);
                $('#guildTitleAttrs').addClass('cr-hidden');
                $('#guildTitleAttrs #guildTitle').prop('disabled',true);
            }
        });

    </script>
    @endscript
    {{toast($errors)}}
</div>
