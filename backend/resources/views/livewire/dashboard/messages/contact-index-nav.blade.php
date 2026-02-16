<div class="cr-nav">
    <nav>
        <ul class="nav" role="tablist">
            <li>
                <a href="" class="@if( $status == 'all') {{'active'}} @endif" wire:click.prevent="filterStatus('all')">
                    <i class='bx bx-border-all'></i>
                    <span>همه</span>
                </a>
            </li>
            <li>
                <a href="" class="@if(empty($status) || $status == 'unread') {{'active'}} @endif" wire:click.prevent="filterStatus('unread')">
                    <i class="bx bx-error-circle"></i>
                    <span>خوانده نشده</span>
                </a>
            </li>
            <li>
                <a href="" class="@if(!empty($status) && $status == 'read') {{'active'}} @endif" wire:click.prevent="filterStatus('read')">
                    <i class="bx bxs-chat"></i>
                    <span>خوانده شده</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
