<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <livewire:dashboard.wallet.asanpardakht-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>لیست {{$statusTitle}} آسان پرداخت</strong>
                        </div>
                        @if(Gate::allows('wallet_ap_create',App\Models\Wallet::class))
                            <div class="cr-actions">
                                <a class="cr-action cr-primary" href="" data-bs-toggle="modal" data-bs-target="#ap-create">
                                    <span>ثبت واریزی</span>
                                    <i class="bx bx-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-5 col-12">
                                <livewire:dashboard.layouts.table-row/>
                            </div>
                            <div class="col-lg-7 col-12">
                                <livewire:dashboard.wallet.asanpardakht-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.wallet.asanpardakht-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @if(Gate::allows('wallet_ap_create',App\Models\Wallet::class))
        <livewire:dashboard.wallet.asanpardakht-create/>
    @endif
</div>
