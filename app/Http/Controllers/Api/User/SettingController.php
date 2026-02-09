<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Contact;
use App\Models\Fava;
use App\Models\Guild;
use App\Models\Submit;
use Illuminate\Http\Request;

class SettingController extends Controller
{

    public function index()
    {
        return sendJson('success', '', $this->data());
    }


    public static function data($user = null)
    {
        if(!$user){
            $user = auth()->user();
        }
        $reward = userRewardToman();
        $minAmountCard = minWithdrawCardToCardToman();
        $minAmountAap = minWithdrawAapToman();
        $wage = wageToman();
        $currentRequest = $user->currentSubmit();

        $guilds = Guild::all();
        foreach ($guilds as $guild){
            $guildMarket[] = [
                'value' => $guild->id,
                'label' => $guild->title,
            ];
        }
        $reviewRequest = $user->submits()->where('status',3)->latest()->first();
        $reviewRequestId = null;
        if($reviewRequest && $reviewRequest->survey === null){
            $reviewRequestId = $reviewRequest->id;
        }
        $gender = null;
        if($user->gender){
            $gender = $user->gender == 2 ? 'female' : 'male';
        }
        $data = [
            'user' => [
                'registered'       => $user->isRegistered(), // if false -> profile page will be shown
                'id'               => $user->id,
                'userType'         => $user->getLegalName(),
                'guildTitle'       => $user->guild_title??'سایر',
                'guildMarket'      => $user->legal == 1 ? $user->guild->id??10 : null,
                'birthDate'        => $user->birthday ? verta()->instance($user->birthday)->format('Y/m/d') : null,
                'firstName'        => $user->name,
                'lastName'         => $user->lastname,
                'mob'              => $user->mobile,
                'email'            => $user->email,
                'gender'           => $gender,
                'referralCode'     => $user->referral_code,
                'selfReferral' => [
                    'text' => 'با استفاده از کد و یا لینک زیر می توانید اپلیکیشن آنیروب را به دوستان خود معرفی کرده و از مزایای معرفی بهره مند شوید',
                    'code' => $user->referral(),
                    'link' => 'https://bazistco.com/ref/?code='.$user->referral(),
                ],
                'city'             => $user->city_id,
                'balance'          => $user->wallets()->first() ? floor($user->wallets()->first()->wallet) : 0
            ],
            "badgeCounters" => [
                "messages" => 0,
                "tickets"  => Contact::where('user_seen_at',null)->where('user_id',$user->id)->count(),
            ],
            "cities" => [
                [ 'id' => 1, 'name' => 'مشهد', 'defaultLocation' => [ 'lat' => 36.298982, 'lng' => 59.605362 ] ],
            ],
            'currentRequest'   => $currentRequest,
            "cached"           => [
                'homeElements' => [
                    'slider' => [
                        [
                            'image'  => 'https://bazistco.com/wp-content/uploads/2024/11/2.png',
                            'target' => 'external',
                            'path'   => Fava::surveyLink($user->fava_id,$user->mobile), //survey link
                        ],
                        [
                            'image'  => 'https://bazistco.com/wp-content/uploads/2024/11/1.png',
                            'target' => 'external',
                            'path'   => 'https://iwms.mashhad.ir/#/propertyTax',
                        ]
                    ],
                    'actionButtons' => [
                        [
                            'icon' => 's-wallet',
                            'title' => 'کیف پول',
                            'target' => 'internal',
                            'path' => '/wallet',
                        ],
                        [
                            'icon' => 's-shopping-bag-alt',
                            'title' => 'فروشگاه',
                            'target' => 'internal',
                            'path' => '/shop',
                        ],
                        [
                            'icon' => 's-chart',
                            'title' => 'قیمت پسماندها',
                            'target' => 'internal',
                            'path' => '/prices',
                        ],
                        [
                            'icon' => 's-share',
                            'title' => 'معرفی به دوستان',
                            'target' => 'internal',
                            'path' => '/suggest',
                        ]
                    ]
                ],
                'guildMarketList' => $guildMarket,
                "apiTimeToLives" => [
                    "addresses" => 10 * 60, // 600 sec
                ],
                "sidebar"        => [
                    "menuItems" => [
                        [
                            "iconName" => "home",
                            "name"     => "home",
                            "text"     => "خانه",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/",
                        ],
                        [
                            "iconName" => "user",
                            "name"     => "profile",
                            "text"     => "پروفایل کاربری",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/user/profile",
                        ],
                        [
                            "iconName" => "map",
                            "name"     => "addresses",
                            "text"     => "آدرس ها",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/addresses",
                        ],
                        [
                            "iconName" => "basket",
                            "name"     => "requests",
                            "text"     => "درخواست ها",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/requests",
                        ],
                        [
                            "iconName" => "wallet",
                            "name"     => "wallet",
                            "text"     => "کیف پول",
                            "badge"    => 15000,
                            "type"     => "internal",
                            "path"     => "/wallet",
                        ],
                        [
                            "iconName" => "hash",
                            "name"     => "rules",
                            "text"     => "قوانین و مقررات",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/rules",
                        ],
                        [
                            "iconName" => "check-shield",
                            "name"     => "privacy",
                            "text"     => "حریم خصوصی",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/privacy",
                        ],
                        [
                            "iconName" => "chart",
                            "name"     => "prices",
                            "text"     => "قیمت پسماندها",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/prices",
                        ],
                        [
                            "iconName"  => "support",
                            "name"      => "tickets",
                            "text"      => "پشتیبانی",
                            "badgeLink" => 'tickets',
                            "type"      => "internal",
                            "path"      => "/tickets",
                        ],
                        [
                            "iconName" => "s-shopping-bag-alt",
                            "name"     => "shop",
                            "text"     => "فروشگاه",
                            "badge"    => 0,
                            "type"     => "internal",
                            "path"     => "/shop",
                        ],
                    ]
                ],
                "statusList"     => Submit::statusList(),
                "withdrawalSettings" => [
                    'card' => [
                        "hintItems" => [
                            'حداقل مبلغ برای برداشت، '.tomanFormat($minAmountCard).' می باشد.',
                            'این مبلغ طی 24 ساعت کاری به حساب شما واریز می شود.',
                        ],
                        "wage" => $wage, //کارمزد
                        "minAmount" => $minAmountCard, //حداقل مبلغ قابل برداشت
                    ],
//                    'aap' => [
//                        "hintItems" => [
//                            'حداقل مبلغ برای برداشت، '.tomanFormat($minAmountAap).' می باشد.',
//                            'این مبلغ در سریع ترین زمان ممکن برای شما واریز می شود',
//                        ],
//                        "wage" => $wage, //کارمزد
//                        "minAmount" => $minAmountAap, //حداقل مبلغ قابل برداشت
//                    ],
                    'amountList' => [
                        [
                            'label' => number_format(5000),
                            'value' => 5000,
                        ],
                        [
                            'label' => number_format(10000),
                            'value' => 10000,
                        ],
                        [
                            'label' => number_format(20000),
                            'value' => 20000,
                        ],
                        [
                            'label' => number_format(50000),
                            'value' => 50000,
                        ],
                        [
                            'label' => 'کل موجودی',
                            'value' => -1,
                        ]
                    ],

                ],
                "paymentMethods" => /*[
                    [
                        "value" => "card",
                        "label" => "کارت به کارت"
                    ],
                    [
                        "value" => "aap",
                        "label" => "کیف پول آپ"
                    ],

                    [
                        "value" => "bazist",
                        "label" => "کیف پول آنیروب"
                    ],

                ]*/null,
                "wallet" => [
                    "notice" => "با اولین درخواست موفق ".tomanFormat($reward)." پاداش دریافت کنید."
                ],
                'reviewSettings' => [
                    'title'   => 'نظرسنجی',
                    'content' => 'لطفا نظر خود را در رابطه با این درخواست با ما درمیان بگذارید',
                    'tips' => [
                        [
                            'label' => 'نقاط قوت',
                            'value' => 'good',
                            'options' => [
                                'سرعت عمل مناسب',
                                'برخورد محترمانه',
                                'وزن کشی دقیق',
                                'وقت شناسی',
                                'خودرو تمیز و مرتب',
                                'رعایت مسائل بهداشتی',
                            ],
                        ],
                        [
                            'label' => 'نقاط ضعف',
                            'value' => 'bad',
                            'options' => [
                                'سرعت عمل نامناسب',
                                'عدم رعایت مسائل بهداشتی',
                                'بی دقتی در وزن کردن',
                                'ارائه موبایل شخصی راننده',
                                'تاخیر در جمع آوری',
                                'پیشنهاد تسویه نقدی راننده',
                                'خودروی کثیف',
                                'عدم استفاده از ماسک',
                                'پوشش ظاهری نامناسب',
                            ]
                        ]
                    ]
                ],
                'supportPhone' => '-'
            ],
            'versioning' => self::versioning(),
            'updateServiceTTL' => env('APP_ENV') == 'local' ? 10 :  20,
            'review' => $reviewRequestId ? [
                'requestId' => $reviewRequestId,
                'mandatory' => true,
            ] : null,
            'favaSurvey' => Fava::surveyLink($user->fava_id,$user->mobile),
            'clubEnabled' => in_array($user->id,[2054,67775]) ? true : false
        ];
        return collect($data);
    }

