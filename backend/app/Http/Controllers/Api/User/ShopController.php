<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AsanPardakht;
use App\Models\WalletDetails;
use App\Models\Cache;
use App\Models\City;
use App\Models\Firebase;
use App\Models\Inax;
use App\Models\Wallet;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use Matrix\Exception;

class ShopController extends Controller
{

    public function data()
    {
        $user = auth()->user();
        $aniroobWallet = Wallet::where('user_id', $user->id)->first();
        $apUrl = null;
        $apBalance = 0;
        $ap = AsanPardakht::check($user->mobile);
        if ($ap) {
            if ($ap['status'] == 1) {
                $apBalance = $ap['data'];
            } elseif($ap['status'] == 2) {
                $apUrl = $ap['data'];
            }
        }
        $data = [
            'notice' => 'با استفاده از کیف پول آنیروب و آپ می توانید شارژ و اینترنت بخرید و یا به بنیادهای خیریه کمک کنید.',
            'operators' => [
                [
                    'value' => 'MTN',
                    'label' => 'ایرانسل',
                ],
                [
                    'value' => 'MCI',
                    'label' => 'همراه اول',
                ],
                [
                    'value' => 'RTL',
                    'label' => 'رایتل',
                ]
            ],
            'charge' => [
                'MCI' => [
                    'amountList' => [
                        [
                            'value' => 10000,
                            'label' => tomanFormat('10000'),
                        ],
                        [
                            'value' => 20000,
                            'label' => tomanFormat('20000'),
                        ],
                        [
                            'value' => 50000,
                            'label' => tomanFormat('50000'),
                        ],
                        [
                            'value' => 100000,
                            'label' => tomanFormat('100000'),
                        ],
                    ],
                    'range' => [
                        'from' => 5000,
                        'to' => 200000,
                    ]
                ],
                'MTN' => [
                    'amountList' => [
                        [
                            'value' => 2000,
                            'label' => tomanFormat('2000'),
                        ],
                        [
                            'value' => 5000,
                            'label' => tomanFormat('5000'),
                        ],
                        [
                            'value' => 10000,
                            'label' => tomanFormat('10000'),
                        ],
                        [
                            'value' => 20000,
                            'label' => tomanFormat('20000'),
                        ]
                    ],
                    'range' => [
                        'from' => 1000,
                        'to' => 200000,
                    ]
                ],
                'RTL' => [
                    'amountList' => [
                        [
                            'value' => 2000,
                            'label' => tomanFormat('2000'),
                        ],
                        [
                            'value' => 5000,
                            'label' => tomanFormat('5000'),
                        ],
                        [
                            'value' => 10000,
                            'label' => tomanFormat('10000'),
                        ],
                        [
                            'value' => 20000,
                            'label' => tomanFormat('20000'),
                        ]
                    ],
                    'range' => [
                        'from' => 1000,
                        'to' => 200000,
                    ]
                ],
                "payMethods" => [
                    [
                        'value' => 'aniroob',
                        'label' => 'کیف پول آنیروب',
                    ]
//                    ,
//                    [
//                        'value' => 'aap',
//                        'label' => 'کیف پول آپ',
//                    ]
                ],
            ],
            'internet' => [
                'packages' => Cache::internetPackage(),
                "payMethods" => [
                    [
                        'value' => 'aniroob',
                        'label' => 'کیف پول آنیروب',
                    ]
//                    ,
//                    [
//                        'value' => 'aap',
//                        'label' => 'کیف پول آپ',
//                    ]
                ],
            ],
            'charity' => [
                'charities' => [
                                        [
                        'value' => 'bonyad',
                        'label' => 'بنیاد پیشگیری و کنترل دیابت ایرانیان',
                    ]
//                    [
//                        'value' => 'golestan_ali',
//                        'label' => 'مؤسسه گلستان علی (ع)',
//                    ],
//                    [
//                        'value' => 'shokuhemehr',
//                        'label' => 'مؤسسه بوستان شكوه مهر',
//                    ],
//                    [
//                        'value' => 'absharatefeha',
//                        'label' => 'خیریه آبشار عاطفه‌ها',
//                    ]
                ],
                'amountList' => [
                    [
                        'value' => 10000,
                        'label' => tomanFormat('10000'),
                    ],
                    [
                        'value' => 20000,
                        'label' => tomanFormat('20000'),
                    ],
                    [
                        'value' => 50000,
                        'label' => tomanFormat('50000'),
                    ],
                    [
                        'value' => 100000,
                        'label' => tomanFormat('100000'),
                    ],
                ],
                'range' => [
                    'from' => 1000,
                    'to' => 10000000,
                ],
                "payMethods" => [
//                    [
//                        'value' => 'aap',
//                        'label' => 'کیف پول آپ',
//                    ]
                ],
            ],
//            'aapLink' => $apUrl,
            'AniRoobBalance' => floor($aniroobWallet->wallet),
//            'aapBalance' => $apUrl ? -1 : $apBalance
        ];
        return $data;
    }

