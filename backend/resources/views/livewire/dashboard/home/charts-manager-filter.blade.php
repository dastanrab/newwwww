
<div class="cr-filters " style="justify-content:flex-start">
    <div class="cr-select">
        <label>نمودار بخش</label>
        <select wire:model.live="type">
            <option value=0 @if(!isset($this->type)) selected @endif>-</option>
            <option value=1 @if(isset($this->type) and $this->type == 1) selected @endif> درخواست ها</option>
            <option value=2 @if(isset($this->type) and $this->type == 2) selected @endif> تناژ</option>
            <option value=3 @if(isset($this->type) and $this->type == 3) selected @endif> مناطق</option>
            <option value=4 @if(isset($this->type) and $this->type == 4) selected @endif> کنسلی ها</option>
        </select>
    </div>
    <div class="cr-select">
        <label>بازه زمانی</label>
        <select wire:model.live="date">
            <option value=0 @if(!isset($this->date)) selected @endif>-</option>
            <option value=1 @if(isset($this->date) and $this->type == 1) selected @endif> امروز</option>
            <option value=2 @if(isset($this->date) and $this->type == 2) selected @endif>ماهانه</option>
        </select>
    </div>
    <div class="cr-date">
        <label> تاریخ</label>
        <i class='bx bxs-calendar'></i>
        <input type="text" wire:model.live.debounce.500ms="dateFrom" id="dateFrom" value="{{$dateFrom}}" autocomplete="off">
    </div>
    <button id="downloadChart" class="btn btn-success">دانلود نمودار</button>
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
</div>



