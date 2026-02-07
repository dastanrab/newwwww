
<div class="cr-card">
        <div id="paginated-list">
            <div class="cr-card-body p-0">
                <div class="table-responsive text-center text-nowrap">
                    <div wire:loading.class="cr-parent-spinner">
                        {{spinner()}}
                    </div>
                    @if($this->transactions->count())
                        <table class="cr-table table" >
                            <thead>
                            <tr>
                                <th>شناسه</th>
                                <th>نام و نام خانوادگی</th>
                                <th>مبلغ (تومان)</th>
                                <th>توضیح</th>
                                <th>تاریخ</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->transactions as $transaction)
                                <tr wire:key="{{$transaction->id}}">
                                    <td>{{$transaction->id}}</td>
                                    <td>
                                        {{$transaction->user->name || $transaction->user->lastname ? $transaction->user->name.' '.$transaction->user->lastname : '-'}}
                                    </td>
                                    <td @if($transaction->type == 'deposit')style="color: green" @else style="color: red"  @endif >{{$transaction->amount ? number_format(floor($transaction->amount)/10).' تومان' : '-'}}</td>
                                    <td>{{$transaction->details}}</td>
                                    <td class="dir-ltr">{{verta()->instance($transaction->created_at)->format('Y/m/d H:i')}}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @else
                        @include('livewire.dashboard.layouts.data-not-exists')
                    @endif

                </div>
            </div>
            <div class="cr-card-footer">
                {{ $this->transactions->links(data: ['scrollTo' => '#paginated-list']) }}
            </div>
        </div>
        <div class="clearfix"></div>
        {{toast($errors)}}
</div>

