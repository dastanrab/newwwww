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
                            <strong>عمومی</strong>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0" id="paginated-list">
                    <div class="table-responsive text-center text-nowrap">
                        <table class="cr-table table">
                            <thead>
                            <tr>
                                <th>توضیحات</th>
                                <th>فیلدها</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>ارسال لینک نرم افزار از طریق پیامک</td>
                                <td>
                                    <div class="cr-text cr-md mb-3">
                                        <input type="text" placeholder="شماره تماس را وارد نمایید" wire:model="mobileSendApp">
                                    </div>
                                </td>
                                <td>
                                    <div class="cr-button wd">
                                        {{button('ارسال پیامک','bx bx-mobile-alt','wire:click="sendApp"')}}
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
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
