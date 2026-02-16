<div class="cr-nav">
    <nav>
        <ul class="nav" role="tablist">
            <li>
                <a href="" class="@if(empty($status) || $status == 'present') {{'active'}} @endif" wire:click.prevent="filterStatus('present')">
                    <i class="bx bxs-message-square-check"></i>
                    <span>حاضر </span>
                </a>
            </li>
            <li>
                <a href="" class="@if(!empty($status) && $status == 'absent') {{'active'}} @endif" wire:click.prevent="filterStatus('absent')">
                    <i class="bx bxs-message-square-x"></i>
                    <span>غائب</span>
                </a>
            </li>
        </ul>
    </nav>
</div>
