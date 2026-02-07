@php
    use App\Models\Submit;
    $gateAddCard       = Gate::allows('submit_tel_create_add_card', Submit::class);
    $gateAddAddress    = Gate::allows('submit_tel_create_add_address', Submit::class);
    $gateDeleteCard    = Gate::allows('submit_delete_card', Submit::class);
    $gateDeleteAddress = Gate::allows('submit_delete_address', Submit::class);
    @endphp
<div>
    <link rel="stylesheet" href="{{asset('/assets/css/select2.min.css')}}">
    <script src="{{asset('/assets/js/select2.min.js')}}"></script>
    <!-- Modal -->
    @if($gateAddCard)
        <livewire:dashboard.wallet.wallet-index-add-card :$user wire:key="{{$userId}}"/>
    @endif
    @if($gateAddAddress)
        <livewire:dashboard.submits.submit-index-add-address :$user wire:key="{{$userId}}"/>
    @endif
    @if($gateDeleteCard)
        <div class="cr-modal">
            <div class="modal fade" tabindex="-1" id="edit-card-{{$user->id}}" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">کارت ها</h5>
                            <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="">
                                <table class="cr-table table">
                                    <thead>
                                    <tr>
                                        <th>شماره کارت</th>
                                        <th>نام و نام خانوادگی</th>
                                        <th>نام بانک</th>
                                        <th class="text-center">حذف</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($cards as $card)
                                        <tr>
                                            <td>{{$card->card}}</td>
                                            <td>{{$card->name}}</td>
                                            <td>{{$card->bank}}</td>
                                            <td>
                                                <div class="cr-actions">
                                                    <ul>
                                                        <li>
                                                            <a href="" class="card-remove" data-card="{{$card->id}}"><i class='bx bx-trash'></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="cr-modal">
            <div class="modal fade" tabindex="-1" id="edit-address-{{$user->id}}" wire:ignore.self>
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">آدرس ها</h5>
                            <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="bx bx-x"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="">
                                <table class="cr-table table">
                                    <thead>
                                    <tr>
                                        <th>آدرس</th>
                                        <th class="text-center">حذف</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($addresses as $address)
                                        <tr>
                                            <td>{{$address->address}}</td>
                                            <td>
                                                <div class="cr-actions">
                                                    <ul>
                                                        <li>
                                                            <a href="" class="address-remove" data-address="{{$address->id}}"><i class='bx bx-trash'></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endif
    <div class="cr-card-header">
        <div class="cr-title">
            <div>
                <strong>ثبت درخواست تلفنی {{$user->name.' '.$user->lastname}}</strong>
            </div>
            <div class="cr-actions">
                @if($gateAddCard)
                <div class="cr-button">
                    <button data-bs-toggle="modal" data-bs-target="#add-iban-{{$user->id}}">افزودن کارت <i class='bx bx-credit-card'></i></button>
                </div>
                @endif
                @if($gateAddAddress)
                    <div class="cr-button">
                        <button data-bs-toggle="modal" data-bs-target="#add-address-{{$user->id}}" class="add-address">افزودن آدرس <i class='bx bx-location-plus'></i></button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <form wire:submit.prevent="store">
        <div class="cr-card-body p-0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-2 col-md-6 col-12">
                        <div class="cr-select cr-md mb-3">
                            <label for="cashout">نوع واریزی</label>
                            <select id="cashout" wire:model="cashout" class="select">
                                <option value="card">کارت به کارت</option>
                                <option value="bazist">کیف پول بازیست</option>
                                {{--<option value="aap">کیف پول آپ</option>--}}
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="cr-select cr-md mb-3">
                            <div class="row">
                                <div class="col-12 col-md-10 p-0">
                                    <label for="card">شماره کارت</label>
                                    <select id="card" wire:model="card" class="select">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($cards as $card)
                                            <option value="{{$card->id}}">{{$card->card.' - '.$card->name.' - '.$card->bank}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($gateDeleteCard)
                                    <div class="col-12 col-md-2 p-1">
                                        <a class="btn btn-danger mt-4" data-bs-toggle="modal" data-bs-target="#edit-card-{{$user->id}}"><i class='bx bx-trash'></i></a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-12">
                        <div class="cr-select cr-md mb-3">
                            <div class="row">
                                <div class="col-12 col-md-10 p-0" wire:ignore>
                                    <label for="address">آدرس</label>
                                    <select id="address" wire:model="address">
                                        <option value="">انتخاب کنید</option>
                                        @foreach($addresses as $address)
                                            <option value="{{$address->id}}">{{$address->address}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($gateDeleteAddress)
                                    <div class="col-12 col-md-2 p-1">
                                        <a class="btn btn-danger mt-4" data-bs-toggle="modal" data-bs-target="#edit-address-{{$user->id}}"><i class='bx bx-trash'></i></a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>زمان بندی درخواست</strong>
                </div>
            </div>
        </div>
        <div class="cr-card-body p-0" wire:ignore.self>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="cr-radio mb-3">
                            <input type="radio" id="submitTypeInstant" name="submitType" wire:model="submitType" value="instant">
                            <label for="submitTypeInstant" style="margin-left: 15px">فوری</label>
                            <input type="radio" id="submitTypeTime" name="submitType" wire:model="submitType" value="time">
                            <label for="submitTypeTime">انتخاب زمان تحویل</label>
                        </div>
                    </div>
                    <div class="col-12" id="time-section">
                        <div class="cr-radio mb-3">
                            @foreach($weeks as $item)
                                <input type="radio" id="week-{{$item->key}}" name="week" wire:model="week" value="{{$item->key}}">
                                <label for="week-{{$item->key}}" style="margin-left: 15px">{{$item->title}}</label>
                            @endforeach

                        </div>
                        <div class="cr-radio mb-3">
                            @foreach($hours as $item)
                                <input type="radio" id="hour-{{$item->key}}" name="hour" wire:model="hour" value="{{$item->key}}">
                                <label for="hour-{{$item->key}}" style="margin-left: 15px">{{$item->title}}</label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cr-card-footer">
            <div class="col-lg-2 col-md-6 col-12">
                <div class="cr-button">
                    <label for=""></label>
                    {{button('ثبت درخواست')}}
                </div>
            </div>
        </div>
    </form>
    {{toast($errors)}}
    @script
    <script>
        $(document).on('click', '.card-remove', function(e) {
            e.preventDefault()
            Swal.fire({
                title: 'حذف شماره کارت؟',
                text: 'آیا می خواهید این شماره کارت را لغو کنید؟',
                icon: 'error',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                console.log(result)
                if (result.isConfirmed) {
                    console.log($(this).data('card'));
                    $wire.dispatch('card-remove',{card : $(this).data('card')});
                    $('.modal').modal('hide');
                    $('#address option:selected').remove();
                    $('#card option:selected').remove();

                }
            });
        });
        $(document).on('click', '.address-remove', function(e) {
            e.preventDefault()
            Swal.fire({
                title: 'حذف آدرس؟',
                text: 'آیا می خواهید این آدرس را لغو کنید؟',
                icon: 'error',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                console.log(result)
                if (result.isConfirmed) {
                    console.log($(this).data('card'));
                    $wire.dispatch('address-remove',{address : $(this).data('address')});
                    $('.modal').modal('hide');
                    $('#address option:selected').remove();
                    $('#card option:selected').remove();

                }
            });
        });

        $wire.on('remove-modal', (event) => {
            //$('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            //$('.modal').modal('hide');
        });

        $(document).on('change','#card',function (){
            @this.card = $(this).val();
        });
        // $(document).on('change','#address',function (){
        //     console.log($(this).val())
        //     @this.address = $(this).val();
        // });
        if($('[name="submitType"]:checked').val() == 'instant'){
            $('#time-section').addClass('cr-hidden');
        }
        $(document).on('change','[name="submitType"]',function (){
            if($(this).val() == 'instant'){
                $('#time-section').addClass('cr-hidden');
            }
            else{
                $('#time-section').removeClass('cr-hidden');
            }
        });
    </script>
    @endscript
    @script
    <script>
        document.addEventListener("livewire:load", () => {
            initSelect2();
        });

        document.addEventListener("livewire:navigated", () => {
            initSelect2();
        });

        function initSelect2() {
             console.log('set select2')
            let $select = $('#address');


            // اگر قبلاً initialize شده، destroy کن
            if ($select.hasClass("select2-hidden-accessible")) {
                $select.select2('destroy');
            }

            // Initialize جدید
            $select.select2({
                placeholder: "جستجو...",
                dir: "rtl",
                width: "100%"
            });

            // وقتی انتخاب شد → Livewire آپدیت شود
            $select.on('change', function () {
                console.log($(this).val(),'hhhh')
            @this.set('address', $(this).val());
            });
        }

    </script>
    @endscript

</div>

