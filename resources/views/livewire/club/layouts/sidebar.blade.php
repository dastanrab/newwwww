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
                    <img src="{{asset('assets/img/Logo.png')}}" alt="" class="img-fluid">
                </figure>
                <div class="cr-menu">
                    <nav>
                        <div class="cr-title">
                            <span>پنل کاربری</span>
                        </div>
                        @if($menu)
                            <ul>
                                @foreach($menu as $item)
                                    @if(isset($item['submenu']) && $item['submenu'])
                                        <li class="cr-dropdown position-relative">
                                            @if(isset($item['count']) && $item['count'] > 0)
                                                <span class="position-absolute top-0 translate-middle p-2 bg-danger border border-light rounded-circle"></span>
                                            @endif
                                            <a class="{{explode('/',request()->route()->uri)[0] == $item['name'] ? 'cr-active' : ''}}">
                                                <i class="{{$item['icon']}}"></i>
                                                <span>{{$item['title']}}</span>
                                            </a>
                                            <ul style="{{explode('/',request()->route()->uri)[0] == $item['name'] ? 'display:block' : ''}}">
                                                @foreach($item['submenu'] as $submenu)
                                                    @php($ex = explode('/',request()->route()->uri))
                                                    <li class="position-relative">
                                                        @if(isset($submenu['count']) && $submenu['count'] > 0) <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger">{{$submenu['count']}}</span>@endif
                                                        <a href="{{$submenu['route']}}" class="{{$ex[0] == $item['name'] && $ex[1] == $submenu['name'] ? 'cr-active' : ''}}">
                                                            <i class="{{$submenu['icon']}}"></i>
                                                            <span>{{$submenu['title']}}</span>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{$item['route']}}" class="{{explode('/',request()->route()->uri)[0] == $item['name'] ? 'cr-active' : ''}}">
                                                <i class="{{$item['icon']}}"></i>
                                                <span>{{$item['title']}}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    </nav>
                </div>
            </div>
        </aside>
    </div>
</div>
