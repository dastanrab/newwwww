<div>
    <li data-bs-toggle="modal" data-bs-target="#edit-{{$isLegal ? 'guild' : 'citizen'}}-{{$recyclable->id}}">
        <a href="#" data-bs-toggle="tooltip" title="ویرایش {{$isLegal ? 'صنفی' : 'شهروندی'}}">
            <i class="bx {{$isLegal ? 'bxs-store-alt' : 'bx-user'}}"></i>
        </a>
    </li>
    <!-- Modal -->
    <div class="modal fade" id="edit-{{$isLegal ? 'guild' : 'citizen'}}-{{$recyclable->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ویرایش درصدهای {{$recyclable->title}} ({{$isLegal ? 'صنفی' : 'شهروندی'}})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="table-responsive text-center text-nowrap">
                            @if($this->percentages->count())
                                <table class="cr-table table">
                                    <thead>
                                    <tr>
                                        <th>وزن (کیلوگرم)</th>
                                        <th>درصد</th>
                                        <th>قیمت (تومان)</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($this->percentages as $percentage)
                                        <tr wire:key="{{$percentage->id}}">
                                            <td>{{ $percentage->weight }}</td>
                                            <td><div class="cr-text"><input class="text-center bxs-widget width-100" type="text" wire:model="percents.{{$percentage->id}}"></div></td>
                                            <td>{{number_format($percentage->price)}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                @include('livewire.dashboard.layouts.data-not-exists')
                            @endif
                        </div>
                    </div>
                    @if(Gate::allows('setting_recyclable_single_edit',App\Livewire\Dashboard\Settings\RecyclableIndex::class))
                        <div class="modal-footer">
                            <div class="cr-button">
                                {{button('ویرایش')}}
                            </div>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    {{toast($errors)}}
</div>
