<div class="cr-card">
    <div class="cr-card-header">
        <div class="cr-title">
            <div>
                <strong>سطح های دسترسی</strong>
            </div>
        </div>
    </div>
    <div class="cr-card-body p-0" id="paginated-list">
        <div class="table-responsive text-center text-nowrap">
            <table class="cr-table table">
                <thead>
                <tr>
                    <th>نقش</th>
                    <th>نامک</th>
                    @if(Gate::allows('setting_role_single',App\Livewire\Dashboard\Settings\RolesIndex::class))
                    <th>ویرایش</th>
                    @endif
                </tr>
                </thead>
                <tbody>
                @foreach($roles as $role)
                    <tr>
                        <td>{{$role->label}}</td>
                        <td>{{$role->name}}</td>
                        @if(Gate::allows('setting_role_single',App\Livewire\Dashboard\Settings\RolesIndex::class))
                        <td>
                            <div class="cr-actions">
                                <ul>
                                    <li>
                                        <a href="{{route('d.settings.roles.single',$role->id)}}">
                                            <i class="bx bxs-edit"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="cr-card-footer">
    </div>
</div>
