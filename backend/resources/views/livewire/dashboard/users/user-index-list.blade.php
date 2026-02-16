@php
$gateWalletAllSingle = Gate::allows('wallet_all_single',App\Models\Wallet::class);
$gateUserSingle = Gate::allows('user_single',App\Models\Wallet::class);
@endphp
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->users->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>نام و نام خانوادگی</th>
                        <th>شماره همراه</th>
                        <th>کد معرف</th>
                        <th>کد معرفی کننده</th>
                        <th>گروه کاربری</th>
                        @if($gateWalletAllSingle)
                        <th>گردش حساب</th>
                        @endif
                        @if($gateUserSingle)
                        <th>ویرایش</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->users as $user)
                        <tr wire:key="{{$user->id}}">
                            <td>{{$user->id}}</td>
                            <td>
                                <a href="{{route('d.users.single',$user->id)}}" class="cr-name">
                                    @if($user->level == 2)
                                        {!! levelIcon() !!}
                                    @endif
                                    {{$user->name || $user->lastname ? $user->name.' '.$user->lastname : '-'}}
                                </a>
                            </td>
                            <td>{{$user->mobile}}</td>
                            <td>{{$user->referral()}}</td>
                            <td>{{$user->referral_code ?? '-'}}</td>
                            <td>{{$user->getRoleName()}} - {{$user->getLegalType()}}</td>
                            @if($gateWalletAllSingle)
                                <td>
                                    <a href="{{route('d.wallet',['user_id' => $user->id])}}" class="cr-edit">
                                        <i class='bx bxs-wallet' ></i>
                                    </a>
                                </td>
                            @endif
                            @if($gateUserSingle)
                                <td>
                                    <a href="{{route('d.users.single',$user->id)}}" class="cr-edit">
                                        <i class="bx bxs-message-square-edit"></i>
                                    </a>
                                </td>
                            @endif
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
        {{ $this->users->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
</div>
