<div class="cr-filters">
    <div class="cr-date">
        <label> تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="dateFrom" id="dateFrom" value="{{$dateFrom}}" autocomplete="off">
    </div>
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

    })
</script>
@endscript
