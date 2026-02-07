<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-modal">
            <div class="modal fade" tabindex="-1" id="wallet-create" wire:ignore>
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">ایجاد کیف پول</h5>
                            <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="create">
                                <div class="cr-select mb-2">
                                    <select wire:model="driverId">
                                        <option value="">راننده را انتخاب کنید</option>
                                        @foreach($this->drivers as $driver)
                                            <option value="{{$driver->id}}">{{$driver->name.' '.$driver->lastname.' ('.$driver->mobile.')'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="cr-text">
                                    <input type="text" placeholder="مبلغ به تومان وارد شود" wire:model="amount">
                                </div>
                                <div class="cr-button mt-2">
                                    {{button('ایجاد')}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>کیف پول رانندگان</strong>
                        </div>
                        <div class="cr-actions">
                            <a class="cr-action cr-primary" href="" data-bs-toggle="modal" data-bs-target="#wallet-create">
                                <span>ایجاد کیف پول</span>
                                <i class="bx bx-plus"></i>
                            </a>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-5 col-12">
                                <livewire:dashboard.layouts.table-row/>
                            </div>
                            <div class="col-lg-7 col-12">
                                <livewire:dashboard.wallet.wallet-driver-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.wallet.wallet-driver-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    @if(Gate::allows('wallet_ap_create',App\Models\Wallet::class))
        <livewire:dashboard.wallet.asanpardakht-create/>
    @endif
    {{toast($errors)}}
</div>
