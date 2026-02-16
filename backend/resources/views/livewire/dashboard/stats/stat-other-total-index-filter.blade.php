
<form action="">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-12">
                <div class="cr-select cr-md mb-3">
                    <label for="cashout">بخش گزارش گیری</label>
                    <select wire:model.live="type" >
                        @foreach($types as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class="cr-select cr-md mb-3">
                    <div class="row">
                        <div class="col-12 col-md-10 p-0">
                            <div class="cr-select cr-icon  mb-6" id="">
                                <label> عملگر </label>
                                <select wire:model.live="op"  id="op" name="op" >
                                    @foreach($operators_fa as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12">
                <div class=" mb-3">
                    <div class="row">
                            <div class=" cr-select cr-md col-12 col-md-10 "  wire:ignore >
                                <label>فیلدها</label>
                                <select  id="select" multiple="multiple" >
                                    @foreach($options as $id => $name)

                                        <option value="{{ $id }}" @if(in_array($id,array_values($this->field))) selected @endif>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-12"> <div class="cr-date">
                    <label>از تاریخ</label>
                    <i class='bx bxs-calendar'></i>
                    <input type="text" wire:model.live.debounce.500ms="dateFrom" id="dateFrom" value="{{$dateFrom}}" autocomplete="off">
                </div></div>
            <div class="col-lg-4 col-md-6 col-12"> <div class="cr-date">
                    <label>تا تاریخ</label>
                    <i class='bx bxs-calendar'></i>
                    <input type="text" wire:model.live.debounce.500ms="dateTo" id="dateTo" value="{{$dateTo}}" autocomplete="off">
                </div></div>
            @script
            <script>
                $(document).ready(function (){

                    $('#dateFrom').persianDatepicker({
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
                        @this.set('dateFrom', new persianDate(unix).format('YYYY/MM/DD'),true)
                        }
                    });
                    $('#dateTo').persianDatepicker({
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
                        @this.set('dateTo', new persianDate(unix).format('YYYY/MM/DD'),true)
                        }
                    });
                    $(document).on('change','#driverId',function (){
                        $wire.dispatch('driverId', {driverId: $(this).val()});
                    })
                    $(document).on('change','#type',function (){
                        $wire.dispatch('type', {type: $(this).val()});
                    })
                })
            </script>
            @endscript

        </div>
    </div>
</form>

<script>
        document.addEventListener('DOMContentLoaded', function () {
            initializeSelect();
            function initializeSelect() {
                if ($('#select').hasClass('select2-hidden-accessible')) {
                    $('#select').select2('destroy');
                }$('#select').select2({
                    placeholder: 'انتخاب کنید',
                    allowClear: true,
                    width: '100%'
                });
                $('#select').on('change', function () {
                    let selectedValues = $(this).val();
                @this.set('field', selectedValues);
                });
            }
            Livewire.on('options', function (data) {
                data = JSON.parse(data)
                console.log(data)
                let selectHtml
                data.forEach((item, index) => {
                    selectHtml += `<option value=${index} >${item}</option>`;
                });
                console.log(selectHtml)
                $('#select').empty()
                $('#select').html(selectHtml)
            });
        });
</script>
