<div class="cr-filters">
{{--    <div class="cr-select">--}}
{{--        <label>شهر</label>--}}
{{--        <select wire:model.live.debounce.500ms="city">--}}
{{--            <option value="">شهر را انتخاب کنید</option>--}}
{{--            @foreach ($options as $value => $label)--}}
{{--                <option value="{{ $value }}">{{ $label }}</option>--}}
{{--            @endforeach--}}
{{--        </select>--}}
{{--    </div>--}}
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.500ms="search" value="{{$search}}">
    </div>
    @empty(!$status)
    <div class="cr-select" id="">
        <select wire:model.live="driver" class="select" id="driver">
            <option value="">راننده</option>
            @foreach($this->drivers as $driver)
                <option value="{{$driver->id}}">{{$driver->name.' '.$driver->lastname}}</option>
            @endforeach
        </select>
    </div>
    @endif
    @if($status == 'done')
        <div class="cr-select" id="">
            <select wire:model.live="sort" class="select" id="sort">
                <option value="">آخرین جمع آوری</option>
                <option value="firstCollection">اولین جمع آوری</option>
                <option value="mostWeight">بیشترین وزن</option>
                <option value="lowestWeight">کمترین وزن</option>
            </select>
        </div>
    @endif

    @script
    <script>
            $(document).on('change','#driver',function (){
                $wire.dispatch('driver', {driver: $(this).val()} );
            });
            $(document).on('change','#sort',function (){
                $wire.dispatch('sort', {sort: $(this).val()} );
            });
            $(document).on('change','#city',function (){
                $wire.dispatch('city', {city: $(this).val()} );
            });
    </script>
    @endscript
</div>
