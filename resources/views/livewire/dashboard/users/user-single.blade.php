<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />


        <div class="cr-container-section">
            <livewire:dashboard.users.user-single-edit :$user/>
            <livewire:dashboard.users.user-single-requests :$user/>
            <livewire:dashboard.users.user-single-orders :$user/>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
