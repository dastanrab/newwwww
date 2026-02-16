<div class="cr-filters">
    <div class="cr-select" id="">
        <select class="select" id="date" wire:model="date">
            <option value="">ماه جاری</option>
            @foreach($this->dates as $key => $date)
                <option value="{{$key}}">{{$date}}</option>
            @endforeach
        </select>
    </div>
    @script
    <script>
        $(document).ready(function (){
            $(document).on('change','#date',function (){
                $wire.dispatch('date', {date: $(this).val()});
            })
        })
    </script>
    @endscript
</div>
