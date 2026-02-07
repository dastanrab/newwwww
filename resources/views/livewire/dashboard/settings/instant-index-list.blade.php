<div class="cr-card">
    <div class="cr-card-header">
        <div class="cr-title">
            <div>
                <strong>مناطق فوری</strong>
            </div>
        </div>
        <div class="cr-filter-section">
            <div class="row">
                <div class="col-lg-5 col-12">
                    <livewire:dashboard.layouts.table-row/>
                </div>
                <div class="col-lg-7 col-12">
                    <livewire:dashboard.settings.instant-index-filter/>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-lg-2 col-12">
                    <button class="btn btn-success" wire:click="instant_all()">
                        <span>فوری کردن همه</span>
                    </button>
                </div>
                <div class="col-lg-2 col-12">
                    <button class="btn btn-danger" wire:click="deinstant_all()">
                        <span> غیر فوری کردن همه</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="cr-card-body p-0" id="paginated-list">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if(count($this->polygons()->items()) > 0)
                <table class="cr-table table">
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>منطقه</th>
                        <th>وضعیت قبول درخواست فوری</th>
                        <th>وضعیت قبول شهروندی</th>
                        <th>وضعیت قبول غیرشهروندی</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->polygons()->items() as $polygon)
                        <tr wire:key="{{$polygon->id}}">
                            <td>{{$polygon->id}}</td>
                            <td>{{$polygon->region}}</td>
                            <td><input  type="checkbox" wire:change="update_instant({{$polygon->id}},$event.target.checked)" id="some_id" {{  $polygon->has_instant === 1 ? "checked" : "" }} /></td>
                            <td><input  type="checkbox" wire:change="update_legal({{$polygon->id}},$event.target.checked)" id="some_id" {{  $polygon->has_legal_collect === 1 ? "checked" : "" }} /></td>
                            <td><input  type="checkbox" wire:change="update_illegal({{$polygon->id}},$event.target.checked)" id="some_id" {{  $polygon->has_illegal_collect === 1 ? "checked" : "" }} /></td>

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
        {{ $this->polygons()->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    {{toast($errors)}}
</div>
