<div>
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading>
                {{spinner()}}
            </div>
            @if($this->rollcalls->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>تاریخ</th>
                        <th>شروع حضور</th>
                        <th>مختصات حضور</th>
                        <th>پایان حضور</th>
                        <th>مختصات خروج</th>
                        <th>ویرایش</th>
                        <th>تاریخچه</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->rollcalls as $rollcall)
                        <tr wire:key="{{$rollcall->id}}">
                            <td>{{$rollcall->id}}</td>
                            <td>{{\Verta::instance($rollcall->start_at)->format('Y/m/d')}}</td>
                            <td>{{\Verta::instance($rollcall->start_at)->format('H:i:s')}}</td>
                            <td>{{$rollcall->start_lat}},{{$rollcall->start_lon}}</td>
                            <td>
                                @if ($rollcall->end_at)
                                    {{\Verta::instance($rollcall->end_at)->format('H:i:s')}}
                                @elseif(Gate::allows('user_driver_index_rollcall_edit',App\Models\User::class))
                                    <a href="" class="cr-edit" data-bs-toggle="modal" data-bs-target="#rollcall-end-{{$rollcall->id}}">
                                        <i class='bx bx-calendar-check'></i>
                                    </a>
                                    <!-- Modal -->
                                    <div class="modal fade" id="rollcall-end-{{$rollcall->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" >
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">پایان حضور</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form wire:submit.prevent="endRollcall('{{$rollcall->id}}')">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-4 col-12">
                                                                <div class="cr-text">
                                                                    <label for="min">دقیقه</label>
                                                                    <input type="text" class="text-center" wire:model="min">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-12">
                                                                <div class="cr-text">
                                                                    <label for="hour">ساعت</label>
                                                                    <input type="text" class="text-center" wire:model="hour">
                                                                </div>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                            <div class="col-12">
                                                                <div class="cr-textarea">
                                                                    <label for="description" class="text-end">توضیحات</label>
                                                                    <textarea id="description" wire:model="description"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="cr-button">
                                                            {{button('پایان حضور')}}
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($rollcall->end_lat)
                                {{$rollcall->end_lat}},{{$rollcall->end_lon}}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ((\Carbon\Carbon::parse($rollcall->created_at)->isToday() || \Carbon\Carbon::parse($rollcall->created_at)->isYesterday()) && Gate::allows('user_driver_index_rollcall_edit',App\Models\User::class))
                                    <a href="" class="cr-edit" data-bs-toggle="modal" data-bs-target="#rollcall-edit-{{$rollcall->id}}" wire:click.prevent="getRollCall('{{$rollcall->id}}')">
                                        <i class='bx bx-calendar-plus'></i>
                                    </a>
                                    <!-- Modal -->
                                    <div class="modal fade" id="rollcall-edit-{{$rollcall->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore >
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">ویرایش حضور</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form wire:submit.prevent="editRollcall('{{$rollcall->id}}')">
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="cr-text">
                                                                    <label class="text-end">پایان حضور</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-12">
                                                                <div class="cr-text">
                                                                    <label for="min">دقیقه</label>
                                                                    <input type="text" class="text-center" wire:model="endMin">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4 col-12">
                                                                <div class="cr-text">
                                                                    <label for="hour">ساعت</label>
                                                                    <input type="text" class="text-center" wire:model="endHour">
                                                                </div>
                                                            </div>
                                                            <div class="clearfix"></div>
                                                            <div class="col-12">
                                                                <div class="cr-textarea">
                                                                    <label for="description" class="text-end">توضیحات</label>
                                                                    <textarea id="description" wire:model="description"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="cr-button">
                                                            {{button('ویرایش حضور')}}
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($rollcall->histories->count())
                                <a href="" class="cr-edit" data-bs-toggle="modal" data-bs-target="#rollcall-history-{{$rollcall->id}}">
                                    <i class='bx bx-history'></i>
                                </a>

                                <!-- Modal -->
                                <div class="modal fade" id="rollcall-history-{{$rollcall->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore >
                                    <div class="modal-dialog modal-xl">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">تاریخچه حضور و غیاب</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">

                                                <table class="table table-responsive">
                                                    <thead>
                                                    <tr>
                                                        <th>کاربر</th>
                                                        <th>توضیحات</th>
                                                        <th>ورود</th>
                                                        <th>خروج</th>
                                                        <th>تاریخ ایجاد رکورد</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($rollcall->histories()->orderBy('created_at','DESC')->get() as $history)
                                                        <tr>
                                                            <td class="dir-ltr">{{$history->user->name.' '.$history->user->lastname}}</td>
                                                            <td class="dir-ltr">{{$history->description}}</td>
                                                            <td class="dir-ltr">{{$history->start_at ? verta()->instance($history->start_at)->format('Y/m/d H:i:s') : '-'}}</td>
                                                            <td class="dir-ltr">{{$history->end_at ? verta()->instance($history->end_at)->format('Y/m/d H:i:s') : '-'}}</td>
                                                            <td class="dir-ltr">{{verta()->instance($history->created_at)->format('Y/m/d H:i:s')}}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>

                                            </div>
                                            <div class="modal-footer"></div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
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
        {{ $this->rollcalls->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    @script
    <script>
        $wire.on('remove-modal', (event) => {
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $('.modal').modal('hide');
        });

    </script>
    @endscript
    @isset($errors)
        <script>
            $('body').removeClass('modal-open');
            $('.modal-backdrop').remove();
            $('.modal').modal('hide');
        </script>
    @endisset
    {{toast($errors)}}
</div>
