<div class="cr-filters">
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.500ms="search" value="{{$search}}">
    </div>
    @if(isset($status) && $status == 'active')
        <div class="cr-select" id="">
            <select wire:model.live="rollCallStatus">
                <option value="">انتخاب کنید</option>
                <option value="presentToday">حاضر امروز</option>
                <option value="currentPresent">حاضر فعلی</option>
                <option value="absent">غایب</option>
            </select>
        </div>
    @endif
</div>
