<div class="cr-nav">
    <nav>
        <ul class="nav" role="tablist">
            <li>
                <a href="" class="@if(empty($status) || $status == 'notDeposited') {{'active'}} @endif" wire:click.prevent="filterStatus('notDeposited')">
                    <span>واریز نشده ({{number_format($notDeposited)}})</span>
                </a>
            </li>
            <li>
                <a href="" class="@if($status == 'waitingDeposit') {{'active'}} @endif" wire:click.prevent="filterStatus('waitingDeposit')">
                    <span>درانتظار واریز ({{number_format($waitingDeposit)}})</span>
                </a>
            </li>
            <li>
                <a href="" class="@if($status == 'deposited') {{'active'}} @endif" wire:click.prevent="filterStatus('deposited')">
                    <span>واریز شده ({{number_format($deposited)}})</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
