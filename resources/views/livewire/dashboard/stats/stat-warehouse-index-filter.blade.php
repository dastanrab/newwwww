<div class="cr-filters">
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
    <div class="cr-select">
        <select name="" id="carId" class="select" wire:model="carId">
            <option value="">اتخاب کنید</option>
            @foreach($this->drivers as $driver)
                <option value="{{$driver->cars->first()->id}}">{{$driver->name.' '.$driver->lastname}}</option>
            @endforeach
        </select>
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
            $(document).on('change','#carId',function (){
                $wire.dispatch('carId', {carId: $(this).val()});
            })
        })
    </script>
    @endscript
</div>
