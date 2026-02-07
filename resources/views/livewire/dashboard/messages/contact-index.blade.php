@php use Hekmatinasser\Verta\Verta; @endphp
<div>
    <div class="cr-overlay"></div>
    <div class="cr-layout-section">
        <livewire:dashboard.layouts.sidebar />
        <livewire:dashboard.layouts.navbar :$breadCrumb />

        <div class="cr-container-section">
            <livewire:dashboard.messages.contact-index-nav/>
            <div class="cr-card">
                <div class="cr-card-header">
                    <div class="cr-title">
                        <div>
                            <strong>لیست پیام ها</strong>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body p-0" id="paginated-list">
                    <div class="table-responsive text-center text-nowrap">
                        <div wire:loading>
                            {{spinner()}}
                        </div>
                        <table class="cr-table table">
                            <thead>
                            <tr>
                                <th>کد پیگیری</th>
                                <th>نام</th>
                                <th>موبایل</th>
                                <th>موضوع</th>
                                <th>آیپی</th>
                                <th>تاریخ ارسال پیام</th>
                                <th>تاریخ آخرین پاسخ</th>
                                @if(Gate::allows('contact_single',App\Models\Contact::class))
                                    <th>پاسخ</th>
                                @endif
                            </tr>
                            </thead>
                            @if($this->contacts->count())
                            <tbody>
                            @foreach($this->contacts as $contact)
                                @php $user = \App\Models\User::find($contact->user_id);@endphp
                                <tr class="@if(!$contact->admin_seen_at) cr-unread @endif">
                                    <td>{{$contact->id}}</td>
                                    @if($user)
                                        <td><a href="{{route('d.users.single',$user->id)}}">{{$user->name.' '.$user->lastname}}</a></td>
                                        <td>{{$user->mobile}}</td>
                                    @else
                                        <td>-</td>
                                        <td>-</td>
                                    @endif
                                    <td>{{ $contact->subject }}</td>
                                    <td>{{ $contact->ip }}</td>
                                    <td>{{ Verta::instance($contact->created_at)->format('Y/m/d H:i') }}</td>
                                    <td>{{ isset($contact->contactReplies[0]->created_at)?Verta::instance($contact->contactReplies[0]->created_at)->format('Y/m/d H:i'):'-' }}</td>
                                    @if(Gate::allows('contact_single',App\Models\Contact::class))
                                        <td>
                                            <a href="{{route('d.contacts.single',$contact->id)}}" class="cr-edit">
                                                <i class="bx bxs-message-square-edit"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                            @endif
                        </table>
                    </div>
                </div>
                <div class="cr-card-footer">
                    {{$this->contacts->links(data: ['scrollTo' => '#paginated-list'])}}
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
</div>
