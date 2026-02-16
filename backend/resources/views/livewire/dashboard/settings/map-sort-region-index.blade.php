<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <livewire:dashboard.settings.map-sort-region-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="row">
                        <div class="col-lg-10 col-md-9 col-12">
                            <div class="cr-title">
                                <div>
                                    <strong>مرتب سازی مناطق</strong>
                                    <div class="cr-stats">بکشید و جای دلخواه رها کنید</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 col-12">
                            <div class="cr-button">
                                <button id="save">مرتب سازی</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0" id="paginated-list">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        @if($this->polygons->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th class="text-end">منطقه</th>
                                </tr>
                                </thead>
                                <tbody id="sortable">
                                @foreach($this->polygons as $polygon)
                                    <tr data-regionid="{{$polygon->id}}" class="el-region">
                                        <td class="text-end"><i class="bx bx-expand-vertical"></i></td>
                                        <td class="text-end">{{$polygon->region}}</td>
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
                    <div class="col-lg-2 col-md-6 col-12">
                        <div class="cr-button">
                            <button id="save">مرتب سازی</button>
                        </div>
                    </div>
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
    {{toast($errors)}}
</div>
@script
<script>
    jQuery(document).ready(function ($){
        $(document).on('click','#save',function (){
            let id = [];
            $('.el-region').each(function(i) {
                id[i] = $(this).data('regionid');
            });
            Livewire.dispatch('sortRegionId', { ids: id})
        });
    });
</script>
@endscript
