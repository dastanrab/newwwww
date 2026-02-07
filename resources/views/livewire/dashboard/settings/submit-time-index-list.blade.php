<div>
    @php
        use App\Models\Polygon;
        $gate = Gate::allows('setting_submit_time_edit',App\Livewire\Dashboard\Settings\SubmitTimeIndex::class);
        $countAllPolygon = Polygon::count();
    @endphp
    <div class="cr-card-body p-0" id="paginated-list">
        <div class="table-responsive text-center text-nowrap">
            <table class="cr-table table">
                <thead>
                <tr>
                    <th>روزهای هفته</th>
                    <th>بازه های درخواست</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>فوری</td>
                    <td>
                        <div class="cr-checkbox mb-4">
                            <input type="checkbox"
                                   @if($gate)
                                       wire:click="instantUpdate"
                                   @else
                                       disabled
                                   @endif
                                   id="instant" value="1" @if($instant == 1) checked @endif>
                            <label for="instant"></label>
                        </div>
                    </td>
                </tr>
                @foreach($this->days as $day)
                    <tr>
                        <td>{{$day->title}}</td>
                        <td>
                            <div class="cr-submit-time">
                                @foreach($this->hours as $hour)
                                    @php
                                        $count = $this->polygonDayHour->where('day_id',$day->id)->where('hour_id',$hour->id)->pluck('status')->intersect(0)->count();
                                        if($count == $countAllPolygon){
                                            $select = '';
                                        }
                                        elseif($count > 0){
                                            $select = 'cr-blue';
                                        }
                                        else{
                                            $select = 'cr-active';
                                        }
                                    @endphp
                                    <span class="cr-hour {{$select}}" data-bs-toggle="modal"
                                          data-bs-target="#modal-day-{{$day->id}}-{{$hour->id}}"
                                          id="day-{{$day->id}}-{{$hour->id}}">{{$hour->start_at.' تا '.$hour->end_at}}</span>
                                    <!-- Modal -->
                                    <div class="modal fade" id="modal-day-{{$day->id}}-{{$hour->id}}" tabindex="-1"
                                         aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="exampleModalLabel">{{$day->title.' '.$hour->start_at.' تا '.$hour->end_at}}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div wire:loading.class="cr-parent-spinner">
                                                        {{spinner()}}
                                                    </div>
                                                    <div class="cr-submit-time">
                                                        @if($gate)
                                                            @php
                                                                $select = '';
                                                                if($count){
                                                                    $select = 'select';
                                                                    $textSelect = 'انتخاب همه';
                                                                }
                                                                else{
                                                                    $select = 'deselect';
                                                                    $textSelect = 'حذف انتخابی ها';
                                                                }
                                                            @endphp
                                                            @if($gate)
                                                            <label class="cr-polygon-all" wire:click="save('{{$day->id}}','{{$hour->id}}','all','{{$select}}')">{{$textSelect}}</label>
                                                            @endif
                                                        @endif
                                                        @foreach($this->polygons as $polygon)
                                                            @php
                                                                $polygonDayHour = $this->polygonDayHour->where('polygon_id' , $polygon->id)->where('day_id',$day->id)->where('hour_id',$hour->id)->first();
                                                            @endphp
                                                            <span class="cr-polygon {{$polygonDayHour && $polygonDayHour['status'] ? 'cr-active' : ''}}"
                                                                @if($gate) wire:click="save('{{$day->id}}','{{$hour->id}}','{{$polygon->id}}')"> @endif
                                                                {{$polygon->region}}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
</div>
