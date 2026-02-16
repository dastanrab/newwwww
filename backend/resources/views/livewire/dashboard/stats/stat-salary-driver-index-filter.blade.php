<div class="cr-filters">
    <div class="cr-date">
        <label> تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="dateFrom" id="dateFrom" value="{{$dateFrom}}" autocomplete="off">
    </div>
{{--    <div class="cr-date">--}}
{{--        <label>تا تاریخ</label>--}}
{{--        <i class='bx bxs-calendar'></i>--}}
{{--        <input type="text" wire:model.live.debounce.500ms="dateTo" id="dateTo" value="{{$dateTo}}" autocomplete="off">--}}
{{--    </div>--}}
    <div class="cr-button">
        <button wire:click="filter()" >اعمال</button>
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
        })
    </script>
    @endscript
</div>
