@php use Carbon\Carbon; @endphp
<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.submits.submit-all-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>درخواست ها</strong>
                            <div class="cr-stats">
                                فردا <strong>({{number_format($this->daysCount(Carbon::tomorrow()))}}) </strong>
                                @for($i=2;$i<=7;$i++)
                                    {{verta()->addDays($i)->format('l')}}<strong>({{number_format($this->daysCount(Carbon::today()->addDays($i)))}}) </strong>
                                @endfor

                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-lg-5 col-12">
                                <livewire:dashboard.layouts.table-row/>
                            </div>
                            <div class="col-lg-7 col-12">
                                <livewire:dashboard.submits.submit-all-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.submits.submit-all-index-list lazy/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>

</div>
