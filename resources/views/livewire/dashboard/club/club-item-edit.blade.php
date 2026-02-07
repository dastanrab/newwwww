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
                            <strong>افزودن آیتم</strong>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="update">
                    <div class="cr-card-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="title">عنوان</label>
                                        <input type="text" id="title" placeholder="" wire:model="title">
                                    </div>
                                </div>
                                <div class="col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="subTitle">زیرعنوان</label>
                                        <input type="text" id="subTitle" placeholder="" wire:model="subTitle">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="score">امتیاز</label>
                                        <input type="text" id="score" placeholder="" wire:model="score">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="status">وضعیت</label>
                                        <select id="status" wire:model="status">
                                            <option value="">انتخاب کنید</option>
                                            <option value="active">فعال</option>
                                            <option value="inActive">غیرفعال</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="category">دسته</label>
                                        <select id="category" wire:model="category">
                                            <option value="">انتخاب کنید</option>
                                            @foreach($categories as $item)
                                                <option value="{{$item->id}}">{{$item->title}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="category">کاربر</label>
                                        <select id="category" wire:model="userId">
                                            <option value="">انتخاب کنید</option>
                                            @foreach($marketer as $user)
                                                <option value="{{$user->id}}">{{$user->name.' '.$user->lastname}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="cr-text cr-icon cr-md mb-3" wire:ignore>
                                        <trix-editor wire:model="content"></trix-editor>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="image">تصویر</label>
                                        <input type="file" id="image" wire:model="image">
                                        <img src="{{asset($club->image)}}" alt="" width="150">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="brandIcon">تصویر برند</label>
                                        <input type="file" id="brandIcon" wire:model="brandIcon">
                                        <img src="{{asset($club->brand_icon)}}" alt="" width="50">
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="cr-card-footer">
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="cr-button">
                                {{button('ویرایش')}}
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

