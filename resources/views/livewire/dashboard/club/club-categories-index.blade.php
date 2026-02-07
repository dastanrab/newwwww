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
                            <strong>دسته بندی</strong>
                        </div>
                        @if(Gate::allows('club_category_create',App\Models\Club::class))
                            <div class="cr-actions">
                                <a class="cr-action cr-primary" href="{{route('d.club.category.create')}}">
                                    <span> افزودن دسته</span>
                                    <i class="bx bx-plus"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                    <div class="cr-filter-section">
                        <div class="row">
                            <div class="col-12">
                                <livewire:dashboard.club.club-category-index-filter/>
                            </div>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.club.club-category-index-list lazy/>
            </div>
            <div class="clearfix"></div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
