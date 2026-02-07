<form action="">
    <div class="cr-filter-section">
        <div class="cr-filters">
            <div class="cr-select md" >
                <select wire:model.live="driver"  id="driver" name="driver">
                    <option value="">انتخاب راننده</option>
                    @foreach($this->drivers as $driver)
                        <option @if(isset($this->driver) and $driver->id == $this->driver) selected @endif value="{{$driver->id}}">{{$driver->name.' '.$driver->lastname}}</option>
                    @endforeach
                </select>
            </div>
            <div class="cr-date">
                <label>تاریخ</label>
                <i class="bx bx-calendar-event"></i>
                <input type="text" autocomplete="off" id="date" name="date" value="{{$date}}">
            </div>
            <div class="cr-button wd">
                <button>جستجو</button>
            </div>
        </div>
    </div>
</form>
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
