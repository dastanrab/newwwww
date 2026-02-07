<div class="cr-modal">
    <div class="cr-modal">
        <div class="modal fade" tabindex="-1" id="add-iban-{{$user->id}}" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">افزودن کارت</h5>
                        <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="addIban('{{$user->id}}')">
                            <div class="row">
                                <div class="col-12">
                                    <div class="cr-text">
                                        <input type="text" placeholder="شماره کارت را وارد نمایید" wire:model="card">
                                    </div>
                                </div>
                                @if($inquiry)
                                    <div><strong>شماره شبا:</strong> <span>{{$inquiryShaba}}</span></div>
                                    <div><strong>شماره کارت:</strong> <span>{{$inquiryCard}}</span></div>
                                    <div><strong>نام صاحب کارت:</strong> <span>{{$inquiryName}}</span></div>
                                    <div><strong>نام بانک:</strong> <span>{{$inquiryBank}}</span></div>
                                @endif
                                <div class="col-12">
                                    <div class="cr-button">
                                        {{button($btnText,$btnIcon)}}
                                    </div>
                                </div>
                            </div>
                        </form>
                        @if($inquiry)
                            <div class="cr-button mt-2">
                                <div class="col-12">
                                    <form wire:submit.prevent="deleteAll">
                                        {{button('حذف','bx bx-x-circle')}}
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @script
    <script>
        $wire.on('prev-modal', (data) => {
            $('#send-to-bank-'+data.userId).modal('show');
            $('#add-iban-'+data.userId).modal('hide');
        });
    </script>
    @endscript
    {{toast($errors)}}
</div>
