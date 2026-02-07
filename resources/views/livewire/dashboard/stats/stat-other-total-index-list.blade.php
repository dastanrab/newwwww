<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->data->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>{{$this->getTypeRowName()}}
                        </th>
                        @foreach($this->column as $item)
                            <th>{{$this->options[$item]}}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->data as $value)
                        <tr wire:key="{{$value->id}}">
                            @foreach($this->rows[$value->id] as $item)
                                <td>{{$item}}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="cr-card-footer">
                    {{ $this->data->links(data: ['scrollTo' => '#paginated-list']) }}
                </div>
            @else
                @include('livewire.dashboard.layouts.data-not-exists')
            @endif
        </div>
    </div>

    @script
    <script>
        $(document).ready(function (){
            $('[data-bs-toggle="tooltip"]').tooltip()
        })
    </script>
    @endscript
</div>
