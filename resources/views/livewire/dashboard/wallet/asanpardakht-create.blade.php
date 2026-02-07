<div>
    <div class="cr-modal" wire:ignore>
        <div class="modal fade" tabindex="-1" id="ap-create">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ثبت مشخصات پرداخت</h5>
                        <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="store" wire:ignore>
                            <div class="cr-text mb-3">
                                <label for="">مبلغ</label>
                                <input type="text" placeholder="مبلغ به تومان وارد شود" wire:model="amount">
                            </div>
                            <div class="cr-text mb-3">
                                <label for="">بانک</label>
                                <input type="text" placeholder="بانک واریزی" wire:model="bank">
                            </div>
                            <div class="cr-text mb-3">
                                <label for="">کد رهگیری</label>
                                <input type="text" placeholder="" wire:model="rrn">
                            </div>
                            <div class="cr-text mb-3">
                                <label for="">تاریخ</label>
                                <input type="text" placeholder="تاریخ واریز" wire:model="date" id="date" autocomplete="off">
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
    @script
    <script>
        $(document).ready(function (){
            $('#date').persianDatepicker({
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
                    @this.date = new persianDate(unix).format('YYYY/MM/DD');
                }
            });

            $wire.on('reload-page',function (event){
                $('#ap-create').modal('hide');
            })
        })
    </script>
    @endscript
{{toast($errors)}}
</div>
