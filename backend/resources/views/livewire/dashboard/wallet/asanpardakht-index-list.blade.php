<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->transactions->count())
                <table class="cr-table table">
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>کاربر</th>
                        <th>توضیحات</th>
                        <th>مبلغ</th>
                        <th>کد رهگیری</th>
                        @empty($status)<th>نوع</th>@endempty
                        @if($status == 'sharj')<th>نوع</th>@endif
                        <th>تاریخ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->transactions as $transaction)
                        <tr wire:key="{{$transaction->id}}">
                            <td>{{$transaction->id}}</td>
                            <td>
                                @if($transaction->user)
                                    <a href="{{route('d.users.single',$transaction->user->id)}}" class="cr-name">
                                    @if($transaction->user->level == 2)
                                        {!! levelIcon() !!}
                                    @endif
                                        {{$transaction->user->name || $transaction->user->lastname ? $transaction->user->name.' '.$transaction->user->lastname : '-'}}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{$transaction->details}}</td>
                            <td>{{ number_format($transaction->amount/10) }}تومان </td>
                            <td>{{ $transaction->rrn }}</td>
                            @empty($status)<td>{{$transaction->method}}</td>@endempty
                            @if($status == 'sharj')<td>{{$transaction->status_message}}</td>@endif
                            <td class="dir-ltr">{{ \Verta::instance($transaction->created_at)->format('Y/n/j H:i') }}</td>
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
