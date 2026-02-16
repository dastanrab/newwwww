<div>
    <div class="cr-page">
        <div class="cr-select">
            <select wire:model.live="row" class="select" id="row">
                @foreach($rows as $item)
                    <option value="{{$item}}">{{$item}}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        $(document).on('change','#row', function (){
            Livewire.dispatch('row', { row: $(this).val() })
        });
    })
</script>
