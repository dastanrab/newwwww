@php
    $gate = Gate::allows('setting_area_edit',App\Livewire\Dashboard\Settings\AreaIndex::class)
@endphp
<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />


        <div class="cr-container-section">
            <livewire:dashboard.settings.area-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div class="clearfix"></div>
                        <div class="cr-filter-section">
                            <div class="row">
                                <div class="col-lg-7 col-12">
                                    <livewire:dashboard.settings.area-index-filter/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0" id="paginated-list">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading.class="cr-parent-spinner">
                            {{spinner()}}
                        </div>
                        @if($this->drivers->count())
                        <table class="cr-table table">
                            <thead>
                            <tr>
                                <th>راننده</th>
                                <th>مناطق بازیست</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($drivers as $driver)
                                <tr>
                                    <td>
                                        <a href="{{route('d.drivers.single',$driver->id)}}" class="cr-name">{{$driver->name.' '.$driver->lastname}}</a>
                                    </td>
                                    <td>
                                        <div class="cr-area">
                                            @foreach($polygons as $polygon)
                                                <span class="
                                                @if ($driver->polygonDrivers)
                                                    @foreach ($driver->polygonDrivers as $polygonSelected)
                                                    @if ($polygonSelected->polygon->region == $polygon->region)
                                                        cr-active
                                                        @break
                                                    @endif
                                                    @endforeach
                                                @endif
                                                "
                                                @if($gate)
                                                wire:click="polygonSelect('{{$driver->id}}','{{$polygon->id}}')">
                                                @endif
                                                {{$polygon->region}}
                                                </span>

                                            @endforeach
                                        </div>
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
                {{--<div class="cr-card-footer">
                    {{ $this->drivers->links(data: ['scrollTo' => '#paginated-list']) }}
                </div>--}}
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
