<div class="cr-filters">
    <div class="cr-select" id="">
        <select wire:model.live="driverId" class="select" id="driverId">
            <option value="">انتخاب راننده</option>
            @foreach($this->drivers as $driver)
                <option value="{{$driver->id}}">{{$driver->name.' '.$driver->lastname}}</option>
            @endforeach
        </select>
    </div>
    <div class="cr-select" id="">
        <select wire:model.live="type" class="select" id="type">
            <option value="">انتخاب نوع</option>
            <option value="citizen">شهروندی</option>
            <option value="guild">صنفی</option>
        </select>
    </div>
    <div class="cr-select" id="">
        <select wire:model.live="status" class="select" id="status">
            <option value="">انتخاب وضعیت</option>
            <option value="pending">در انتظار</option>
            <option value="AssignToCar">در انتظار جمع آوری</option>
            <option value="collected">جمع آوری شده</option>
            <option value="cancelByUser">لغو توسط کاربر</option>
            <option value="cancelByOperator">لغو توسط اوپراتور</option>

        </select>
    </div>
    <div class="cr-date">
        <label>از تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="dateFrom" id="dateFrom" value="{{$dateFrom}}" autocomplete="off">
    </div>
    <div class="cr-date">
        <label>تا تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="dateTo" id="dateTo" value="{{$dateTo}}" autocomplete="off">
    </div>
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.500ms="search" value="{{$search}}">
    </div>
    @script
    <script>
        $(document).ready(function (){

            $('#dateFrom').persianDatepicker({
                calendar: {
                    persian: {
                        leapYearMode: 'astronomical'
                    },},
                defaultValue: '',
                observer: true,
                initialValueType: 'persian',
                format: 'YYYY/MM/DD',
                initialValue: false,
                autoClose: true,
                onSelect: function(unix){
                    @this.set('dateFrom', new persianDate(unix).format('YYYY/MM/DD'),true)
                }
            });
            $('#dateTo').persianDatepicker({
                calendar: {
                    persian: {
                        leapYearMode: 'astronomical'
                    },},
                defaultValue: '',
                observer: true,
                initialValueType: 'persian',
                format: 'YYYY/MM/DD',
                initialValue: false,
                autoClose: true,
                onSelect: function(unix){
                    @this.set('dateTo', new persianDate(unix).format('YYYY/MM/DD'),true)
                }
            });
            $(document).on('change','#driverId',function (){
                $wire.dispatch('driverId', {driverId: $(this).val()});
            })
            $(document).on('change','#type',function (){
                $wire.dispatch('type', {type: $(this).val()});
            })
            $(document).on('change','#status',function (){
                $wire.dispatch('status', {status: $(this).val()});
            })
        })
    </script>
    @endscript
</div>
