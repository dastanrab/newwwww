<?php

namespace App\Livewire\Dashboard\Layouts;

use App\Models\Contact;
use App\Models\Permission;
use Livewire\Attributes\On;
use Livewire\Component;

class Sidebar extends Component
{
    public $isMashhad = true;
    public  $city;
    /**
     * @var \Illuminate\Foundation\Application|\Illuminate\Session\SessionManager|mixed|null
     */
    private  $city_id;
    #[On('city')]
    public function city($city)
    {
        $currentCity = session('city', 1);

        // تغییر مقدار city
        $newCity = $currentCity === 1 ? 3 : 1;

        // ذخیره در session
        session(['city' => (int)$city]);

        return true;
        // رفرش صفحه
//        return redirect(request()->header('Referer'));
    }

    public function render()
    {
        $this->city = session('city', 1);
        if ($this->city === 1) {
            $this->isMashhad = true;
        }
        else{
            $this->isMashhad = false;
        }
        $contactCount = Contact::where('admin_seen_at',null)->count();
        $menu = [
            [
                'title' => 'داشبورد',
                'icon'  => 'bx bxs-dashboard',
                'route' => route('d.home'),
                'name' => 'home',
                'permission' => 'home-menu',
            ],
            [
                'title' => 'نمودارها',
                'icon'  => 'bx bxs-chart',
                'route' => route('d.chart'),
                'name' => 'chart',
                'permission' => 'stat_other_index',
            ],
            [
                'title' => 'درخواست ها',
                'icon'  => 'bx bxs-cart-add',
                'route' => route('d.submits.all'),
                'name' => 'submits',
                'permission' => 'submit-menu',
                'submenu' => [
                    [
                        'title' => 'درخواست تلفنی',
                        'icon' => 'bx bx-headphone',
                        'route' => route('d.submits.tel'),
                        'name' => 'tel',
                        'permission' => 'submits-tel-menu',
                    ],
                    [
                        'title' => 'کل درخواست ها',
                        'icon' => 'bx bxs-cart-download',
                        'route' => route('d.submits.all'),
                        'name' => 'all',
                        'permission' => 'submits-all-menu',
                    ],
                    [
                        'title' => 'نقشه درخواست ها',
                        'icon' => 'bx bxs-map-alt',
                        'route' => route('d.submits.map'),
                        'name' => '',
                        'permission' => 'submits-map-menu',
                    ]
                ],
            ],
            [
                'title' => 'کیف پول',
                'icon'  => 'bx bxs-wallet',
                'route' => route('d.wallet'),
                'name' => 'wallet',
                'permission' => 'wallet-menu',
                'submenu' => [
                    [
                        'title' => 'گردش حساب',
                        'icon' => 'bx bxs-analyse',
                        'route' => route('d.wallet'),
                        'name' => 'all',
                        'permission' => 'wallet-all-menu',
                    ],
                    [
                        'title' => 'درخواست واریز',
                        'icon' => 'bx bxs-credit-card',
                        'route' => route('d.wallet.cashout'),
                        'name' => 'cashout',
                        'permission' => 'wallet-cashout-menu',
                    ],
                    [
                        'title' => 'آسان پرداخت',
                        'icon' => 'bx bxs-up-arrow-alt',
                        'route' => route('d.wallet.ap'),
                        'name' => 'ap',
                        'permission' => 'wallet-ap-menu',
                    ],
                    [
                        'title' => 'رانندگان',
                        'icon' => 'bx bx-wallet-alt',
                        'route' => route('d.wallet.drivers'),
                        'name' => 'drivers',
                        'permission' => 'wallet-driver-menu',
                    ],
                    [
                        'title' => 'واریز دستی',
                        'icon' => 'bx bxs-hand',
                        'route' => route('d.wallet.manual_transaction'),
                        'name' => 'manual_transaction',
                        'permission' => 'wallet-all-menu',
                    ],
                    /*[
                        'title' => 'واریز دستی آپ',
                        'icon' => 'bx bxs-hand',
                        'route' => '',
                        'name' => '',
                        'permission' => 'wallet-manual-ap-menu',
                    ]*/
                ]
            ],
            [
                'title' => 'آمار',
                'icon'  => 'bx bxs-pie-chart-alt-2',
                'route' => '',
                'name' => 'stats',
                'permission' => 'stat-menu',
                'submenu' => [
                    [
                        'title' => 'درخواست ها',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.submit'),
                        'name' => 'submit',
                        'permission' => 'stat-submit-menu',
                    ],
                    [
                        'title' => 'روزانه',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.daily'),
                        'name' => 'daily',
                        'permission' => 'stat-daily-menu',
                    ],
                    [
                        'title' => 'بار رانندگان',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.warehouse-driver'),
                        'name' => 'warehouse-driver',
                        'permission' => 'stat-drivers-menu',
                    ]
                    ,
                    [
                        'title' => 'حاضر به کار رانندگان',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.attendance-driver'),
                        'name' => 'attendance-driver',
                        'permission' => 'stat-drivers-menu',
                    ],
                    [
                        'title' => 'حقوق رانندگان',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.salary-driver'),
                        'name' => 'salary-driver',
                        'permission' => 'stat-drivers-salary-menu',
                    ],
                    [
                        'title' => 'پسماندها',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.waste'),
                        'name' => 'waste',
                        'permission' => 'stat-waste-menu',
                    ],
                    [
                        'title' => 'آرشیو مبالغ',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.total-cost'),
                        'name' => 'total-cost',
                        'permission' => 'stat-amount-archive-menu',
                    ],
                    [
                        'title' => 'آرشیو مبالغ 2',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.total-cost2'),
                        'name' => 'total-cost2',
                        'permission' => 'stat-amount-archive-menu',
                    ],
                    [
                        'title' => 'کاربران',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.user'),
                        'name' => 'user',
                        'permission' => 'stat-users-menu',
                    ],
                    [
                        'title' => 'تعداد کاربران',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.total.user'),
                        'name' => 'total-user',
                        'permission' => 'stat-users-total-menu',
                    ],
                    [
                        'title' => 'کلی انبار',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.warehouse'),
                        'name' => 'warehouse',
                        'permission' => 'stat-warehouse-all-menu',
                    ],
                    [
                        'title' => 'روزانه بار تحویلی انبار',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.warehouse-daily'),
                        'name' => 'warehouse-daily',
                        'permission' => 'stat-warehouse-daily-delivery-menu',
                    ],
                    [
                        'title' => 'مسافت',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.distances'),
                        'name' => 'distances',
                        'permission' => 'stat-distances-menu',
                    ],
                    [
                        'title' => 'سایر',
                        'icon' => 'bx bxs-bar-chart-alt-2',
                        'route' => route('d.stats.top-users'),
                        'name' => 'other',
                        'permission' => 'stat-other-menu',
                    ]
                ]
            ],
            [
                'title' => 'ردیابی',
                'icon'  => 'bx bxs-map-pin',
                'route' => route('d.track'),
                'name' => 'track',
                'permission' => 'track-menu',
                'submenu' => [
                    [
                        'title' => 'گزارش ارتباط با سرور',
                        'icon' => 'bx bx-signal-5',
                        'route' => route('d.track.report'),
                        'name' => 'report',
                        'permission' => 'track-report-menu',
                    ],
                    [
                        'title' => 'ردیابی آنلاین',
                        'icon' => 'bx bx-current-location',
                        'route' => route('d.track'),
                        'name' => 'online',
                        'permission' => 'track-online-menu',
                    ]
                ],
            ],
            [
                'title' => 'کاربران',
                'icon'  => 'bx bxs-group',
                'route' => route('d.users'),
                'name' => 'users',
                'permission' => 'users-menu',
                'submenu' => [
                    [
                        'title' => 'رانندگان',
                        'icon' => 'bx bxs-user-check',
                        'route' => route('d.drivers'),
                        'name' => 'drivers',
                        'permission' => 'drivers-menu',
                    ],
                    [
                        'title' => 'کاربران',
                        'icon' => 'bx bxs-user',
                        'route' => route('d.users'),
                        'name' => 'all',
                        'permission' => 'users-users-menu',
                    ]
                ],
            ],
            [
                'title' => 'باشگاه مشتریان',
                'count' => null,
                'icon'  => 'bx bxs-store',
                'route' => route('d.club.items'),
                'name' => 'club',
                'permission' => 'club-menu',
                'submenu' => [
                    [
                        'title' => 'آیتم ها',
                        'icon' => 'bx bxs-food-menu',
                        'route' => route('d.club.items'),
                        'name' => 'items',
                        'permission' => 'club-item-menu',
                    ],
                    [
                        'title' => 'دسته بندی',
                        'icon' => 'bx bx-align-right',
                        'route' => route('d.club.categories'),
                        'name' => 'categories',
                        'permission' => 'club-item-menu',
                    ],
                    [
                        'title' => 'کدهای تخفیف باز',
                        'count' => null,
                        'icon' => 'bx bxs-check-circle',
                        'route' => route('d.club.offers-open'),
                        'name' => 'open_offers',
                        'permission' => 'club-offers-menu',
                    ],
                    [
                        'title' => 'کدهای تخفیف بسته',
                        'count' => null,
                        'icon' => 'bx bxs-x-circle',
                        'route' => route('d.club.offers-close'),
                        'name' => 'close_offers',
                        'permission' => 'club-offers-menu',
                    ],
                    [
                        'title' => 'سابقه امتیاز ها',
                        'count' => null,
                        'icon' => 'bx bx-history',
                        'route' => route('d.club.score-histories'),
                        'name' => 'score-histories',
                        'permission' => 'club-offers-menu',
                    ]
                ],
            ],
            [
                'title' => 'پیام ها',
                'count' => $contactCount,
                'icon'  => 'bx bxs-chat',
                'route' => route('d.notifications'),
                'name' => 'messages',
                'permission' => 'messages-menu',
                'submenu' => [
                    [
                        'title' => 'پیام های گروهی',
                        'icon' => 'bx bx-chat',
                        'route' => route('d.notifications'),
                        'name' => 'notifications',
                        'permission' => 'messages-notification-menu',
                    ],
                    [
                        'title' => 'صندوق پیام ها',
                        'count' => $contactCount,
                        'icon' => 'bx bx-message-square-detail',
                        'route' => route('d.contacts'),
                        'name' => 'contacts',
                        'permission' => 'messages-contacts-menu',
                    ],
                    [
                        'title' => 'نظرات درخواست ها',
                        'icon' => 'bx bx-message-square-detail',
                        'route' => route('d.submit-surveys'),
                        'name' => 'submit-surveys',
                        'permission' => 'messages-contacts-menu',
                    ]
                ],
            ],
            [
                'title' => 'تنظیمات',
                'icon'  => 'bx bxs-cog',
                'route' => '',
                'name' => 'settings',
                'permission' => 'settings-menu',
                'submenu' => [
                    [
                        'title' => 'بازه های درخواست',
                        'icon' => 'bx bx-calendar-event',
                        'route' => route('d.settings.submit-time'),
                        'name' => 'submit-time',
                        'permission' => 'settings-submit-time-menu',
                    ],
                    [
                        'title' => 'دسته بندی پسماندها',
                        'icon' => 'bx bxs-category',
                        'route' => route('d.settings.recyclable'),
                        'name' => 'recyclable',
                        'permission' => 'settings-recyclables-menu',
                    ],
                    [
                        'title' => 'مناطق آنیروب',
                        'icon' => 'bx bxs-area',
                        'route' => route('d.settings.area'),
                        'name' => 'area',
                        'permission' => 'settings-area-menu',
                    ],
                    [
                        'title' => 'نقش های کاربری',
                        'icon' => 'bx bxs-universal-access',
                        'route' => route('d.settings.roles'),
                        'name' => 'role',
                        'permission' => 'settings-role-menu',
                    ],
                    [
                        'title' => 'نقشه',
                        'icon' => 'bx bx-map-alt',
                        'route' => route('d.settings.map'),
                        'name' => 'map',
                        'permission' => 'settings-map-menu',
                    ],
                    [
                        'title' => 'عمومی',
                        'icon' => 'bx bx-cog',
                        'route' => route('d.settings.general'),
                        'name' => 'general',
                        'permission' => 'settings-general-menu',
                    ]
                ],
            ],
            [
                'title' => 'فعالیت ها',
                'icon'  => 'bx bxs-megaphone',
                'route' => route('d.logs'),
                'name' => 'logs',
                'permission' => 'logs-menu',
            ],
            [
                'title' => 'رانندگان',
                'icon'  => 'bx bxs-car',
                'route' => route('d.supervisor.drivers'),
                'name' => 'supervisor-drivers',
                'permission' => 'supervisor-drivers-menu',
            ],
        ];
        $permissions = auth()->user()->getPermissions();
        $role=auth()->user()->getRoles(0);
        $can_see= in_array($role,['superadmin','admin','manager']);
        return view('livewire.dashboard.layouts.sidebar',compact('menu','permissions','role','can_see'));
    }
}
