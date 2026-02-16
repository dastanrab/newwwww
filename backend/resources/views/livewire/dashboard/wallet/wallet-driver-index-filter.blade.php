<div class="cr-filters">
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.500ms="search" value="{{$search}}">
    </div>
</div>
@script
<script>
    $(document).ready(function (){

    })
</script>
@endscript
