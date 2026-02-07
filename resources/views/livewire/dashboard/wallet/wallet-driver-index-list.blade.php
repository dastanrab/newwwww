<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->drivers->count())
                <table class="cr-table table">
                    <thead>
                    <tr>
                        <th>راننده</th>
                        <th>موجودی</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->drivers as $driver)
                        <tr wire:key="{{$driver->id}}">
                            <td>
                                @if($driver->user)
                                    <a href="{{route('d.users.single',$driver->user->id)}}" class="cr-name">{{$driver->user->name || $driver->user->lastname ? $driver->user->name.' '.$driver->user->lastname : '-'}}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{tomanFormat($driver->amount/10)}}</td>
                            <td>
                                <div class="cr-modal">
                                    <div class="modal fade" tabindex="-1" id="deposit-{{$driver->user->id}}" wire:ignore>
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">افزایش موجودی</h5>
                                                    <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                                                        <i class="bx bx-x"></i>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form wire:submit.prevent="deposit('{{$driver->user->id}}')">
                                                        <div class="cr-text">
                                                            <input type="text" placeholder="مبلغ به تومان وارد شود" wire:model="amount">
                                                        </div>
                                                        <div class="cr-button mt-2">
                                                            {{button('ثبت')}}
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="cr-actions">
                                    <ul>
                                        <li class="m-2" data-bs-toggle="modal" data-bs-target="#deposit-{{$driver->user->id}}">
                                            <a href="#" class="text-bg-success" data-bs-toggle="tooltip" title="افزایش موجودی" >
                                                <i class='bx bxs-wallet'></i>
                                            </a>
                                        </li>
                                    </ul>
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
    <div class="cr-card-footer">
        {{ $this->drivers->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    @script
    <script>

        jQuery(document).ready(function($) {
            $('[data-bs-toggle="tooltip"]').tooltip()
        });
    </script>
    @endscript
    {{toast($errors)}}
</div>
