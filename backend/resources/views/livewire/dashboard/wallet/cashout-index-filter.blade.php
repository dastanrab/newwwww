<div class="cr-filters">
    <div class="cr-date">
        <label> تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="date" id="date" value="{{$date}}"  autocomplete="off">
    </div>
    <div class="cr-search">
        <label>جستجو</label>
        <button><i class="bx bx-search"></i></button>
        <input type="text" wire:model.live.debounce.500ms="search" value="{{$search}}">
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
        })
    </script>
    @endscript
</div>
