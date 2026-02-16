<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Driver;
use App\Models\DriverWallet;
use App\Models\Guild;
use App\Models\Recyclable;
use App\Models\Submit;
use App\Models\SubmitMessage;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public static function data($user = null){
        if(!$user){
            $user = auth()->user();
        }
        $reward = userRewardToman();
        $maxWeight = 10;
        $recyclables = Recyclable::all();
        foreach ($recyclables as $recyclable) {
            $wastes[] = [
                'value' => $recyclable->id,
                'label' => $recyclable->title,
                'maxPrice' => $recyclable->max_price,
            ];
        }
        $guilds = Guild::all();
        $minAmountCard = minWithdrawCardToCardToman();
        $minAmountAap = minWithdrawAapToman();
        $wage = wageToman();
        $guildMarket = [];
        foreach ($guilds as $guild){
            $guildMarket[] = [
                'value' => $guild->id,
                'label' => $guild->title,
            ];
        }
        $data = [
            'user' => [
                'firstName'        => $user->name,
                'lastName'         => $user->lastname,
                'mob'              => $user->mobile,
                'rollCall'         => $user->rollCallData(),
                'selfReferralCode' => $user->referral(),
                'city'             => $user->city ? $user->city->id : null,
                'avatar'           => asset('assets/img/avatar/avatar-driver-3.png'),
                'currentWeight' => current_driver_weight($user->id),
                'currentIncome'=>current_driver_salary($user->id),
            ],
            'cities' =>  [
                [
                    'id' => 1, 'name' => 'مشهد', 'defaultLocation' => [ 'lat' => 36.298982, 'lng' => 59.605362 ]
                ]
            ],
            "pricingEnabled" => DriverWallet::where('user_id', $user->id)->first() ? true : false,
            'wasteList' => $wastes,
            'cached' => [
                "maxWeight" => [
                    'value' => $maxWeight,
                    'warning' => "وزن انتخاب شده بیش‌تر از $maxWeight کیلوگرم می‌باشد"
                ],
                "statusList" => Submit::driverStatusList(),
                "sidebar"        => [
                    "menuItems" => [
                        [
                            "icon"  => "monetization_on",
                            "name"  => "prices",
                            "text"  => "قیمت پسماندها",
                            "badge" => 0,
                            "type"  => "internal",
                            "path"  => "/prices",
                        ],
                        [
                            "icon"  => "pending",
                            "name"  => "currentRequest",
                            "text"  => "درخواست های جاری",
                            "badge" => $user->drivers()->where('status',2)->count(),
                            "type"  => "internal",
                            "path"  => "/requests/current",
                        ],
                        [
                            "icon"  => "library_add_check",
                            "name"  => "historyRequest",
                            "text"  => "درخواست های انجام شده",
                            "badge" => 0,
                            "type"  => "internal",
                            "path"  => "/requests/history",
                        ]
                    ]
                ],
                "wallet" => [
                    "notice" => "با اولین درخواست موفق ".tomanFormat($reward)." پاداش دریافت کنید."
                ],
                "withdrawalSettings" => [
                    'card' => [
                        "hintItems" => [
                            'حداقل مبلغ برای برداشت، '.tomanFormat($minAmountCard).' می باشد.',
                            'این مبلغ طی 24 ساعت کاری به حساب شما واریز می شود.',
                        ],
                        "wage" => $wage, //کارمزد
                        "minAmount" => $minAmountCard, //حداقل مبلغ قابل برداشت
                    ],
                    'aap' => [
                        "hintItems" => [
                            'حداقل مبلغ برای برداشت، '.tomanFormat($minAmountAap).' می باشد.',
                            'این مبلغ در سریع ترین زمان ممکن برای شما واریز می شود',
                        ],
                        "wage" => $wage, //کارمزد
                        "minAmount" => $minAmountAap, //حداقل مبلغ قابل برداشت
                    ],
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
                'requestMessages' => SubmitMessage::driverMessagesApi(),
                'guildMarketList' => $guildMarket,
            ],
            'updateServiceTTL' => env('APP_ENV') == 'local' ? 10 :  15,
            'versioning' => self::versioning()
        ];
        return $data;
    }


    public static function versioning()
    {
        $currentVersion = '1.0.5';
        $versioning = [
            'android' => [
                'currentVersion' => $currentVersion,
                'targets' => [
                    // این سناریو فقط روی کاربرانی که ورژن اپشون بیشتر مساوری با 4.0.0 باشه، اعمال می شه.
                    [
                        'minVersion' => '1.0.0',
                        'method' => 'OTA_FORCE', // OTA_WITH_DIALOG, OTA_SILENT, OTA_FORCE, STORE, STORE_FORCE
                        'otaUrl' => 'https://bazistco.com/appdl/ota/driver/com.bazistco.driver_'.$currentVersion.'.zip',
                        'dialog' => [
                            'title' => 'بروزرسانی برنامه',
                            'message' => 'برای ارائه خدمات بهتر نیاز هست که حتما برنامه خود را به روز رسانی نمایید.',
                            'actionButton' => [
                                'title' => 'بروزرسانی',
                            ]
                        ]
                    ],

                    // این سناریو فقط روی کاربرانی که ورژن اپشون بیشتر مساوری با 1.0.0 باشه، و سناریوهای بالا براشون صدق نکنه، اعمال می شه.
                    /*[
                        'minVersion' => '3.0.0',
                        'method' => 'STORE', // OTA_WITH_DIALOG, OTA_SILENT, OTA_FORCE, STORE, STORE_FORCE
                        'dialog' => [
                            'title' => 'بروزرسانی برنامه',
                            'message' => 'برای ارائه خدمات بهتر نیاز هست که حتما برنامه خود را از لینک های زیر به روز رسانی نمایید.',
                            'storeButtons' => [
                                [
                                    'link' => 'https://adamvahava.org/',
                                    'type' => 'direct',
                                    'icon' => 'app/setting/telegram.png',
                                    'title' => 'دانلود از سایت',
                                    'color' => '#25d366',
                                ],
                                [
                                    'link' => 'https://adamvahava.org/',
                                    'type' => 'direct',
                                    'icon' => 'app/setting/telegram.png',
                                    'title' => 'دانلود از تلگرام',
                                    'color' => '#f07c00',
                                ],
                                [
                                    'link' => 'https://adamvahava.org/',
                                    'type' => 'direct',
                                    'icon' => 'app/setting/telegram.png',
                                    'title' => 'دانلود از ایتا',
                                    'color' => '#0088cc',
                                ],
                                [
                                    'link' => 'market://details?id=com.rasef.adamvahava',
                                    'type' => 'market',
                                    'icon' => 'app/setting/telegram.png',
                                    'title' => 'دانلود از مارکت',
                                    'color' => '#25d366',
                                ],
                            ],
                            'actionButton' => [ // will be shown if type = STORE
                                'title' => 'بعدا',
                            ]
                        ]
                    ],*/
                ]
            ],
            'pwa' => [
                'needUpdate' => false,
                'currentVersion' => 105,
            ],
        ];
        return $versioning;
    }

    public function index()
    {
        return sendJson('success', '', $this->data());
    }
}
