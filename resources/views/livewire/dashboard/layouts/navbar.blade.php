<div>
    <div class="cr-nav-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-5">
                    {{breadCrumb($breadCrumb)}}
                </div>
                <div class="col-lg-7 col-12">
                    <nav>
                        <div class="cr-actions">
                            <ul class="cr-action">
                                <li>
                                    <div class="cr-responsive">
                                            <span>
                                                <i class="bx bx-menu-alt-left"></i>
                                            </span>
                                    </div>
                                </li>
                                <li>
                                    <div class="cr-user">
                                        <img src="{{asset('assets/img/users/5.png')}}" alt="" class="img-fluid">
                                        <div>
                                            <strong>{{auth()->user()->name.' '.auth()->user()->lastname}}</strong>
                                            <span>{{auth()->user()->getRoleName()}}</span>
                                        </div>
                                        <i class="bx bx-chevron-down"></i>
                                    </div>
                                    <div class="cr-dropmenu">
                                        <ul class="cr-icons">
                                            <li>
                                                <a href="{{route('d.logout')}}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="خروج">
                                                    <i class="bx bxs-log-out-circle"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="{{route('d.users.single',auth()->user()->id)}}" data-bs-toggle="tooltip" data-bs-placement="bottom" title="ویرایش">
                                                    <i class="bx bxs-user-circle"></i>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="تنظیمات">
                                                    <i class="bx bxs-cog"></i>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- firebase commented to firebase.blade.php--}}
