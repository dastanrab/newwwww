<div>
    <div class="cr-sidebar-section">
        <div class="cr-close">
            <i class="bx bx-x"></i>
        </div>
        <div class="cr-sidebar">
                <span>
                    <i class="bx bx-chevron-left"></i>
                </span>
        </div>
        <aside>
            <div class="cr-primary">
                <figure>
                    <img src="{{asset('assets/img/logo.png')}}" alt="" class="img-fluid">
                </figure>
                <div class="cr-menu">
                    <nav>
                        <div class="cr-title">
                            <span>پنل مدیریت آنیروب</span>
                        </div>
                        @if($can_see) <div class="cr-primary">
                            <div class="cr-title">
                                <span>شهر</span>
                            </div>
                            <div class="mx-3">
                                <div class="cr-select" >
                                    <select id="city" wire:model.live="city">
                                        <option value="0">همه</option>
                                        <option value="1">مشهد</option>
                                        <option value="3">طرقبه</option>
                                    </select>
                                </div>
                                {{--                                <label class="switch">--}}
                                {{--                                    <input type="checkbox" id="togBtn" wire:model.live="isMashhad">--}}
                                {{--                                    <div class="slider round"><!--ADDED HTML -->--}}
                                {{--                                        <span class="on">مشهد</span>--}}
                                {{--                                        <span class="off">طرقبه</span><!--END-->--}}
                                {{--                                    </div>--}}
                                {{--                                </label>--}}
                            </div>
                        </div>
                        <br> @endif

                        @if($menu)
                            <ul>
                                @foreach($menu as $item)
                                    @if(isset($item['submenu']) && $item['submenu'] && $permissions->contains($item['permission']))
                                        <li class="cr-dropdown position-relative">
                                            @if(isset($item['count']) && $item['count'] > 0)
                                                <span class="position-absolute top-0 translate-middle p-2 bg-danger border border-light rounded-circle"></span>
                                            @endif
                                            <a class="{{explode('/',request()->route()->uri)[1] == $item['name'] ? 'cr-active' : ''}}">
                                                <i class="{{$item['icon']}}"></i>
                                                <span>{{$item['title']}}</span>
                                            </a>
                                            <ul style="{{explode('/',request()->route()->uri)[1] == $item['name'] ? 'display:block' : ''}}">
                                                @foreach($item['submenu'] as $submenu)
                                                    @if($permissions->contains($submenu['permission']))
                                                        @php($ex = explode('/',request()->route()->uri))
                                                    <li class="position-relative">
                                                        @if(isset($submenu['count']) && $submenu['count'] > 0) <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger">{{$submenu['count']}}</span>@endif
                                                        <a href="{{$submenu['route']}}" class="{{$ex[1] == $item['name'] && $ex[2] == $submenu['name'] ? 'cr-active' : ''}}">
                                                            <i class="{{$submenu['icon']}}"></i>
                                                            <span>{{$submenu['title']}}</span>
                                                        </a>
                                                    </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </li>
                                    @elseif($permissions->contains($item['permission']))
                                        <li>
                                            <a href="{{$item['route']}}" class="{{explode('/',request()->route()->uri)[1] == $item['name'] ? 'cr-active' : ''}}">
                                                <i class="{{$item['icon']}}"></i>
                                                <span>{{$item['title']}}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </nav>
                    <nav>
                        <div class="cr-title">
                            <span>عمومی</span>
                        </div>
                        <ul>
                            <li>
                                <a href="{{env('SITE_URL')}}" target="_blank">
                                    <i class="bx bx-recycle"></i>
                                    <span>وب سایت بازیست</span>
                                </a>
                            </li>
                            <li>
                                <a href="https://wmo.mashhad.ir" target="_blank">
                                    <i class="bx bxs-buildings"></i>
                                    <span>سازمان مدیریت پسماند</span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </aside>
    </div>
    <script>
        $(document).on('change','#city',function (){
            Livewire.dispatch('city', {city: $(this).val()})
        })
    </script>
</div>
