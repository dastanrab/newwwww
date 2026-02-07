<div class="cr-filters">
    <div class="cr-date">
        <label>تاریخ شروع</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="StartDate" id="start" value="{{$StartDate}}" autocomplete="off">
    </div>
    <div class="cr-date">
        <label>تاریخ پایان</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="EndDate" id="end" value="{{$EndDate}}" autocomplete="off">
    </div>
    @script
    <script>
        $(document).ready(function (){

            $('#start').persianDatepicker({
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
                    @this.set('StartDate', new persianDate(unix).format('YYYY/MM/DD'),true)
                }
            });
            $('#end').persianDatepicker({
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
                @this.set('EndDate', new persianDate(unix).format('YYYY/MM/DD'),true)
                }
            });
        })
    </script>
    @endscript
</div>
