<div>
    <form wire:submit.prevent="store">
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>ورودی انبار {{$user->name.' '.$user->lastname}}</strong>
                </div>
            </div>
        </div>
        <div class="cr-card-body p-0" wire:ignore.self>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="cr-text cr-md mb-3">
                            <label for="name">شماره قبض باسکول</label>
                            <input type="text" id="name" placeholder="شماره قبض باسکول را وارد نمایید"  wire:model="basculeBillNumber">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="cr-text cr-md mb-3">
                            <label for="receivedAt">تاریخ</label>
                            <input type="text" id="receivedAt" placeholder="تاریخ ثبت را وارد نمایید" wire:model="receivedAt" autocomplete="off">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="cr-card-footer"></div>
        <div class="cr-card-header">
            <div class="cr-title">
                <div>
                    <strong>پسماندها</strong>
                </div>
                <div class="cr-actions">
                    <a class="cr-action cr-primary" href="" wire:click.prevent="addWaste">
                        <span> افزودن پسماند</span>
                        <i class="bx bx-plus"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="cr-card-body p-0">
            <div class="cr-card-body p-0">
                <div class="table-responsive text-center text-nowrap">
                    <div wire:loading.class="cr-parent-spinner">
                        {{spinner()}}
                    </div>
                    <table class="cr-table table">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>عنوان</th>
                            <th>وزن</th>
                            <th>حذف</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if($waste)
                            @foreach($waste as $key => $value)
                                <tr wire:key="{{$key}}">
                                    <td>{{$key+1}}</td>
                                    <td>
                                        <div class="cr-select">
                                            <select name="" id="" wire:model="waste.{{$key}}">
                                                <option value="">انتخاب پسماند</option>
                                                @foreach($this->recyclables as $recyclable)
                                                    <option value="{{$recyclable->id}}">{{$recyclable->title}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cr-text">
                                            <input type="text" value="" wire:model="weight.{{$key}}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="cr-actions">
                                            <ul>
                                                <li>
                                                    <a href="#" class="cr-remove" data-bs-toggle="tooltip" title="حذف" data-waste-id="{{$key}}">
                                                        <i class="bx bxs-trash"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="cr-card-footer">
            <div class="cr-card-footer">
                <div class="col-lg-2 col-md-6 col-12">
                    <div class="cr-button">
                        <label for=""></label>
                        {{button('ثبت در انبار')}}
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{toast($errors)}}
</div>
@script
<script>
    $(document).ready(function (){

        $(document).on('click', '.cr-remove', function(e) {
            Swal.fire({
                title: 'حذف',
                text: 'آیا می خواهید حذف کنید؟',
                icon: 'error',
                showCloseButton: true,
                showCancelButton: true,
                cancelButtonText: 'خیر',
                confirmButtonText: 'بله',
            }).then((result) => {
                console.log(result)
                if (result.isConfirmed) {
                    $wire.dispatch('removeWaste',{waste : $(this).data('waste-id')});
                }
            });
        });

        $('#receivedAt').persianDatepicker({
            calendar: {
                persian: {
                    leapYearMode: 'astronomical'
                },},
            defaultValue: '',
            observer: true,
            initialValueType: 'persian',
            format: 'YYYY/MM/DD',
            initialValue: false,
            autoClose: true,
            onSelect: function(unix){
                @this.set('receivedAt', new persianDate(unix).format('YYYY/MM/DD'),true)
            }
        });
    })
</script>
@endscript
