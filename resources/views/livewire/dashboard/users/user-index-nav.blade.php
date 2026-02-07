<div>
    <div class="cr-nav">
        <nav>
            <ul class="nav" role="tablist">
                <li>
                    <a href="" class="@if(empty($role)) {{'active'}} @endif" wire:click.prevent="filterRole('')">
                        <span>همه</span>
                    </a>
                </li>
                @foreach($roles as $item)
                    <li>
                        <a href="" class="@if($role == $item->name) {{'active'}} @endif" wire:click.prevent="filterRole('{{$item->name}}')">
                            <span>{{$item->label}}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
    <div class="cr-time">
        <nav>
            <ul class="nav" role="tablist">
                <li>
                    <a href="" class="@if(empty($level)) {{'active'}} @endif" wire:click.prevent="filterLevel('')">
                        <span>همه سطوح</span>
                    </a>
                </li>
                <li>
                    <a href="" class="@if($level == 1) {{'active'}} @endif" wire:click.prevent="filterLevel('1')">
                        <span>سطح یک</span>
                    </a>
                </li>
                <li>
                    <a href="" class="@if($level == 2) {{'active'}} @endif" wire:click.prevent="filterLevel('2')">
                        <span>سطح دو</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>