    public static function versioning()
    {
        $currentVer = '3.2.1';
        $versioning = [
            'android' => [
                'currentVersion' => $currentVer,
                'targets' => [
                    // این سناریو فقط روی کاربرانی که ورژن اپشون بیشتر مساوری با 3.0.0 باشه، اعمال می شه.
                    [
                        'minVersion' => '3.2.0',
                        'method' => 'OTA_FORCE', // OTA, OTA_FORCE, STORE, STORE_FORCE
                        'otaUrl' => 'https://bazistco.com/appdl/ota/user/com.bazistco.bazist_'.$currentVer.'.zip',
                        'dialog' => [
                            'title' => 'بروزرسانی برنامه',
                            'message' => 'برای ارائه خدمات بهتر نیاز هست که حتما برنامه خود را به روز رسانی نمایید.',
                            'actionButton' => [
                                'title' => 'بروزرسانی',
                            ],
                            'storeButtons' => [ // just for store method
                            ],
                        ]
                    ],
                    [
                        'minVersion' => '3.0.0',
                        'method' => 'STORE_FORCE', // OTA, OTA_FORCE, STORE, STORE_FORCE
                        'otaUrl' => 'https://bazistco.com/appdl/ota/user/com.bazistco.bazist_'.$currentVer.'.zip',
                        'dialog' => [
                            'title' => 'بروزرسانی برنامه',
                            'message' => 'برای ارائه خدمات بهتر نیاز هست که حتما برنامه خود را به روز رسانی نمایید.',
                            'actionButton' => [
                                'title' => 'بروزرسانی',
                            ],
                            'storeButtons' => [ // just for store method
                                [
                                    'link' => 'market://details?id=com.bazistco.bazist',
                                    'type' => 'market', // market direct
                                    'icon' => 'https://landingo.cafebazaar.ir/brand-refresh/assets/brand-image.png',
                                    'title' => 'دریافت نسخه جدید',
                                    'color' => '#25d366',
                                ],
                            ],
                        ]
                    ],
                ]
            ],
            'pwa' => [
                'currentVersion' => 321,
            ],
        ];
        return $versioning;
    }

    public function updating(Request $request)
    {
        $user = auth()->user();
        $data = [];
        if(in_array('currentRequest',$request['services'])){
            $data['currentRequest'] = $user->currentSubmit();
        }
        $reviewRequest = $user->submits()->where('status',3)->where('survey',null)->latest()->first();
        $reviewRequestId = null;
        if($reviewRequest){
            $reviewRequestId = $reviewRequest->id;
            if($reviewRequestId){
                $data['review'] = [
                    'requestId' => $reviewRequestId,
                    'mandatory' => true,
                ];
            }
        }

        return sendJson('success','',$data);
    }
}
