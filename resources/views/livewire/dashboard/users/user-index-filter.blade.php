<div class="cr-filters">
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.500ms="search" value="{{$search}}">
    </div>
    @if(isset($role) && $role == 'user')
        <div class="cr-select" id="">
            <select wire:model.live="isLegal" class="select">
                <option value="">نوع کاربر</option>
                <option value="0">شهروندی</option>
                <option value="1">صنفی</option>
            </select>
        </div>
    @endif
</div>
