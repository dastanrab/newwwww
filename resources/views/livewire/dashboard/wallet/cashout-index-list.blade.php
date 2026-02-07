@php use App\Models\Wallet; @endphp
<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->cashouts->count())
                <table class="cr-table table">
                    <thead>
                    <tr>
                        <th>کاربر</th>
                        <th>اطلاعات کارت</th>
                        <th>مبلغ</th>
                        <th>اطلاعات بانک</th>
                        <th>اپراتور</th>
                        <th>تاریخ</th>
                        @if(Gate::allows('cashout_all_single',Wallet::class) || Gate::allows('cashout_all_back_to_wallet',Wallet::class) || Gate::allows('cashout_all_send_to_bank',Wallet::class))
                        <th>عملیات</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->cashouts as $cashout)
                        <tr wire:key="{{$cashout->id}}">
                            <td>
                                @if($cashout->user)
                                    <a href="{{route('d.users.single',$cashout->user->id)}}" class="cr-name">
                                        @if($cashout->user->level == 2)
                                            {!! levelIcon() !!}
                                        @endif
                                        {{$cashout->user->name || $cashout->user->lastname ? $cashout->user->name.' '.$cashout->user->lastname : '-'}}</a>
                                @if(!in_array(auth()->user()->getRoles(0), ['accountants', 'financial_manager']))
                                        <span>{{$cashout->user->mobile}}</span>
                                @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <strong>شماره کارت:</strong> <span>{{$cashout->card_number ?? '-'}}</span>
                                <div class="clearfix"></div>
                                <strong>شماره شبا:</strong> <span>{{ $cashout->shaba_number ?? '-' }}</span>
                                <div class="clearfix"></div>
                                <strong>نام صاحب کارت:</strong> <span>{{$cashout->name ?? '-'}}</span>
                            </td>
                            <td>{{ number_format($cashout->amount) }} تومان</td>
                            <td>
                                <strong>کد رهگیری:</strong> <span>{{$cashout->trace_code ?? '-'}}</span>
                                <div class="clearfix"></div>
                                <strong>بانک عامل:</strong> <span>{{ $cashout->bank ?? '-' }}</span>
                            </td>
                            <td>{{ $cashout->operator ? $cashout->operator->name . ' ' . $cashout->operator->lastname : '-' }}</td>
                            <td class="dir-ltr">{{ \Verta::instance($cashout->updated_at)->format('Y/m/d H:i') }}</td>
                            @if(Gate::allows('cashout_all_single',Wallet::class) || Gate::allows('cashout_all_back_to_wallet',Wallet::class) || Gate::allows('cashout_all_send_to_bank',Wallet::class))
                                <td>
                                    <div class="cr-actions">
                                        <ul>
                                            @if(Gate::allows('cashout_all_single',Wallet::class))
                                                <li class="m-2">
                                                    <a wire:ignore.self href="{{route('d.wallet.cashout.single',$cashout->id)}}" class="cr-edit" data-bs-toggle="tooltip" title="ویرایش"><i class='bx bx-edit' ></i></a>
                                                </li>
                                            @endif
                                            @if(Gate::allows('cashout_all_back_to_wallet',Wallet::class) && ($cashout->trace_code === null || $cashout->trace_code > 0))
                                                <li class="m-2" wire:loading.remove>
                                                    <a class="text-bg-danger" href="#" data-bs-toggle="tooltip" title="برگشت به کیف پول کاربر" id="refund-to-wallet" data-cashout-id="{{$cashout->id}}">
                                                        <i class='bx bxs-wallet'></i>
                                                    </a>
                                                </li>
                                            @endif
                                            @if(Gate::allows('cashout_all_send_to_bank',Wallet::class) && $cashout->trace_code === null)
                                                <li class="m-2" wire:loading.remove>
                                                    <a href="#" class="text-bg-success" data-bs-toggle="tooltip" title="ارسال به بانک" id="send-to-bank" data-cashout-id="{{$cashout->id}}">
                                                        <i class='bx bxs-bank'></i>
                                                    </a>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
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
        {{ $this->cashouts->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>

    @script
    <script>

        jQuery(document).ready(function($) {

            $('[data-bs-toggle="tooltip"]').tooltip()

            $(document).on('click', '#send-to-bank', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'ارسال به بانک',
                    text: 'آیا از ارسال به بانک اطمینان دارید؟',
                    icon: 'error',
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: 'خیر',
                    confirmButtonText: 'بله',
                }).then((result) => {
                    console.log(result)
                    if (result.isConfirmed) {
                        $wire.dispatch('send-to-bank', { cashout: $(this).data('cashout-id') });
                    }
                });
            });

            $(document).on('click', '#refund-to-wallet', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'برگشت به کیف پول',
                    text: 'آیا از برگشت مبلغ به کیف پول کاربر اطمینان دارید؟',
                    icon: 'error',
                    showCloseButton: true,
                    showCancelButton: true,
                    cancelButtonText: 'خیر',
                    confirmButtonText: 'بله',
                }).then((result) => {
                    console.log(result)
                    if (result.isConfirmed) {
                        $wire.dispatch('refund-to-wallet', { cashout: $(this).data('cashout-id') });
                    }
                });
            });

        });
    </script>
    @endscript
    {{toast($errors)}}
</div>
