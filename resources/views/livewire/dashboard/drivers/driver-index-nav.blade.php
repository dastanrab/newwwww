<div class="cr-nav">
    <nav>
        <ul class="nav" role="tablist">
            <li>
                <a href="" class="@if( $status == 'all') {{'active'}} @endif" wire:click.prevent="filterStatus('all')">
                    <i class='bx bx-grid-small'></i>
                    <span>همه <small>({{$activeCount+$deActiveCount}})</small></span>
                </a>
            </li>
            <li>
                <a href="" class="@if(empty($status) || $status == 'active') {{'active'}} @endif" wire:click.prevent="filterStatus('active')">
                    <i class="bx bxs-message-square-check"></i>
                    <span>فعال <small>({{$activeCount}})</small></span>
                </a>
            </li>
            <li>
                <a href="" class="@if(!empty($status) && $status == 'deactive') {{'active'}} @endif" wire:click.prevent="filterStatus('deactive')">
                    <i class="bx bxs-message-square-x"></i>
                    <span>غیر فعال <small>({{$deActiveCount}})</small></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
