<div>
    <div class="cr-card-header">
        <div class="cr-title">
            <div>
                <strong>انتخاب کاربر</strong>
            </div>
        </div>
    </div>
    <div class="cr-card-body p-0">
        <div class="container-fluid">
            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12">
                        <div class="cr-text cr-icon cr-md mb-3">
                            <label for="driver">نام و یا شماره همراه راننده را وارد نمایید</label>
                            <i class='bx bx-file-find'></i>
                            <input type="text" id="driver" wire:model.live.debounce.1000ms="search">
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="cr-card-body p-0">
            <div class="table-responsive text-center text-nowrap">
                <div wire:loading.class="cr-parent-spinner">
                    {{spinner()}}
                </div>
                @if($this->users->count())
                    <table class="cr-table table">
                        <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام و نام خانوادگی</th>
                            <th>شماره همراه</th>
                            <th>گروه کاربری</th>
                            <th>انتخاب</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($this->users as $user)
                            <tr wire:key="{{$user->id}}">
                                <td>{{$user->id}}</td>
                                <td>
                                    @if($user->level == 2)
                                        {!! levelIcon() !!}
                                    @endif
                                    {{$user->name || $user->lastname ? $user->name.' '.$user->lastname : '-'}}
                                </td>
                                <td>{{$user->mobile}}</td>
                                <td>{{$user->getRoleName()}} - {{$user->getLegalType()}}</td>
                                <td><a class="btn btn-success select-driver" wire:click="selectUser('{{$user->id}}')" data-user-id="{{$user->id}}"><i class='bx bx-select-multiple' ></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <table class="cr-table table">
                        <thead>
                        <th>شناسه</th>
                        <th>نام و نام خانوادگی</th>
                        <th>شماره همراه</th>
                        <th>گروه کاربری</th>
                        <th>انتخاب</th>
                        </thead>
                        <tbody>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td><a href="{{route('d.users.create')}}?submit={{$search}}" class="btn btn-success select-driver"><i class='bx bx-select-multiple'></i></a></td>
                        </tr>
                        </tbody>
                    </table>
                @endif

            </div>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
</div>
