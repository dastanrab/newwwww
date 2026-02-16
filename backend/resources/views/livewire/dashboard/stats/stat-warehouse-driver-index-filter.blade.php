<div class="cr-filters">
    <div class="cr-date">
        <label>تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="date" id="date" value="{{$date}}" autocomplete="off">
    </div>
    <div class="cr-date">
        <label>  تا تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="date_to" id="date_to" value="{{$date_to}}" autocomplete="off">
    </div>
    @script
    <script>
        $(document).ready(function (){

            $('#date').persianDatepicker({
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
                    @this.set('date', new persianDate(unix).format('YYYY/MM/DD'),true)
                }
            });
            $('#date_to').persianDatepicker({
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
                @this.set('date_to', new persianDate(unix).format('YYYY/MM/DD'),true)
                }
            });
        })
    </script>
    @endscript
</div>
