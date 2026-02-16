<div class="cr-filters">
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.1000ms="search" value="{{$search}}">
    </div>
</div>
