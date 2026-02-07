<div id="paginated-list">
    <div class="cr-card-body p-0">
        <div class="table-responsive text-center text-nowrap">
            <div wire:loading.class="cr-parent-spinner">
                {{spinner()}}
            </div>
            @if($this->users->count())
                <table class="cr-table table" >
                    <thead>
                    <tr>
                        <th>شناسه</th>
                        <th>نام و نام خانوادگی</th>
                        <th>شماره همراه</th>
                        <th>تاریخ ثبت نام</th>
                        <th>گروه کاربری</th>
                        <th>تاریخ آخرین همکاری</th>
                        <th>مدت زمان همکاری</th>
                        <th>تعداد همکاری</th>
                        <th>ملبغ (تومان)</th>
                        @if(auth()->user()->getRole('name') != 'supervisor') <th>توضیح</th> @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($this->users as $user)
                        <tr wire:key="{{$user->id}}">
                            <td>{{$user->id}}</td>
                            <td>
                                <a href="{{route('d.users.single',$user->id)}}" class="cr-name">
                                    @if($user->level == 2)
                                        {!! levelIcon() !!}
                                    @endif
                                    {{$user->name || $user->lastname ? $user->name.' '.$user->lastname : '-'}}
                                </a>
                            </td>
                            <td>{{$user->mobile}}</td>
                            <td class="dir-ltr">{{verta($user->created_at)->format('Y/m/d H:i')}}</td>
                            <td>{{$user->getRoleName()}} - {{$user->getLegalType()}}</td>
                            <td class="dir-ltr">{{ $user->submits->where('status', 3)->last() ? \Verta::instance($user->submits->last()->start_deadline)->format('Y/n/j H:i') : '-' }}</td>
                            <td class="dir-ltr">{{ $user->submits->where('status', 3)->last() ? \Carbon\Carbon::parse(\Carbon\Carbon::now())->diffInDays(\Carbon\Carbon::parse($user->submits->where('status', 3)->last()->start_deadline)) : '-' }}</td>
                            <td>{{ $user->submits->where('status', 3)->count() }}</td>
                            <td>{{ number_format($user->submits->pluck('total_amount')->sum()) }}</td>
                            @if(auth()->user()->getRole('name') != 'supervisor' or auth()->user()->getRole('name') != 'senior_supervisor')
                                <td>
                                    <div class="cr-actions">
                                        <ul>
                                            <li data-bs-toggle="modal" data-bs-target="#desc-{{$user->id}}" class="position-relative">
                                                @if($user->userComments->count())
                                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $user->userComments->count() }}
                                                    </span>
                                                @endif
                                                <a href="#" data-bs-toggle="tooltip" title="توضیحات">
                                                    <i class="bx bxs-copy-alt"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    {{--modal--}}
                                    <div class="cr-modal">
                                        <div class="modal fade" tabindex="-1" id="desc-{{$user->id}}" wire:ignore.self>
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">توضیحات</h5>
                                                        <button type="button" class="cr-close" data-bs-dismiss="modal" aria-label="Close">
                                                            <i class="bx bx-x"></i>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form wire:submit.prevent="storeComment('{{$user->id}}')">
                                                            @foreach ($user->userComments as $comments)
                                                                <div class="alert alert-secondary text-end">{{ $comments->text }}</div>
                                                            @endforeach
                                                            <div class="cr-textarea">
                                                                <textarea placeholder="توضیح خود را وارد نمایید" wire:model="comment"></textarea>
                                                            </div>
                                                            <div class="cr-button">
                                                                {{button('ثبت')}}
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
        {{ $this->users->links(data: ['scrollTo' => '#paginated-list']) }}
    </div>
    @script
    <script>
        $(document).ready(function (){
            $('[data-bs-toggle="tooltip"]').tooltip()
        })
    </script>
    @endscript
</div>
