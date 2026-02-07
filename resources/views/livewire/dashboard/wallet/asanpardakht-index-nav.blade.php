<div class="cr-nav">
    <nav>
        <ul class="nav" role="tablist">
            <li>
                <a href="" class="@if(empty($status)) {{'active'}} @endif" wire:click.prevent="filterStatus('')">
                    <span>همه</span>
                </a>
            </li>
            <li>
                <a href="" class="@if($status == 'deposit') {{'active'}} @endif" wire:click.prevent="filterStatus('deposit')">
                    <span>واریز</span>
                </a>
            </li>
            <li>
                <a href="" class="@if($status == 'withdraw') {{'active'}} @endif" wire:click.prevent="filterStatus('withdraw')">
                    <span>برداشت</span>
                </a>
            </li>
            <li>
                <a href="" class="@if($status == 'sharj') {{'active'}} @endif" wire:click.prevent="filterStatus('sharj')">
                    <span>واریزی مخزن</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
