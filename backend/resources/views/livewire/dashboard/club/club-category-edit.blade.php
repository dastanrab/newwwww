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
                            <strong>ویرایش دسته بندی</strong>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="update">
                    <div class="cr-card-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="title">عنوان</label>
                                        <input type="text" id="title" placeholder="" wire:model="title">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-icon cr-md mb-3">
                                        <label for="icon">آیکون</label>
                                        <input type="file" id="icon" wire:model="icon">
                                        <img src="{{asset($clubCategory->icon)}}" alt="" width="50">
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
