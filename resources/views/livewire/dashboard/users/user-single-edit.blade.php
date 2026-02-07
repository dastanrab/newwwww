<div class="cr-card">
    <div class="cr-card-header">
        <div class="cr-title">
            <div>
                <strong>{{$user->name}} {{$user->lastname}}</strong>
            </div>
        </div>
    </div>
    <div class="cr-card-body p-0">
        <div class="container-fluid">
            <form wire:submit.prevent="update">
                <div class="row">
                        @if($this->roles)
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-select cr-icon cr-md mb-3">
                                    <label for="role">نقش کاربر</label>
                                    <i class="bx bx-user-circle"></i>
                                    <select name="" id="role" wire:model="roleId">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($this->roles as $role)
                                            <option value="{{$role->id}}" {{$user->getRole('id') == $role->id ? 'selected' : ''}}>{{$role->label}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        @endif
                    <div class="col-lg-3 col-md-6 col-12 {{!in_array($user->getRole('id'),$this->passwordRequiredIds) ? 'cr-hidden' : ''}}" id="passwordContainer">
                        <div class="cr-text cr-icon cr-md mb-3">
                            <label for="password">رمزعبور</label>
                            <i class="bx bx-user-circle"></i>
                            <input type="password" id="password" placeholder="در صورت تغییر رمز این قسمت را پر نمایید" wire:model="password">
                        </div>
                    </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-select cr-icon cr-md mb-3">
                                    <label for="gender">سطح</label>
                                    <i class="bx bx-user-circle"></i>
                                    <select name="" id="gender" wire:model="level">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($this->levels as $level)
                                            <option value="{{$level}}">{{$level}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="cr-select cr-icon cr-md mb-3">
                            <label for="gender">جنسیت</label>
                            <i class="bx bx-user-circle"></i>
                            <select name="" id="gender" wire:model="gender">
                                <option value="">انتخاب کنید</option>
                                @foreach($this->genders as $gender)
                                    <option value="{{$gender->name}}" {{$gender->name == $user->gender ? 'selected' : ''}}>{{$gender->label}}</option>
                                @endforeach
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
                            <input type="text" id="mobile" value="{{$user->mobile}}" readonly>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="cr-select cr-icon cr-md mb-3">
                            <label for="city">شهر</label>
                            <i class="bx bx-user-circle"></i>
                            <select name="" id="city" disabled>
                                <option value="">انتخاب کنید</option>
                                @foreach($this->cities as $city)
                                    <option value="{{$city->title}}" {{isset($user->city) && $city->title == $user->city->title ? 'selected' : ''}}>{{$city->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="cr-select cr-icon cr-md mb-3">
                            <label for="userType">نوع کاربر</label>
                            <i class="bx bx-user-circle"></i>
                            <select id="userType" wire:model="userType">
                                <option value="">انتخاب کنید</option>
                                <option value="0">شهروند</option>
                                <option value="1">صنفی</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 {{$user->legal != '1' ? 'cr-hidden' : ''}}" id="guildTitleAttrs">
                        <div class="cr-text cr-icon cr-md mb-3">
                            <label for="guildTitle">نام صنف</label>
                            <i class="bx bx-user-circle"></i>
                            <input type="text" id="guildTitle" placeholder="عنوان صنف را وارد نمایید" wire:model="guildTitle">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 {{$user->legal != '1' ? 'cr-hidden' : ''}}" id="guildAttrs">
                        <div class="cr-select cr-icon cr-md mb-3">
                            <label for="guildType">نوع صنف</label>
                            <i class="bx bx-user-circle"></i>
                            <select name="" id="guildType" @if($user->legal == '1') wire:model="guildType" @endif>
                                <option value="">انتخاب کنید</option>
                                @foreach($this->guilds as $guild)
                                    <option value="{{$guild->id}}" {{$user->guild_id == $guild->id ? 'selected' : ''}}>{{$guild->title}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="cr-text cr-icon cr-md mb-3">
                            <label for="referral">معرف</label>
                            @if(!$user->referral_code)
                                <i class="bx bx-user-circle"></i>
                                <input type="text" id="referral" placeholder="کد معرف را کامل وارد نمایید" wire:model="referral" >
                            @else
                                <a class="btn btn-secondary" href="{{route('d.users.single',$this->presenter->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="برای دیدن اطلاعات این کاربر کلیک کنید">{{$this->presenter->name.' '.$this->presenter->lastname.' ('.$this->presenter->mobile.')'}}</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-6 col-12">
                        @if(Gate::allows('user_single_edit',App\Models\User::class))
                            <div class="cr-button">
                                <label for=""></label>
                                <button>
                                    <span wire:loading.class="cr-hidden">{{$textButton}}</span>
                                    <i class="bx bx-plus" wire:loading.class="cr-hidden"></i>
                                    <span class="cr-hidden" wire:loading.class.remove="cr-hidden">{{spinner()}}</span>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="cr-card-footer">
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
