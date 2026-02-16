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
                            <strong>مناطق آنیروب</strong>
                        </div>
                    </div>
                </div>
                <livewire:dashboard.settings.submit-time-index-list/>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
