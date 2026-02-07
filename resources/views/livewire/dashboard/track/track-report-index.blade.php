<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar/>
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>ردیابی</strong>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0">
                    <div class="table-responsive text-center text-nowrap">
                        @if($cars->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>راننده</th>
                                    <th>خودرو</th>
                                    <th>IMEI</th>
                                    <th>سیم کارت</th>
                                    <th>آخرین ارتباط</th>
                                    <th>مناطق</th>
                                </tr>
                                </thead>
                                <tbody class="uk-text-center">
                                @foreach($cars as $car)
                                    <tr>
                                        <td>{{ $car->user->name }} {{ $car->user->lastname }}</td>
                                        <td>{{ $car->type }}</td>
                                        <td>{{ $car->imei }}</td>
                                        <td>{{ $car->simcard }}</td>
                                        @php($location = \App\Models\Location::where('car_id', $car->id)->orderBy('created_at', 'DESC')->first())
                                        <td>{{ $location ? \Verta::instance($location->created_at)->format('H:i:s - Y/m/d') : ''}}</td>
                                        <td>
                                            @if ($car->user->polygonDrivers)
                                                @foreach ($car->user->polygonDrivers as $polygon)
                                                    ,{{ $polygon->polygon->region }}
                                                @endforeach
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
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
