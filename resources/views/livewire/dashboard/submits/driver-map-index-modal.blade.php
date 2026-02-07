@php
    use App\Models\Submit;use Hekmatinasser\Verta\Verta;
 @endphp
<div>
    @if(isset($driverinfo))
        <div class="modal fade modal-lg" id="driverinfo" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true" wire:ignore>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            {{$driverinfo['name'].' '.$driverinfo['lastname']}}</h5>
                        <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive text-center text-nowrap">
                            <div class="row">
                                <div class="col-2 text-end"></div>
                                <div class="col-10">
                                </div>
                                <div class="col-2 text-end"><i class='bx bxs-user'></i>نام</div>
                                <div class="col-10">

                                    <a href="{{route('d.users.single',$driverinfo['id'])}}"
                                       target="_blank">{{$driverinfo['name'].' '.$driverinfo['lastname']}}</a>
                                </div>
                                <div class="col-2 text-end"><i class='bx bx-time-five'></i>زمان ثبت آخرین موقعیت</div>
                                <div
                                    class="col-10 dir-ltr">{{ \Verta::instance($driverinfo['created_at'])->format('Y/m/d H:i') }}</div>
                                <div class="col-2 text-end"><i class='bx bx-current-location'></i>منطقه</div>
                                <div
                                    class="col-10">{{ xDistrict([$driverinfo['lat'], $driverinfo['lon']]) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    </div>

                </div>
            </div>
        </div>
    @endif
    {{toast($errors)}}
</div>

