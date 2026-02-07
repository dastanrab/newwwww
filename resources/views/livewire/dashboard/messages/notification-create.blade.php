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
                            <strong>ارسال پیام گروهی</strong>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0">
                    <div class="container-fluid">
                        <form wire:submit.prevent="store">
                            <div class="row">

                                <div class="col-lg-3 col-md-6 col-12">
                                    <div class="cr-text cr-md mb-3">
                                        <label for="title">عنوان</label>
                                        <input type="text" id="name" wire:model="title">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="cr-textarea cr-md mb-3">
                                        <label for="text">متن</label>
                                        <textarea wire:model="text" id="text"></textarea>
                                    </div>
                                </div>

                                <div class="col-lg-2 col-md-6 col-12">
                                    <div class="cr-button">
                                        <label for=""></label>
                                        {{button('ارسال پیام')}}
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
    {{toast($errors)}}
</div>
