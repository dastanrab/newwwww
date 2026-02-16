<div class="cr-nav">
    <nav>
        <ul class="nav" role="tablist">
            <li>
                <a href="" class="@if(empty($category)) {{'active'}} @endif" wire:click.prevent="filterCategory('')">
                    <span>همه</span>
                </a>
            </li>
            @foreach($categories as $item)
                <li>
                    <a href="" class="@if($category == $item->id) {{'active'}} @endif" wire:click.prevent="filterCategory('{{$item->id}}')">
                        <span>{{$item->title}}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
