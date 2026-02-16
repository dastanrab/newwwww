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
                            <strong>ویرایش نقش {{$role->label}}</strong>
                        </div>
                    </div>
                </div>
                <form wire:submit.prevent="update">
                    <div class="cr-card-body p-0">
                        <div class="container-fluid">
                            <div class="row">
                                @if(Gate::allows('setting_role_single_edit',App\Livewire\Dashboard\Settings\RolesIndex::class))
                                <div class="col-lg-2 col-md-3 col-12">
                                    <div class="cr-button cr-md mb-3">
                                        <button type="button" wire:click="select">فعال/غیرفعال کردن همه</button>
                                    </div>
                                </div>
                                @endif
                                <div class="clearfix"></div>
                                @foreach($permissions as $i => $permission)
                                    <div class="col-lg-4 col-12">
                                        <div class="cr-checkbox cr-md mb-3">
                                            <input type="checkbox" id="{{$permission->name}}"
                                                   @if(Gate::allows('setting_role_single_edit',App\Livewire\Dashboard\Settings\RolesIndex::class))
                                                   wire:model.defer="permissionSelected"
                                                   @elseif(in_array($permission->id,$permissionSelected->toArray()))
                                                       checked disabled
                                                   @else
                                                       disabled
                                                   @endif
                                                   value="{{$permission->id}}">
                                            <label for="{{$permission->name}}">{{$permission->label.' ('.$permission->name.')'}}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="cr-card-footer">
                        @if(Gate::allows('setting_role_single_edit',App\Livewire\Dashboard\Settings\RolesIndex::class))
                        <div class="col-lg-2 col-md-3 col-12">
                            <div class="cr-button wd">
                                {{button('ویرایش')}}
                            </div>
                        </div>
                        @endif
                    </div>
                </form>
                {{toast($errors)}}
            </div>

            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
