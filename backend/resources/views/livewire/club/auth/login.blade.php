<div>
    <div class="cr-login-section">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-6 d-lg-block d-none p-0">
                    <div class="cr-cover" style="background-image: url({{asset('assets/img/login-cover.jpg')}});">
                        <div class="cr-content animate__animated animate__fadeIn">
                            <figure>
                                <img src="{{asset('assets/img/Logo-White.png')}}" alt="" class="img-fluid">
                            </figure>
                            <div class="cr-carousel">
                                <div id="cr-carousel" class="carousel slide" data-bs-interval="true">
                                    <div class="cr-pagination carousel-indicators">
                                        <button type="button" data-bs-target="#cr-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1">
                                        </button>
                                        <button type="button" data-bs-target="#cr-carousel" data-bs-slide-to="1" aria-label="Slide 2">
                                        </button>
                                        <button type="button" data-bs-target="#cr-carousel" data-bs-slide-to="2" aria-label="Slide 3">
                                        </button>
                                    </div>
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <h1>قهرمان باش و دنیا رو نجات بده!</h1>
                                            <p>آنیروب در راستای ایجاد محیط زیست پایدار و به عنوان راهکاری اثربخش برای آسایش شهروندان گرامی اقدام به ایجاد سیستم موثر و کارآمد در جهت جمع‌آوری و بازیافت پسماند خشک نموده است.</p>
                                        </div>
                                        <div class="carousel-item">
                                            <h1>کمک به محیط زیست پایدار شهری</h1>
                                            <p>مجموعه آنیروب در راستای حفظ و حمایت از محیط‌زیست و تضمین بهره‌مندی صحیح و مستمر از آن، اقدام به راه‌اندازی سیستمی هوشمند در جهت بالا بردن سطح کیفی و ارتقای فرهنگ تفکیک زباله و جمع‌آوری و بازیافت پسماند خشک نموده است.
                                            </p>
                                        </div>
                                        <div class="carousel-item">
                                            <h1>راهکار هوشمند بازیافت</h1>
                                            <p>راهکار هوشمند بازیافت پسماند یک رویکرد نوآورانه است که برای بهبود عملکرد و کارآیی فرآیند بازیافت پسماند استفاده می‌شود.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-12 p-0">
                    <div class="cr-form animate__animated animate__fadeIn">
                        <figure>
                            <a href="#">
                                <img src="{{asset('assets/img/logo.png')}}" alt="" class="img-fluid">
                            </a>
                        </figure>
                        <div class="cr-content">
                            <div class="cr-title">
                                <strong>پنل باشگاه مشتریان</strong>
                                <p>با ورود و یا ثبت نام در آنیروب شما شرایط و قوانین و حریم خصوصی
                                    <br>
                                    استفاده از سرویس های سایت آنیروب را می‌پذیرید.
                                </p>
                            </div>
                            <form wire:submit.prevent="login">
                                <div class="row">
                                    @if($type == 'mobile')
                                        <div class="col-12">
                                            <div class="cr-text cr-icon cr-md mb-3">
                                                <label for="mobile">شماره همراه</label>
                                                <i class="bx bx-user-circle"></i>
                                                <input wire:model="mobile" type="text" id="mobile" placeholder="شماره همراه را وارد نمایید">
                                            </div>
                                        </div>
                                    @elseif($type == 'code')
                                        <div class="col-12">
                                            <div class="cr-text cr-icon cr-md mb-3">
                                                <label for="code">کد تایید</label>
                                                <i class="bx bx-user-circle"></i>
                                                <input wire:model="code" type="text" id="code" placeholder="کدتایید را وارد نمایید">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="cr-button cr-md">
                                    <button>
                                        <span wire:loading.class="cr-hidden">{{$btnText}}</span>
                                        <div class="cr-hidden" wire:loading.class.remove="cr-hidden">
                                            {{spinner()}}
                                        </div>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{toast($errors)}}

</div>
