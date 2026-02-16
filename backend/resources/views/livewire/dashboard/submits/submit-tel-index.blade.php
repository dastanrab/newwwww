<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <div class="cr-card">
                @if($step == 1 && $userId == null)
                    <livewire:dashboard.submits.submit-tel-index-list/>
                @elseif($step == 2 || $userId != null)
                    <livewire:dashboard.submits.submit-tel-index-form :$userId/>
                @endif
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>
