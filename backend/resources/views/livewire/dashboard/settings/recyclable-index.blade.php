<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />
        <div class="cr-container-section">
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>دسته بندی پسماندها</strong>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0" id="paginated-list">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        @if($this->recyclables->count())
                            <table class="cr-table table">
                                <thead>
                                <tr>
                                    <th>عنوان</th>
                                    <th>قیمت پایه فاوا (تومان)</th>
                                    <th>قیمت صنفی (تومان)</th>
                                    <th>قیمت شهروندی (تومان)</th>
                                    <th>حداکثر خرید راننده (تومان)</th>
                                    @if(Gate::allows('setting_recyclable_single',App\Livewire\Dashboard\Settings\RecyclableIndex::class))
                                    <th>عملیات</th>
                                    @endif
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($this->recyclables as $i => $recyclable)
                                    <tr>
                                        <td>{{ $recyclable->title }}</td>
                                        <td>{{ number_format($recyclable->price) }}</td>
                                        <td>
                                            <i class='bx bxs-up-arrow' style='color:#348606'></i> {{ number_format($recyclable->percentages->where('is_legal', true)->sortByDesc('price')->first()->price) }}
                                            <br>
                                            <i class='bx bxs-down-arrow' style='color:#ea1c1c'></i> {{ number_format($recyclable->percentages->where('is_legal', true)->where('weight', 1)->first()->price) }}
                                        </td>
                                        <td>
                                            <i class='bx bxs-up-arrow' style='color:#348606'></i> {{ number_format($recyclable->percentages->where('is_legal', false)->sortByDesc('price')->first()->price) }}
                                            <br>
                                            <i class='bx bxs-down-arrow' style='color:#ea1c1c'></i> {{ number_format($recyclable->percentages->where('is_legal', false)->where('weight', 1)->first()->price) }}
                                        </td>
                                        <td>{{number_format($recyclable->max_price)}}</td>
                                        @if(Gate::allows('setting_recyclable_single',App\Livewire\Dashboard\Settings\RecyclableIndex::class))
                                        <td>
                                            <div class="cr-actions">
                                                <ul>
                                                    <livewire:dashboard.settings.recyclable-index-edit-max-price :$recyclable/>
                                                    <livewire:dashboard.settings.recyclable-index-edit-percent :$recyclable :isLegal="0" wire:key="percent-1"/>
                                                    <livewire:dashboard.settings.recyclable-index-edit-percent :$recyclable :isLegal="1" wire:key="percent-2"/>
                                                    <livewire:dashboard.settings.recyclable-index-edit-description :$recyclable/>
                                                </ul>
                                            </div>
                                        </td>
                                        @endif
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
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
