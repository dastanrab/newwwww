<div>

    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            @if(!empty($user_id))
                <livewire:dashboard.wallet.wallet-index-list :$user_id/>
            @else
                <livewire:dashboard.wallet.wallet-index-user-list/>
            @endif
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