    public function index()
    {
        $data = $this->data();
        return sendJson('success','', $data);
    }

    public function charge(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'operator'  => 'required|string',
                'mobile'    => 'required|regex:/(09)[0-9]{9}/|digits:11|numeric',
                'amount'    => 'required|numeric|min:1000',
                'payMethod' => 'required|in:aniroob',
            ],
            [
                'operator' => 'اپراتور را انتخاب کنید',
                'mobile' => 'شماره همراه را به درستی وارد نمایید',
                'amount' => 'حداقل مبلغ ۱,۰۰۰ تومان می باشد',
                'payMethod' => 'روش پرداخت را انتخاب کنید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $amount = $request->amount; //toman
        $city_id = $user->city->id;
        if ($request->payMethod == 'aap') {
            $apBalance = AsanPardakht::balance($user->mobile);
            if($apBalance === false){
                return sendJson('error', 'در حین اتصال به آپ اشکالی به وجود آمد لطفا دوباره امتحان کنید');
            }
            elseif ($apBalance === -1){
                return sendJson('error', 'لطفا کیف پول آپ خود را به آنیروب وصل کنید');
            }
            else{
                $balance = $apBalance;
            }

        } elseif ($request->payMethod == 'aniroob') {
            $wallet = Wallet::where('user_id', $user->id)->first();
            $balance = $wallet->wallet;
        }
        if($balance < $amount){
            return sendJson('error', 'موجودی شما کافی نمی باشد');
        }
        $request->payMethod='aniroob';
        $method = 'topup';
        $chargeType = 'normal';
        $orderId = Inax::addChargeRecord($user->id,$request->amount,$request->operator,$request->mobile,$method,$request->payMethod,$chargeType);
        dump('order_id');
        dump($orderId);
        if($orderId > 0){
            $inax = Inax::where('order_id',$orderId)->first();
            if ($request->payMethod == 'aap') {
                try {
                    $is_okay=false;
                    $buy = AsanPardakht::decrease($user->mobile, $amount * 10);
                    $buy_hresp = json_decode($buy['hresp']);
                    if($buy_hresp->st == 0){
                        $inax->update(['status' => 'pendingVerifyDecreaseCredit']);
                        $buy_varify = AsanPardakht::verify($buy_hresp->htran, $buy_hresp->htime, $buy_hresp->ao, $buy_hresp->stkn);
                        $buy_varify = json_decode($buy_varify['hresp']);
                        if($buy_varify->st == 0){
                            $inax->update(['status' => 'pendingSubmitRecordDb']);
                            $buy_inax = AsanPardakht::addInaxChargeRecord($user->id,$city_id,$inax->id,$buy_hresp);
                            if($buy_inax) {
                                $is_okay=true;
                                $inax->update(['status' => 'done']);
                            }
                        }
                    }
                    if (!$is_okay)
                    {
                        Inax::where('order_id',$orderId)->delete();
                        return sendJson('error', 'خطایی پیش آمد لطفا بعدا امتحان کنید');
                    }

                } catch (Exception $e) {
                    return sendJson('error', 'خطایی پیش آمد لطفا بعدا امتحان کنید');
                }
            }
            else {
                DB::transaction(function () use ($amount, $inax, $user, $city_id) {
                    $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                    if (!$wallet) {
                        throw new \Exception('کیف پول یافت نشد');
                    }
                    if ($wallet->wallet < $amount) {
                        throw new \Exception('موجودی کافی نیست');
                    }
                    $wallet->wallet -= $amount;
                    $wallet->save();
                    $inax->update(['status' => 'pendingSubmitRecordDb']);
                    $bazistWallet = WalletDetails::create($city_id, $user->id, $wallet->id, 'sharj_mobile', $inax->id, $amount * 10, ($wallet->wallet)*10,'برداشت','شارژ موبایل');
                    if ($bazistWallet) {
                        $inax->update(['status' => 'done']);
                    } else {
                        throw new \Exception('ثبت تراکنش در کیف پول با خطا مواجه شد');
                    }
                });

                    $wallet->wallet -= $amount;
                    $wallet->save();
                    $inax->update(['status' => 'pendingSubmitRecordDb']);
                    $Wallet = WalletDetails::create($city_id, $user->id, $wallet->id, 'sharj_mobile', $inax->id, $amount * 10, ($wallet->wallet)*10,'برداشت','شارژ موبایل');
                    if($Wallet){
                        $inax->update(['status' => 'done']);
                    }
            }
//            $result = Inax::buyCharge($method,$request->operator,$request->amount,$request->mobile,$chargeType,$orderId);
            // {"code":1,"trans_id":"2062909","ref_code":"148171913"}
            $result = [
                'code' => 1,
                'trans_id' => rand(1321,454354),
                'ref_code' => rand(4233242,543543345),
                'msg' => 'با موفقیت انجام شد',
            ];
            if($result['code'] === 1){
//                dump('ok');

                $inax = Inax::where('order_id',$orderId)->first();
//                $inax->update(['status' => 'pendingDecreaseCredit', 'ref_code' => $result['ref_code'], 'trans_id' => $result['trans_id']]);
                 $inax->update(['ref_code' => $result['ref_code'], 'trans_id' => $result['trans_id']]);
                $data = [
                    'transId' => $result['trans_id'],
                    'orderId' => $orderId,
                    'balance' => [
                        'aniroob' => $wallet->wallet,
//                        'aap'    => AsanPardakht::balance($user->mobile),
                    ]
                ];
//                dump($data);
                return sendJson('success','خرید شارژ با موفقیت انجام شد', $data);
            }
            else{
//                 dump('fail');
                Inax::where('order_id',$orderId)->update(['status' => 'cancel', 'description' => $result['msg']]);
                $data = [
                    'transId' => null,
                    'orderId' => $orderId,
                ];
                DB::transaction(function () use ($amount, $user, $city_id) {
                    $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                    WalletDetails::create(
                        $city_id,
                        $user->id,
                        $wallet->id,
                        'back_mobile',
                        $wallet->id,
                        $amount * 10, // Rial
                        ($wallet->wallet + $amount)*10,
                        'واریز',
                        'برگشت شارژ موبایل کنسل شده به کیف پول کاربر '
                    );
                    $wallet->wallet += $amount; // Toman
                    $wallet->save();
                });
                $message = Inax::errors($result['code']) ?? $result['msg'];
                return sendJson('error', $message, $data);
            }
        }
        elseif ($orderId === -1){
            $data = [
                'transId' => null,
                'orderId' => null,
            ];
            return sendJson('error', 'خرید شارژ با اشکال روبرو شد لطفا دقایقی دیگر امتحان کنید (1003)',$data);
        }
        elseif ($orderId === -2){
            $data = [
                'transId' => null,
                'orderId' => null,
            ];
            return sendJson('error', 'خرید شارژ با اشکال روبرو شد لطفا دقایقی دیگر امتحان کنید (1004)', $data );
        }
    }


