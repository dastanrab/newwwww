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
                            <strong>افزودن کدتخفیف</strong>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="store">
                    <div class="cr-card-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="title"> عنوان کد تخفیف</label>
                                        <input type="text" id="title" placeholder="" wire:model="title">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="category">باشگاه</label>
                                        <select id="category" wire:model="club">
                                            <option value="">انتخاب کنید</option>
                                            @foreach($items as $item)
                                                <option value="{{$item->id}}">{{$item->title.' - '.$item->score}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-select cr-icon cr-md mb-3">
                                        <label for="category">تعداد ایجاد</label>
                                        <select id="category" wire:model="count">
                                            <option value="">انتخاب کنید</option>
                                                <option value=1>1 کد</option>
                                                <option value=5>5 کد</option>
                                                <option value=10>10 کد</option>
                                                <option value=20>20 کد</option>
                                        </select>
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
    {{toast($errors)}}
</div>

