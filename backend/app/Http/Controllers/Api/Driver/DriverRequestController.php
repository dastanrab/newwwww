<?php

namespace App\Http\Controllers\Api\Driver;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Car;
use App\Models\City;
use App\Models\Driver;
use App\Models\Polygon;
use App\Models\Submit;
use App\Models\SubmitTime;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DriverRequestController extends Controller
{
    /**
     * استعلام کاربر با موبایل
     *
     * اگر کاربر وجود داشته باشد، اطلاعات و آدرس‌های فعالش برمی‌گردد.
     * اگر وجود نداشته باشد، با نام و نام خانوادگی ایجاد می‌شود.
     */
    public function resolveCustomer(Request $request)
    {
        // پشتیبانی از اعداد فارسی/عربی و فرمت‌های رایج شماره موبایل
        $mobile = $this->normalizeMobile((string) $request->input('mobile'));
        $request->merge(['mobile' => $mobile]);

        $validator = Validator::make($request->all(), [
            'mobile'    => ['required', 'string', 'regex:/^09[0-9]{9}$/'],
            'firstName' => ['nullable', 'string', 'min:2', 'max:100'],
            'lastName'  => ['nullable', 'string', 'min:2', 'max:100'],
        ], [
            'mobile.required' => 'شماره موبایل را وارد نمایید',
            'mobile.regex'    => 'شماره موبایل را به‌درستی وارد نمایید',
            'firstName.min'   => 'نام را به‌درستی وارد نمایید',
            'lastName.min'    => 'نام خانوادگی را به‌درستی وارد نمایید',
        ]);

        if ($validator->fails()) {
            return sendJson('error', $validator->errors()->first());
        }

        return DB::transaction(function () use ($request, $mobile) {
            // برای جلوگیری از ایجاد هم‌زمان کاربر تکراری،
            // روی ستون mobile حتماً unique index داشته باشید.
            $customer = User::where('mobile', $mobile)->first();
            $created = false;

            if (!$customer) {
                $name = trim((string) $request->input('firstName'));
                $lastname = trim((string) $request->input('lastName'));

                if (mb_strlen($name) < 2 || mb_strlen($lastname) < 2) {
                    return sendJson(
                        'error',
                        'این شماره در سیستم ثبت نشده است؛ نام و نام خانوادگی را وارد نمایید'
                    );
                }

                $city = City::where('name', 'mashhad')->first();

                if (!$city) {
                    return sendJson('error', 'شهر پیش‌فرض مشهد در سیستم تعریف نشده است');
                }

                $customer = new User();

                // کاربر خام؛ ثبت‌نام کامل یا ارسال پیامک در این مرحله انجام نمی‌شود.
                // اگر جدول users فیلد اجباری دیگری دارد، مقدار پیش‌فرض آن را هم اینجا قرار دهید.
                $customer->forceFill([
                    'mobile'      => $mobile,
                    'name'        => $name,
                    'lastname'    => $lastname,
                    'legal'       => 0,
                    'guild_id'    => null,
                    'guild_title' => null,
                    'email'       => null,
                    'birthday'    => null,
                    'city_id'     => $city->id,

                    // رمز تصادفی؛ در صورتی که ستون password در دیتابیس nullable نباشد.
                    'password'    => Hash::make(Str::random(40)),
                ])->save();

                $created = true;
            }

            return sendJson(
                'success',
                $created ? 'کاربر جدید ایجاد شد' : 'کاربر پیدا شد',
                [
                    'created' => $created,
                    'user' => [
                        'id'        => $customer->id,
                        'mobile'    => $customer->mobile,
                        'firstName' => $customer->name,
                        'lastName'  => $customer->lastname,
                    ],
                    'addresses' => $this->formatAddresses($customer),
                ]
            );
        });
    }

    /**
     * دریافت آدرس‌های یک مشتری برای راننده
     */
    public function addresses(User $customer)
    {
        return sendJson(
            'success',
            'لیست آدرس‌های کاربر',
            $this->formatAddresses($customer)
        );
    }

    /**
     * ثبت آدرس برای مشتری توسط راننده
     */
    public function storeAddress(Request $request, User $customer)
    {
        $validator = Validator::make($request->all(), [
            'title'      => ['nullable', 'string', 'min:2', 'max:100'],
            'address'    => ['required', 'string', 'min:6'],
            'lat'        => ['required', 'numeric', 'between:-90,90'],
            'lng'        => ['required', 'numeric', 'between:-180,180'],
            'isFavorite' => ['nullable', 'boolean'],
        ], [
            'title.min'   => 'عنوان آدرس را به‌درستی وارد نمایید',
            'address.required' => 'آدرس را وارد نمایید',
            'address.min' => 'آدرس را به‌درستی وارد نمایید',
            'lat.required' => 'موقعیت lat ارسال نشده است',
            'lng.required' => 'موقعیت lng ارسال نشده است',
        ]);

        if ($validator->fails()) {
            return sendJson('error', $validator->errors()->first());
        }

        $district = xDistrict([
            (float) $request->input('lat'),
            (float) $request->input('lng'),
        ]);

        $polygon = Polygon::where('region', $district)->first();

        if (!$polygon) {
            return sendJson('error', 'این آدرس خارج از محدوده خدمات است');
        }

        $address = $customer->addresses()->create([
            'city_id'  => $polygon->city_id,
            'title'    => $request->input('title') ?: 'آدرس جدید',
            'address'  => $request->input('address'),
            'region'   => $polygon->id,
            'district' => 0,
            'lat'      => $request->input('lat'),
            'lon'      => $request->input('lng'),

            // در API فعلی فهرست آدرس‌ها status=1 را برمی‌گرداند؛
            // بنابراین آدرس راننده باید فعال باشد تا بعداً در فهرست دیده شود.
            'status'   => 1,
        ]);

        return sendJson('success', 'آدرس برای کاربر ثبت شد', [
            'id'      => $address->id,
            'title'   => $address->title,
            'address' => $address->address,
            'city'    => $address->city_id,
            'lat'     => $address->lat,
            'lng'     => $address->lon,
        ]);
    }

    /**
     * ثبت درخواست فوری برای مشتری توسط راننده
     */
    public function storeImmediateRequest(Request $request, User $customer)
    {
        try {
            /** @var \App\Models\User $driverUser */
            $driverUser = auth()->user();

            if (!$driverUser) {
                return sendJson('error', 'کاربر راننده احراز هویت نشده است');
            }

            // ۱. اعتبارسنجی ورودی
            $validator = Validator::make($request->all(), [
                'addressId' => ['required', 'integer'],
            ], [
                'addressId.required' => 'لطفاً آدرس را انتخاب نمایید',
                'addressId.integer'  => 'شناسه آدرس معتبر نیست',
            ]);

            if ($validator->fails()) {
                return sendJson('error', $validator->errors()->first());
            }

            // ۲. بررسی خودرو فعال راننده
            $activeCar = Car::where('user_id', $driverUser->id)
                ->where('is_active', true)
                ->first();

            if (!$activeCar) {
                $activeCar = Car::where('user_id', $driverUser->id)->first();
            }

            if (!$activeCar) {
                return sendJson('error', 'هیچ خودرویی برای حساب رانندگی شما یافت نشد.');
            }

            // ۳. بررسی سقف ۴ بار فعال
            if ($driverUser->drivers()->where('status', 2)->count() >= 4 && $driverUser->id !== 2) {
                return sendJson('error', 'نمی‌توانید بیشتر از ۴ درخواست فعال داشته باشید');
            }

            // ۴. آدرس مشتری
            $address = $customer->addresses()
                ->where('id', $request->input('addressId'))
                ->where('status', 1)
                ->first();

            if (!$address) {
                return sendJson('error', 'این آدرس برای کاربر انتخاب‌شده ثبت نشده است');
            }

            // ۵. بررسی محدوده پلیگان
            $targetLat = $address->lat;
            $targetLon = $address->lon ?? $address->lng;

            if (!isLocationInsidePolygon($driverUser->id, [$targetLat, $targetLon])) {
                return sendJson('error', 'این درخواست خارج از منطقه کاری شما می‌باشد');
            }

            // ۶. بررسی درخواست فعال برای کاربر
            if (Submit::where('user_id', $customer->id)->where('status', 1)->exists()) {
                return sendJson('error', 'این کاربر یک درخواست فعال دارد');
            }

            // ۷. بررسی بازه زمانی فوری
            $submitTime = SubmitTime::find(1);
            if (!$submitTime || (int) $submitTime->instant === 0) {
                return sendJson('error', 'در حال حاضر ثبت درخواست فوری غیرفعال است');
            }

            if (!Submit::immediateValidate((int) verta()->format('G'))) {
                return sendJson('error', 'در حال حاضر ثبت درخواست فوری در این بازه زمانی غیرفعال است');
            }

            // مرج کردن فیلدهای ضروری
            $request->merge([
                'addressId'  => $address->id,
                'cardId'     => null,
                'payMethod'  => 'aniroob',
                'scheduling' => 'immediate',
            ]);

            $submit = null;

            DB::transaction(function () use ($customer, $request, $driverUser, $activeCar, $address, &$submit) {
                // ایجاد سابمیت
                $res = Submit::add($customer->id, $customer, $request);

                if ($res instanceof Submit) {
                    $submit = $res;
                } else {
                    $submit = Submit::where('user_id', $customer->id)
                        ->latest('id')
                        ->first();
                }

                if (!$submit) {
                    throw new \Exception('خطا: رکورد درخواست Submit در دیتابیس ایجاد نشد.');
                }

                // بررسی رکورد انتساب به راننده در جدول drivers
                $driverRecord = Driver::where('submit_id', $submit->id)->first();

                $cityId = $submit->city_id ?? $address->city ?? null;

                if ($driverRecord) {
                    $driverRecord->update([
                        'user_id' => $driverUser->id,
                        'car_id'  => $activeCar->id,
                        'status'  => 2,
                        'city_id' => $cityId,
                    ]);
                } else {
                    Driver::create([
                        'user_id'   => $driverUser->id,
                        'car_id'    => $activeCar->id,
                        'submit_id' => $submit->id,
                        'status'    => 2,
                        'city_id'   => $cityId,
                    ]);
                }

                @Storage::put('driver-submitAll.txt', date('Y-m-d H:i:s') . ' - Driver: ' . $driverUser->id . ' - Submit: ' . $submit->id);
            });

            $customer->refresh();

            return sendJson(
                'success',
                'درخواست فوری با موفقیت ثبت شد',
                $customer->currentSubmit() ?? $submit
            );

        } catch (\Throwable $e) {
            // بازگرداندن دقیق محل و خطای پرتاب شده
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'debug'   => [
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                    'class' => get_class($e),
                ]
            ], 500);
        }
    }

    /**
     * خروجی آدرس‌ها با همان قالب API فعلی
     */
    private function formatAddresses(User $customer): array
    {
        return $customer->addresses()
            ->where('status', 1)
            ->get()
            ->map(function (Address $address) {
                return [
                    'id'      => $address->id,
                    'title'   => $address->title,
                    'address' => $address->address,
                    'city'    => $address->city_id,
                    'lat'     => $address->lat,
                    'lng'     => $address->lon,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * تبدیل شماره‌های فارسی/عربی و فرمت +98 به فرمت 09xxxxxxxxx
     */
    private function normalizeMobile(string $mobile): string
    {
        $mobile = strtr($mobile, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);

        $digits = preg_replace('/\D+/', '', $mobile) ?? '';

        if (str_starts_with($digits, '0098')) {
            $digits = '0' . substr($digits, 4);
        } elseif (str_starts_with($digits, '98') && strlen($digits) === 12) {
            $digits = '0' . substr($digits, 2);
        }

        return $digits;
    }
}