    public function internet(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(),
            [
                'productId'    => 'required',
                'operator'     => 'required',
                'mobile'       => 'required|regex:/(09)[0-9]{9}/|digits:11|numeric',
                'internetType' => 'required',
                'simType'      => 'required',
                'payMethod'    => 'required|in:aniroob',
            ],
            [
                'productId'    => 'اینترنت درخواستی پیدا نشد',
                'operator'     => 'اپراتور به درستی انتخاب نشده است',
                'mobile'       => 'شماره همراه را به درستی وارد نمایید',
                'internetType' => 'نوع اینترنت به درستی انتخاب نشده است',
                'simType'      => 'نوع اعتباری و یا دائمی بودن سیم کارت انتخاب نشده است',
                'payMethod'    => 'روش پرداخت را انتخاب کنید'
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $findPackage = Inax::findInternetPackage($request->productId,$request->operator,$request->internetType,$request->simType);

        if(!$findPackage){
            return sendJson('error', 'اینترنت مطابق با درخواست شما یافت نشد');
        }
        $request->payMethod == 'aniroob';
        $amount = $findPackage['amount']['value']; //toman
        $city_id = $user->city->id;
        if ($request->payMethod == 'aap') {
            $apBalance = AsanPardakht::balance($user->mobile);
            if($apBalance === false){
                return sendJson('error', 'در حین اتصال به آپ اشکالی به وجود آمد لطفا دوباره امتحان کنید');
            }
            elseif ($apBalance === -1){
                return sendJson('error', 'لطفا کیف پول آپ خود را به آنیروب وصل کنید');
            }
            else{
                $balance = $apBalance;
            }
        } elseif ($request->payMethod == 'aniroob') {
            $wallet = Wallet::where('user_id', $user->id)->first();
            $balance = $wallet->wallet;
        }
        if($balance < $amount){
            return sendJson('error', 'موجودی شما کافی نمی باشد');
        }
        $method = 'internet';
        $orderId = Inax::addInternetRecord($user->id,$amount,$request->operator,$request->mobile,$method,$request->payMethod,$request->internetType);
        if($orderId > 0){
            $inax = Inax::where('order_id',$orderId)->first();
            if ($request->payMethod == 'aap') {
                try {
                    $buy = AsanPardakht::decrease($user->mobile, $amount * 10);
                    $buy_hresp = json_decode($buy['hresp']);
                    if($buy_hresp->st == 0){
                        $inax->update(['status' => 'pendingVerifyDecreaseCredit']);
                        $buy_varify = AsanPardakht::verify($buy_hresp->htran, $buy_hresp->htime, $buy_hresp->ao, $buy_hresp->stkn);
                        $buy_varify = json_decode($buy_varify['hresp']);
                        if($buy_varify->st == 0){
                            $inax->update(['status' => 'pendingSubmitRecordDb']);
                            $buy_inax = AsanPardakht::addInaxInternetRecord($user->id,$city_id,$inax->id,$buy_hresp);
                            if($buy_inax) {
                                $inax->update(['status' => 'done']);
                            }
                        }
                    }
                } catch (\Exception $e) {
                    return sendJson('error', 'خطایی پیش آمد لطفا بعدا امتحان کنید');
                }
            }
            else {
                // bazist wallet
                DB::transaction(function () use ($amount, $inax, $user, $city_id) {
                    $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                    if (!$wallet) {
                        throw new \Exception('کیف پول یافت نشد');
                    }
                    if ($wallet->wallet < $amount) {
                        throw new \Exception('موجودی کافی نیست');
                    }
                    $wallet->wallet -= $amount;
                    $wallet->save();
                    $inax->update(['status' => 'pendingSubmitRecordDb']);
                    $bazistWallet = WalletDetails::create($city_id, $user->id, $wallet->id, 'sharj_internet', $inax->id, $amount * 10, ($wallet->wallet)*10, 'برداشت', 'شارژ اینترنت');
                    if($bazistWallet){
                        $inax->update(['status' => 'done']);
                    }
                    else {
                        throw new \Exception('ثبت تراکنش در کیف پول با خطا مواجه شد');
                    }
                });

            }
//            $result = Inax::buyInternet($request->productId,$request->operator,$request->mobile,$request->internetType,$request->simType,$orderId);
            // {"code":1,"trans_id":"2062909","ref_code":"148171913"}
            $result = [
                'code' => 1,
                'trans_id' => rand(1321,454354),
                'ref_code' => rand(4233242,543543345),
                'msg' => 'با موفقیت انجام شد',
            ];
            if($result['code'] === 1){
                $inax = Inax::where('order_id',$orderId)->first();
                $inax->update(['ref_code' => $result['ref_code'], 'trans_id' => $result['trans_id']]);
                $data = [
                    'transId' => $result['trans_id'],
                    'orderId' => $orderId,
                    'balance' => [
                        'aniroob' => $wallet->wallet,
                       // 'aap'    => AsanPardakht::balance($user->mobile),
                    ]
                ];
                return sendJson('success','خرید اینترنت با موفقیت انجام شد', $data);
            }
            else{
                Inax::where('order_id',$orderId)->update(['status' => 'cancel', 'description' => $result['msg']]);
                $data = [
                    'transId' => null,
                    'orderId' => $orderId,
                ];
                DB::transaction(function () use ($amount, $user, $city_id) {
                    $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                    WalletDetails::create(
                        $city_id,
                        $user->id,
                        $wallet->id,
                        'back_internet',
                        $wallet->id,
                        $amount * 10, // Rial
                        ($wallet->wallet + $amount)*10,
                        'واریز',
                        'برگشت شارژ اینترنت کنسل شده به کیف پول کاربر '
                    );
                    $wallet->wallet += $amount; // Toman
                    $wallet->save();
                });
                $error = Inax::errors($result['code']);
                return sendJson('error', $error ?? $result['msg'],$data);
            }
        }
        elseif ($orderId === -1){
            $data = [
                'transId' => null,
                'orderId' => null,
            ];
            return sendJson('error', 'خرید شارژ با اشکال روبرو شد لطفا دقایقی دیگر امتحان کنید (1003)',$data);
        }
        elseif ($orderId === -2){
            $data = [
                'transId' => null,
                'orderId' => null,
            ];
            return sendJson('error', 'خرید شارژ با اشکال روبرو شد لطفا دقایقی دیگر امتحان کنید (1004)', $data );
        }

    }

    public function charity(Request $request)
    {
        $user = auth()->user();
        $data = $this->data();
        $validator = Validator::make($request->all(),
            [
                'charity'   => 'required|in:'.collect($data['charity']['charities'])->pluck('value')->implode(','),
                'amount'    => 'required|numeric|min:'.$data['charity']['range']['from'].',max:'.$data['charity']['range']['to'],
                'payMethod' => 'required|in:'.collect($data['charity']['payMethods'])->pluck('value')->implode(','),
            ],
            [
                'charity'    => 'خیریه را انتخاب کنید',
                'amount.min' => 'حداقل مبلغ '.tomanFormat($data['charity']['range']['from']).' می باشد',
                'amount.max' => 'حداکثر مبلغ '.tomanFormat($data['charity']['range']['to']).' می باشد',
                'payMethod' => 'روش پرداخت را انتخاب کنید',
            ]
        );
        if($validator->fails()){
            return sendJson('error',$validator->errors()->first());
        }
        $request->payMethod='aniroob';
        $amount = $request->amount; //toman
        $charity = $request->charity;
        $city_id = $user->city->id;
        if ($request->payMethod == 'aap') {
            $apBalance = AsanPardakht::balance($user->mobile);
            if($apBalance === false){
                return sendJson('error', 'در حین اتصال به آپ اشکالی به وجود آمد لطفا دوباره امتحان کنید');
            }
            elseif ($apBalance === -1){
                return sendJson('error', 'لطفا کیف پول آپ خود را به آنیروب وصل کنید');
            }
            else{
                $balance = $apBalance;
            }
        } elseif ($request->payMethod == 'aniroob') {
            $wallet = Wallet::where('user_id', $user->id)->first();
            $balance = $wallet->wallet;
        }
        if($balance < $amount){
            return sendJson('error', 'موجودی شما کافی نمی باشد');
        }
        $charityName = collect($data['charity']['charities'])->filter(function ($item) use($charity){
            return $item['value'] == $charity;
        })->first()['value'];
        $charitylabel = collect($data['charity']['charities'])->filter(function ($item) use($charity){
            return $item['value'] == $charity;
        })->first()['label'];
        if ($request->payMethod == 'aap') {
            try {
                $decrease = AsanPardakht::decrease($user->mobile, $amount * 10);
                $decrease_hresp = json_decode($decrease['hresp']);
                if($decrease_hresp->st == 0){
                    $buy_verify = AsanPardakht::verify($decrease_hresp->htran, $decrease_hresp->htime, $decrease_hresp->ao, $decrease_hresp->stkn);
                    $buy_verify = json_decode($buy_verify['hresp']);
                    if($buy_verify->st == 0){
                        $order = AsanPardakht::addCharityWithdrawRecord($decrease_hresp,$charityName,$city_id);
                        $increase = AsanPardakht::increase(env($charityName), $amount * 10);
                        $increase_hresp = json_decode($increase['hresp']);
                        if($increase_hresp->st == 0) {
                            $charity_verify = AsanPardakht::verify($increase_hresp->htran, $increase_hresp->htime, $increase_hresp->ao, $increase_hresp->stkn);
                            $charity_verify = json_decode($charity_verify['hresp']);
                            if($charity_verify->st == 0){
                                AsanPardakht::addCharityDepositRecord($increase_hresp,$charityName,$city_id);
                            }
                        }
                    }
                    $bazistWallet = Wallet::where('user_id', $user->id)->first();
                    $data = [
                        'transId' => $decrease_hresp->rrn,
                        'orderId' => $order->id,
                        'balance' => [
                            'aniroob' => $bazistWallet->wallet,
                            'aap'    => AsanPardakht::balance($user->mobile),
                        ]
                    ];
                    $date=verta()->format('Y-m-d');
                    $msg = [
                        'title' => 'پیام از پشتیبانی',
                        'message' => "ضمن سپاس از همراهی ارزشمند شما شهروند گرامی
                        درتاریخ {$date} مبلغ {$amount} از کیف پول شما کسر و به حساب خیریه {$charitylabel} پرداخت گردید جزییات این تراکنش از بخش سوابق تراکنش قابل مشاهده و پیگیری می باشد",
                    ];
                  //  Notification::send($user, new UserNotification(Firebase::dataFormat($msg)));
                    return sendJson('success', 'کمک شما به خیریه انجام شد',$data);
                }
            } catch (Exception $e) {
                return sendJson('error', 'خطایی پیش آمد لطفا بعدا امتحان کنید');
            }
        }


    }

    public function transactions()
    {
        $user = auth()->user();
        $paginate = 10;
        $data = ['list' => [], 'limit' => $paginate];
        $orders = $user->inaxes()->where('status','done')->latest()->paginate($paginate);
        foreach ($orders as $order) {
            $type = '';
            if($order->method == 'topup'){
                $type = 'شارژ';
            }
            elseif($order->method == 'internet'){
                $type = 'اینترنت';
            }
            $phone = $order->mobile ? ' - '.$order->mobile : '';
            $data['list'][] = [
                'id'       => $order->id,
                'details' => $type.' '.Inax::getOperator($order->operator).$phone,
                "date" => [
                    "day" => verta()->instance($order->created_at)->format('Y/m/d'),
                    "time" => verta()->instance($order->created_at)->format('H:i'),
                ],
                "amount" => $order->amount
            ];
        }
        return sendJson('success','',$data);
    }

}
