<div class="cr-filters">
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.500ms="search" value="{{$search}}">
    </div>
    <div class="cr-select" id="">
        <select wire:model.live="isLegal" class="select" id="isLegal">
            <option value="">نوع کاربر</option>
            <option value="0">شهروندی</option>
            <option value="1">صنفی</option>
        </select>
    </div>
    @script
    <script>
        $(document).ready(function (){
            $(document).on('change','#isLegal',function (){
                $wire.dispatch('isLegal', {isLegal: $(this).val()});
            })
        })
    </script>
    @endscript
</div>
