@php use App\Models\Wallet; @endphp
<div>
    <div class="cr-card">
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>لیست کاربران</strong>
                    <div class="cr-stats">
                        (جمع کل موجودی کیف پول <strong>{{number_format($this->walletSum)}} تومان</strong>)
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="cr-filter-section mt-2">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="cr-text cr-icon cr-md mb-3">
                            <label for="driver">نام و یا شماره همراه کاربر را وارد نمایید</label>
                            <i class="bx bx-file-find"></i>
                            <input type="text" id="driver" wire:model.live.debounce.500ms="search">
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                                @if(in_array(auth()->user()->getRoles(0), ['accountants', 'financial_manager']))
                                    <th>شماره همراه</th>
                                @endif
                                <th>گروه کاربری</th>
                                <th>موجودی</th>
                                @if(Gate::allows('wallet_all_index_withdraw', Wallet::class) || Gate::allows('wallet_all_single', Wallet::class))
                                    <th>عملیات</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($this->users as $user)
                                <tr wire:key="{{$user->id}}">
                                    <td>{{$user->id}}</td>
                                    <td>
                                        @if($user->level == 2)
                                            {!! levelIcon() !!}
                                        @endif
                                        {{$user->name || $user->lastname ? $user->name.' '.$user->lastname : '-'}}
                                    </td>
                                    @if(!in_array(auth()->user()->getRoles(0), ['accountants', 'financial_manager']))
                                        <td>{{ $user->mobile }}</td>
                                    @endif
                                    <td>{{$user->getRoleName()}} - {{$user->getLegalType()}}</td>
                                    @php
                                    $wallet = $user->wallets()->first();
                                    @endphp
                                    <td>{{$wallet ? number_format(floor($wallet->wallet)).' تومان' : 'کیف پول ایجاد نشده'}}</td>
                                    @if(Gate::allows('wallet_all_index_withdraw', Wallet::class) || Gate::allows('wallet_all_single', Wallet::class))
                                        <td>
                                            @if(Gate::allows('wallet_all_index_add_card', Wallet::class))
                                                <livewire:dashboard.wallet.wallet-index-add-card :$user wire:key="{{$user->id}}"/>
                                            @endif
                                            @if(Gate::allows('wallet_all_index_withdraw', Wallet::class))
                                                <div class="cr-modal">
                                                    <div class="modal fade" tabindex="-1" id="send-to-bank-{{$user->id}}" wire:ignore>
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"> ثبت مشخصات پرداخت : {{@$user->name.' '.@$user->lastname}}</h5>
                                                                    <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                                                                        <i class="bx bx-x"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    @if(Gate::allows('wallet_all_index_add_card', Wallet::class))
                                                                        <div class="cr-button mb-2">
                                                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add-iban-{{$user->id}}">ثبت کارت جدید <i class='bx bxs-credit-card'></i></button>
                                                                        </div>
                                                                    @endif
                                                                    <form wire:submit.prevent="withdraw">
                                                                        <div class="cr-select">
                                                                            <p>
                                                                                <select name="{{$user->id}}" id="type" wire:model="type">
                                                                                    <option value="">نوع درخواست را انتخاب کنید</option>
                                                                                    <option value="card">واریز به کارت</option>
                                                                                    @if(Gate::allows('wallet_all_deposit_bazist_wallet', Wallet::class))
                                                                                    <option value="miss_ref">واریز حق معرف</option>
                                                                                    @endif
                                                                                    @if(Gate::allows('wallet_all_deposit_bazist_wallet', Wallet::class))
                                                                                        <option value="deposit">واریز به کیف پول آنیروب</option>
                                                                                    @endif
                                                                                    @if(Gate::allows('wallet_all_withdraw_bazist_wallet', Wallet::class))
                                                                                    <option value="withdraw">برداشت از موجودی کیف پول آنیروب</option>
                                                                                    @endif
                                                                                </select>
                                                                            </p>
                                                                        </div>
                                                                            <div class="cr-text" style="display: none"  id="ref-{{$user->id}}" >
                                                                                <label>کد معرف</label>
                                                                                <input type="text"  wire:model="ref"/>
                                                                            </div>
                                                                            <div class="cr-text" style="display: none"  id="submit_id-{{$user->id}}">
                                                                                <label>شناسه درخواست</label>
                                                                                <input type="text"  wire:model="submit_id" />
                                                                            </div>
                                                                        <div class="cr-textarea" id="desc-div-{{$user->id}}" style="display: none">
                                                                            <textarea wire:model="description" placeholder="توضیحات را وارد نمایید"></textarea>
                                                                        </div>
                                                                        <div class="cr-select" id="iban-div-{{$user->id}}" style="display: none">
                                                                            <p>
                                                                                <select name="" id="ibans-{{$user->id}}" wire:model="ibanId">
                                                                                    <option value="" id="iban-select-{{$user->id}}">کارت را انتخاب کنید</option>
                                                                                    @foreach($user->ibans()->latest()->get() as $iban)
                                                                                        <option value="{{$iban->id}}">{{$iban->card.' | '.$iban->name.' | '.$iban->bank }}</option>
                                                                                    @endforeach
                                                                                </select>
                                                                            </p>
                                                                        </div>
                                                                        <div class="cr-text">
                                                                            <input type="number" placeholder="مبلغ به تومان وارد شود" wire:model="amount">
                                                                        </div>
                                                                        <div class="cr-button mt-2">
                                                                            {{button('ثبت درخواست')}}
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="cr-actions">
                                                <ul>
                                                    @if(Gate::allows('wallet_all_single', Wallet::class))
                                                        <li class="m-2">
                                                            <a href="{{route('d.wallet')}}?user_id={{$user->id}}" class="text-bg-success"  data-bs-toggle="tooltip" title="نمایش جزئیات" id="">
                                                                <i class="bx bx-select-multiple"></i>
                                                            </a>
                                                        </li>
                                                    @endif
                                                    @if(Gate::allows('wallet_all_index_withdraw', Wallet::class))
                                                        <li class="m-2" data-bs-toggle="modal" data-bs-target="#send-to-bank-{{$user->id}}">
                                                            <a href="" class="text-bg-primary" data-bs-toggle="tooltip" title="درخواست واریز" wire:click.prevent="setUserId('{{$user->id}}')">
                                                                <i class='bx bx-wallet'></i>
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
        </div>
    </div>
    <div class="clearfix"></div>
    {{toast($errors)}}

    @script
    <script>
        $wire.on('reload-iban', (res) => {
            console.log(res.data);
            $('<option value="'+res.data.ibanId+'">' + res.data.card + ' | ' + res.data.name + ' | ' + res.data.bank + '</option>').insertAfter('#iban-select-'+ res.data.userId);
        });

        jQuery(document).ready(function ($){
            $(document).on('change','#type', function (){
                let name = $(this).attr("name")
                $('#iban-div-'+name).css('display','none');
                $('#desc-div-'+name).css('display','none');
                $('#ref-'+name).css('display','none');
                $('#submit_id-'+name).css('display','none');
                if($(this).val() == 'card'){
                    $('#iban-div-'+name).css('display','block');
                }
                if($(this).val() == 'withdraw' || $(this).val() == 'deposit'){
                    $('#desc-div-'+name).css('display','block');
                }
                if($(this).val() == 'miss_ref'){
                    $('#ref-'+name).css('display','block');
                    $('#submit_id-'+name).css('display','block');
                }
            });
        });
    </script>
    @endscript
</div>
