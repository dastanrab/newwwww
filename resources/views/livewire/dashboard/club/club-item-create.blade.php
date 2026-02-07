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
                <form wire:submit.prevent="store">
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
                                        <label for="site">سایت</label>
                                        <select id="site" wire:model="site">
                                            <option value="">انتخاب کنید</option>
                                            <option value="1">دارد</option>
                                            <option value="2">ندارد</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="discount_type">نوع تخفیف</label>
                                        <select id="discount_type" wire:model="discount_type">
                                            <option value="">انتخاب کنید</option>
                                                <option value="1">درصدی</option>
                                            <option value="2">ریالی</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="discount_value">مقدار تخفیف</label>
                                        <input disabled type="text" id="discount_value" placeholder="نوع تخفیف را انتخاب کنید" wire:model="discount_value">
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
                                <div class="col-lg-12 col-md-12 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3" wire:ignore>
                                        <trix-editor wire:model="content"></trix-editor>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="image">تصویر</label>
                                        <input type="file" id="image" wire:model="image">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="brandIcon">تصویر برند</label>
                                        <input type="file" id="brandIcon" wire:model="brandIcon">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="cr-card-footer">
                        <div class="col-lg-2 col-md-6 col-12">
                            <div class="cr-button">
                                {{button('افزودن')}}
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    <script>
        const selectElement = document.getElementById('discount_type');
        const inputField = document.getElementById('discount_value');

        selectElement.addEventListener('change', () => {
            const selectedValue = selectElement.value;
            if (selectedValue) {
                inputField.disabled = false;
                switch (selectedValue) {
                    case '1':
                        inputField.placeholder = 'درصد تخفیف را وارد کنید';
                        break;
                    case '2':
                        inputField.placeholder = 'مقدار تخفیف را وارد کنید';
                        break;
                    default:
                        inputField.placeholder = 'نوع تخفیف را انتخاب کنید';
                }
            } else {
                inputField.disabled = true;
                inputField.placeholder = 'نوع تخفیف را انتخاب کنید';
            }
        })
    </script>
    {{toast($errors)}}
</div>

