<div>

    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <livewire:dashboard.club.club-item-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>لیست آیتم ها</strong>
                        </div>
                        @if(Gate::allows('club_create',App\Models\Club::class))
                            <div class="cr-actions">
                                <a class="cr-action cr-primary" href="{{route('d.club.create')}}">
                                    <span> افزودن آیتم</span>
                                    <i class="bx bx-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-12">
                                <livewire:dashboard.club.club-item-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.club.club-item-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
