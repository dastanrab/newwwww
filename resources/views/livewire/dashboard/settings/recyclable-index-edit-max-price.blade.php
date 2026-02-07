<div>
    <li data-bs-toggle="modal" data-bs-target="#edit-max-price-{{$recyclable->id}}">
        <a href="#" class="cr-remove" data-bs-toggle="tooltip" title="ویرایش حداکثر خرید راننده">
            <i class="bx bx-car"></i>
        </a>
    </li>
    <!-- Modal -->
    <div class="modal fade" id="edit-max-price-{{$recyclable->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ویرایش حداکثر خرید راننده</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body">
                        <div class="table-responsive text-center text-nowrap">
                            <div class="cr-text">
                                <input type="text" wire:model="maxPrice">
                            </div>
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
