<?php

namespace App\Models;

use App\Events\ActivityEvent;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Inax extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'amount', 'operator', 'mobile', 'method', 'pay_method', 'type', 'order_id', 'ref_code', 'trans_id', 'description', 'status'];

    /*
     * preparation => ثبت رکورد و آماده سازی برای انجام عملیات
     * pendingDecreaseCredit => شارژ خریداری شده و منتظر کسر از اعتبار است
     * pendingVerifyDecreaseCredit => منتظر اعتبارسنجی پرداخت
     * pendingSubmitRecordDb => منتظر ثبت رکورد مربوط به جدول پرداختی (آپ یا کیف پول بازیست)
     * done => روال پرداخت با موفقیت انجام شده
     * cancel => لغو شده و هیچ شارژی هم خریداری نشده
     */
    // وضعیت هایی که باید در پنل چک شوند: pendingDecreaseCredit - pendingVerifyDecreaseCredit - pendingSubmitRecordDb
    protected $url = 'https://inax.ir/webservice.php';
    protected $indexOrderId = 100;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function errors($code = null)
    {
        $errors = [
            1 => 'عملیات با موفقیت انجام شد',
            -11 => 'اطلاعات ارسال شده ناقص است',
            -12 => 'شماره تلفن وارد شده در شارژ مستقیم صحیح نمی باشد یا با نوع اپراتور همخوانی ندارد',
            -13 => 'شناسه قبض نامعتبر است',
            -14 => 'شناسه پرداخت نامعتبر است',
            -15 => 'شناسه قبض و شناسه پرداخت همخوانی ندارند',
            -16 => 'قبضی با این مشخصات قبلاً در سیستم ثبت شده است',
            -18 => 'شماره تلفن وارد شده مربوط به سیم کارت دائمی همراه اول نمی باشد',
            -22 => 'خطا در عملیات لطفا این مورد را با پشتیبانی درمیان بگذارید (22)',
            -33 => 'خطا در عملیات لطفا این مورد را با پشتیبانی درمیان بگذارید (33)',
            -44 => 'مبلغ درخواست خارج از محدوده است یا محصول مورد نظر موجود نمی باشد',
            -55 => 'تراکنشی با شماره ارسال شده وجود ندارد',
            -66 => 'اپراتور شارژ مستقیم ایرانسل در دسترس نمی باشد',
            -67 => 'خطا در انجام عملیات لطفا دوباره امتحان کنید (67)',
            -77 => 'خطا در انجام عملیات لطفا دوباره امتحان کنید (77)',
            -91 => 'سیستم موقتاً با مشکل مواجه شده است',
            -200 => 'عملیات با اشکال روبرو شد اگر مبلغ از شما کسر شد لطفا این مورد را با اپراتور درمیان بگذارید (200)',
        ];
        if($code === null)
            return $errors;
        return isset($errors[$code]) ? $errors[$code] : null;
    }

    public static function addChargeRecord($userId,$amount,$operator,$mobile,$method,$payMethod,$chargeType)
    {
        $self = new static;
        $inax = Inax::create([
            'user_id'     => $userId,
            'amount'      => $amount,
            'operator'    => $operator,
            'mobile'      => $mobile,
            'method'      => $method,
            'pay_method'  => $payMethod,
            'type'        => $chargeType,
            'status'      => 'preparation',
        ]);
        if($inax){
            $orderId = $self->indexOrderId+$inax->id;
            $orderIdUpdated = $inax->update(['order_id' => $orderId]);
            if($orderIdUpdated){
                return  $orderId;
            }
            $inax->update(['status' => 'cancel', 'description' => 'order id ذخیره نشد به همین دلیل ثبت این رکورد ناموفق شد.']);
            return -2;
        }
        else{
            return -1;
        }
    }

    public static function addInternetRecord($userId,$amount,$operator,$mobile,$method,$payMethod,$type)
    {
        $self = new static;
        $inax = Inax::create([
            'user_id'     => $userId,
            'amount'      => $amount,
            'operator'    => $operator,
            'mobile'      => $mobile,
            'method'      => $method,
            'pay_method'  => $payMethod,
            'type'        => $type,
            'status'      => 'preparation',
        ]);
        if($inax){
            $orderId = $self->indexOrderId+$inax->id;
            $orderIdUpdated = $inax->update(['order_id' => $orderId]);
            if($orderIdUpdated){
                return  $orderId;
            }
            $inax->update(['status' => 'cancel', 'description' => 'order id ذخیره نشد به همین دلیل ثبت این رکورد ناموفق شد.']);
            return -2;
        }
        else{
            return -1;
        }
    }

    public static function balance()
    {
        $self = new static;

        if (\Illuminate\Support\Facades\Cache::has('inax_remain_balance'))
        {
            return \Illuminate\Support\Facades\Cache::get('inax_remain_balance');
        }else{
            try {
                $response = Http::withBody(json_encode([
                    "method" => "credit",
                    "username" => '2842d49f2725c02b42ca71959360b9ed',
                    "password" => '#z58k#r6g4'
                ]))->withOptions(['timeout' => 5])->post($self->url);
                $value= $response->json()['credit']??10000;
                \Illuminate\Support\Facades\Cache::set('inax_remain_balance',$value,100);
                return $value;
            }catch (\Exception $exception)
            {
                return 0;
            }
        }

    }

    public static function getProducts()
    {
        $self = new static;
        try {
            $response = Http::withBody(json_encode([
                "method" => "products",
                "username" => '2842d49f2725c02b42ca71959360b9ed',
                "password" => '#z58k#r6g4'
            ]))->post($self->url);
            return $response->json();
        } catch (Exeption $e) {
            event(new ActivityEvent($response->throw(), 'INAX', false));
        }
    }

    public static function getOperator($key = null)
    {
        $data = [
            'MTN' => 'ایرانسل',
            'MCI' => 'همراه اول',
            'RTL' => 'رایتل',
            'SHT' => 'شاتل',
        ];
        if(empty($key))
            return $data;
        else
            return $data[$key];
    }

    public static function filterProduct($data)
    {
        $package = [
            [
                'value' => 'MTN',
                'label' => 'ایرانسل',
                'packages' => [],
            ],

            [
                'value' => 'MCI',
                'label' => 'همراه اول',
                'packages' => [],
            ],
            [
                'value' => 'RTL',
                'label' => 'رایتل',
                'packages' => [],
            ],
            [
                'value' => 'SHT',
                'label' => 'شاتل',
                'packages' => [],
                'days' => ['1']
            ]
        ];
        $operators = ['MTN' => 0,'MCI' => 1,'RTL' => 2,'SHT' => 3];
        $types = [
            'hourly'  => 'بسته اینترنت ساعتی',
            'daily'   => 'بسته اینترنت روزانه',
            'weekly'  => 'بسته اینترنت هفتگی',
            'monthly' => 'بسته اینترنت ماهیانه',
            'yearly'  => 'بسته اینترنت سالیانه',
            'amazing' => 'بسته اینترنت شگفت انگیز',
            'TDLTE'   => 'بسته های اینترنت TDLTE',
        ];
        $simTypes = [
            'permanent'       => 'دائمی',
            'credit'          => 'اعتباری',
            'TDLTE_credit'    => 'TDLTE اعتباری',
            'TDLTE_permanent' => 'TDLTE دائمی',
            'data'            => 'دیتا',
            'TDLTE'           => 'TDLTE',
        ];
        foreach ($data['products']['internet'] as $item){
            $package[$operators[$item['operator']]]['days'][] = $item['days'];
            $package[$operators[$item['operator']]]['packages'][] = [
                'id'      => (int)$item['id'],
                'days'    => $item['days'],
                'title'   => $item['name'],
                'type'    => [
                    'value' => $item['internet_type'],
                    'label' => $item['internet_type'] ? $types[$item['internet_type']] : '',
                ],
                'simType' => [
                    'value' => $item['sim_type'],
                    'label' => $simTypes[$item['sim_type']],
                ],
                'amount' => [
                    'value' => $item['amount'],
                    'label' => tomanFormat($item['amount']),
                ],
            ];
        }
        $i=0;
        foreach ($package as $item){
            $days = array_values(array_unique($item['days']));
            sort($days);
            $packages[$i] = $item;
            $packages[$i]['days'] = $days;
            $i++;
        }
        unset($packages[3]);
        return $packages;
    }
    public static function findInternetPackage($productId,$operator,$internetType,$simType)
    {
        $data = self::getProducts();
        $packages = self::filterProduct($data);
        $packages = collect($packages);
        $packages = $packages->filter(function ($item) use ($operator){
            return $item['value'] == $operator;
        })->first();
        if($packages) {
            $packages = collect($packages['packages'])->filter(function ($package) use ($productId, $internetType, $simType) {
                return $package['id'] == $productId && $package['type']['value'] == $internetType && $package['simType']['value'] == $simType;
            });
            return $packages->first();
        }
        return null;
    }

    public static function buyCharge($method,$operator,$amount,$mobile,$type,$orderId)
    {

        $self = new static;
        try {
            $response = Http::timeout(35)->withBody(json_encode([
                "method"      => $method,
                "username"    => '2842d49f2725c02b42ca71959360b9ed',
                "password"    => '#z58k#r6g4',
                "operator"    => $operator,
                "amount"      => $amount,
                "mobile"      => $mobile,
                "charge_type" => $type,
                "order_id"    => $orderId,
                "company"     => 'بازیست',
                "pay_type"    => 'credit',
                //"test_mode"   => true
            ]))->post($self->url);

            return $response->json();

        } catch (\Exception $e) {
            $order=$self::verifyOrder($orderId);
            if (isset($order['code']) and $order['code'] == 1 and $order['final_status'] == 'succeeded') {
                return $order;
            }
            event(new ActivityEvent($response->throw(), 'INAX', false));
        }

    }

    public static function buyInternet($productId,$operator,$mobile,$internetType,$simType,$orderId)
    {
        $self = new static;
        try {
            $response = Http::timeout(35)->withBody(json_encode([
                "method"        => "internet",
                "username"      => '2842d49f2725c02b42ca71959360b9ed',
                "password"      => '#z58k#r6g4',
                "product_id"    => $productId,
                "operator"      => $operator,
                "mobile"        => $mobile,
                "internet_type" => $internetType,
                "sim_type"      => $simType,
                "order_id"      => $orderId,
                "pay_type"      => "credit",
                //"test_mode"   => true
            ]))->post($self->url);

            return $response->json();

        } catch (\Exception $e) {
            $order=$self::verifyOrder($orderId);
            if (isset($order['code']) and $order['code'] == 1 and $order['final_status'] == 'succeeded') {
                return $order;
            }
            event(new ActivityEvent($response->throw(), 'INAX', false));
        }

    }
    public static function verifyOrder($orderId)
    {

        $self = new static;
        try {
            $response = Http::withBody(json_encode([
                "method"      => 'trans_status',
                "username"    => '2842d49f2725c02b42ca71959360b9ed',
                "password"    => '#z58k#r6g4',
                "order_id"    => $orderId,
                //"test_mode"   => true
            ]))->post($self->url);
            return $response->json();

        } catch (\Exception $e) {
            event(new ActivityEvent($response->throw(), 'INAX', false));
        }

    }

}
