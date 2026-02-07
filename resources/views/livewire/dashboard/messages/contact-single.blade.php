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
                            <strong>{{ $contact->subject }}</strong>
                        </div>
                        <div class="cr-actions">
                            <label class="dir-ltr">{{ \Verta::instance($contact->created_at)->format('Y/n/j H:i') }}</label>
                        </div>
                    </div>
                </div>
                <div class="cr-card-body">
                    @php
                    $requester = \App\Models\User::find($contact->user_id);
                    @endphp
                    <form wire:submit.prevent="store('{{$contact->id}}')">
                        <div class="alert alert-light" role="alert">
                            {{$requester->name.' '.$requester->lastname.' (کاربر):'}}  {{$contact->message}}
                            <div>
                                <label class="dir-ltr">{{ \Verta::instance($contact->created_at)->format('Y/n/j H:i') }}</label>
                            </div>
                        </div>
                        @if($replies)
                            @foreach($replies as $reply)
                                <div class="alert {{$reply->user->getRole('name') == 'user' ? 'alert-light' : 'alert-dark'}}" role="alert">
                                   {{$reply->user->name.' '.$reply->user->lastname}} {{$reply->user->getRole('name') == 'user' ? '(کاربر):' : '(پشتیبان):'}} {{$reply->message}}

                                    <div>
                                        <label class="dir-ltr">{{ \Verta::instance($reply->created_at)->format('Y/n/j H:i') }}</label>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        @if(Gate::allows('contact_single_edit',App\Models\Contact::class))
                            <div class="cr-textarea">
                                <textarea name="" id="" cols="30" rows="10" wire:model="message"></textarea>
                            </div>
                             <div class="cr-button">
                                 {{button('ارسال')}}
                             </div>
                        @endif
                    </form>
                </div>
            </div>
            <livewire:dashboard.layouts.footer/>
        </div>
    </div>
{{toast($errors)}}
</div>
