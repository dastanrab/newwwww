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
                            <strong>اطلاعات واریزی</strong>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="save">
                <div class="cr-card-body p-0">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="cr-text cr-icon cr-md mb-3">
                                    <label for="cardNumber">شماره کارت</label>
                                    <i class='bx bx-credit-card' ></i>
                                    <input type="text" id="cardNumber" placeholder="شماره کارت را وارد نمایید" wire:model="cardNumber">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-text cr-icon cr-md mb-3">
                                    <label for="shabaNumber">شماره شبا</label>
                                    <i class='bx bxs-credit-card' ></i>
                                    <input type="text" id="shabaNumber" placeholder="شماره شبا را بدون IR وارد نمایید" wire:model="shabaNumber">
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-12">
                                <div class="cr-text cr-icon cr-md mb-3">
                                    <label for="name">نام صاحب کارت</label>
                                    <i class='bx bxs-id-card' ></i>
                                    <input type="text" id="name" placeholder="نام صاحب کارت را وارد نمایید"  wire:model="name">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="cr-text cr-icon cr-md mb-3">
                                    <label for="bank">بانک عامل</label>
                                    <i class='bx bxs-bank' ></i>
                                    <input type="text" id="bank" placeholder="بانک عامل را وارد نمایید" wire:model="bank">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="cr-text cr-icon cr-md mb-3">
                                    <label for="traceCode">کد رهگیری</label>
                                    <i class='bx bx-barcode' ></i>
                                    <input type="text" id="traceCode" placeholder="کد رهگیری را وارد نمایید" wire:model="traceCode">
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6 col-12">
                                <div class="cr-select cr-icon cr-md mb-3">
                                    <label for="traceCode">وضعیت</label>
                                    <i class='bx bx-barcode' ></i>
                                    <select name="" id="" wire:model="status">
                                        <option value="waiting">درانتظار واریز</option>
                                        <option value="depositing">درحال واریز</option>
                                        <option value="deposited">واریز شده</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cr-card-footer">
                    <div class="col-lg-2 col-md-6 col-12">
                        <div class="cr-button">
                            <label for=""></label>
                            {{button('ذخیره','bx bx-edit')}}
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
