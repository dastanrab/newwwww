@php use App\Models\Warehouse; @endphp
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            <table class="cr-table table" >
                <thead>
                <tr>
                    <th>نام انبار</th>
                    <th>جمع کل</th>
                    @foreach($this->recyclables as $recyclable)
                        <th>{{$recyclable}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach(Warehouse::titles() as $i => $item)
                    <tr>
                        <td>{{$item->title}}</td>
                        <td>
                            {{$this->warehouses['w1'][$i] +  $this->warehouses['w2'][$i] + $this->warehouses['w3'][$i] + $this->warehouses['w4'][$i] + $this->warehouses['w5'][$i] + $this->warehouses['w6'][$i] + $this->warehouses['w7'][$i] + $this->warehouses['w8'][$i] + $this->warehouses['w9'][$i] + $this->warehouses['w10'][$i] + $this->warehouses['w11'][$i] + $this->warehouses['w12'][$i] + $this->warehouses['w13'][$i] + $this->warehouses['w14'][$i] + $this->warehouses['w15'][$i] + $this->warehouses['w16'][$i] + $this->warehouses['w17'][$i] + $this->warehouses['w18'][$i] + $this->warehouses['w19'][$i] + $this->warehouses['w20'][$i] + $this->warehouses['w21'][$i] + $this->warehouses['w22'][$i]}}
                        </td>
                        <td>{{ $this->warehouses['w1'][$i] }}</td>
                        <td>{{ $this->warehouses['w2'][$i] }}</td>
                        <td>{{ $this->warehouses['w3'][$i] }}</td>
                        <td>{{ $this->warehouses['w4'][$i] }}</td>
                        <td>{{ $this->warehouses['w5'][$i] }}</td>
                        <td>{{ $this->warehouses['w6'][$i] }}</td>
                        <td>{{ $this->warehouses['w7'][$i] }}</td>
                        <td>{{ $this->warehouses['w8'][$i] }}</td>
                        <td>{{ $this->warehouses['w9'][$i] }}</td>
                        <td>{{ $this->warehouses['w10'][$i] }}</td>
                        <td>{{ $this->warehouses['w11'][$i] }}</td>
                        <td>{{ $this->warehouses['w12'][$i] }}</td>
                        <td>{{ $this->warehouses['w13'][$i] }}</td>
                        <td>{{ $this->warehouses['w14'][$i] }}</td>
                        <td>{{ $this->warehouses['w15'][$i] }}</td>
                        <td>{{ $this->warehouses['w16'][$i] }}</td>
                        <td>{{ $this->warehouses['w17'][$i] }}</td>
                        <td>{{ $this->warehouses['w18'][$i] }}</td>
                        <td>{{ $this->warehouses['w19'][$i] }}</td>
                        <td>{{ $this->warehouses['w20'][$i] }}</td>
                        <td>{{ $this->warehouses['w21'][$i] }}</td>
                        <td>{{ $this->warehouses['w22'][$i] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
    </div>
    <div class="cr-card-footer">
    </div>
    @script
    <script>
    </script>
    @endscript
</div>
