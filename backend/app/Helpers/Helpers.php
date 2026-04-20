<?php

use App\Classes\TransactionService;
use App\Models\Address;
use App\Models\ArchiveApp;
use App\Models\ArchiveLegal;
use App\Models\ArchiveNotLegal;
use App\Models\ArchivePhone;
use App\Models\WalletDetails;
use App\Models\Cache;
use App\Models\Car;
use App\Models\Cashout;
use App\Models\Day;
use App\Models\Driver;
use App\Models\DriversSalaryDetails;
use App\Models\DriverSuggestedRequests;
use App\Models\Hour;
use App\Models\Location;
use App\Models\NeshanApiLog;
use App\Models\Percentage;
use App\Models\Polygon;
use App\Models\PolygonDayHour;
use App\Models\PolygonDriver;
use App\Models\QueueFails;
use App\Models\Receive;
use App\Models\ReceiveArchive;
use App\Models\Submit;
use App\Models\User;
use Carbon\Carbon;
use Hekmatinasser\Verta\Verta;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;


function developerId()
{
    //57165
    return 67775;
}

function bdd($data = '')
{
    if(auth()->id() == developerId()){
        dd($data);
    }
}

function toast($errors = ''){
    $html = '';
    if(session()->has(['toast']) || $errors) {
        $type = session()->has(['toast']) && session('toast')['result'] == 1 ? "text-bg-primary" : "text-bg-danger";
        $message = session()->has(['toast']) && session('toast')['message'] ? session('toast')['message'] : $errors->first();
        if($message) {
            $html .= '<div class="toast align-items-center show ' . $type . ' border-0" role="alert" aria-live="assertive" aria-atomic="true">';
            $html .= '<div class="d-flex">';
            $html .= '<div class="toast-body">';
            $html .= $message;
            $html .= '</div>';
            $html .= '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close" ></button>';
            $html .= '</div>';
            $html .= '</div>';
        }
    }
    echo $html;
}

function offlineMessage(){
    $html = '<div class="toast align-items-center show text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true" wire:offline style="left: 21px; right: auto">';
    $html .= '<div class="d-flex">';
    $html .= '<div class="toast-body">';
    $html .= '<i class=\'bx bx-wifi-off\'></i> اتصال شما به اینترنت قطع می باشد';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    echo '';
}
function sendToast($result, $message){
    session()->flash('toast',['result' => $result,'message' => $message]);
}

function breadCrumb($items = []){
    $html = '';
    $html = '<div class="rc-breadcrumb"><nav><ul>';
    $html .= '<li><a href="'.route('d.home').'">داشبورد</a></li>';
    $i=1;
    foreach ($items as $item){
        $html .= '<li>';
        if($i != count($items)) $html .= '<a href="'.route($item[1]).'">';
        $html .= $item[0];
        if($i != count($items)) $html .= '</a>';
        $html .= '</li>';
        $i+=1;
    }
    $html .= '</ul></nav></div>';
    echo $html;
}


function isMob($mob){
    return (strlen($mob) == 11 && is_numeric($mob) && substr($mob, 0, 2) == "09");
}

function isEmail($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function spinner(){
    echo <<<HTML
    <div class="cr-table-spinner"><div class="spinner-border spinner-border-sm" role="status"></div></div>
    HTML;

}

function headTitle($data = []){
    return implode(' > ',$data);
}

function strRandom($length = 16)
{
    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}

function weightFormat($number = 0){
    $text = '';
    if ($number >= 1) {
        $kilos = floor($number);
        $grams = ($number - $kilos) * 1000;
        $text .= number_format($kilos) . " کیلو ";

        if ($grams > 0) {
            $text .= 'و '.number_format($grams);
        }
    } else {
        $grams = $number * 1000;

        if ($grams > 0) {
            $text .= number_format($grams) ;
        }
    }
    $text .= $text ? " گرم": '';
    return $text;

}

function tomanFormat($amount)
{
    return number_format(floor($amount)).' تومان';
}



function button($textButton = 'ذخیره',$icon = 'bx bx-plus', $wire = '',$class = '',$property = '')
{
    echo <<<HTML
        <button $wire class="$class" wire:ignore.self wire:loading.attr="disabled" $property><span wire:loading.class="cr-hidden">$textButton</span>
        <i class="$icon" wire:loading.class="cr-hidden"></i>
        <span class="cr-hidden" wire:loading.class.remove="cr-hidden"><div class="cr-spinner"><div class="spinner-border spinner-border-sm" role="status"></div></div></span>
        </button>
        HTML;
}


if (! function_exists('convertDigits')) {
    function convertDigits($string)
    {
        if($string === null)
            return null;
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);

        return $englishNumbersOnly;
    }
}

function toGregorian($date = '',$separator = '-',$separateTo = '-', $showHour = true)
{
    if(empty($date))
        return '';
    $dateTime = explode(' ',$date);
    if(count($dateTime) > 1) {
        list($date, $time) = $dateTime;
        $time = ' '.$time;
    }
    else{
        $date = $dateTime[0];
        $time = '';
    }
    $exp = explode($separator, convertDigits($date));
    $date = implode($separateTo, Verta::jalaliToGregorian($exp[0],$exp[1],$exp[2]));
    $dateExp = explode($separateTo,$date);
    $dateExp[1] = strlen($dateExp[1]) === 1 ? '0'.$dateExp[1] : $dateExp[1];
    $dateExp[2] = strlen($dateExp[2]) === 1 ? '0'.$dateExp[2] : $dateExp[2];
    $date = implode($separateTo,$dateExp);
    if($showHour == true)
        return $date.$time;
    else
        return $date;

}

function xDistrict($point, $polygon = null)
{
    if($polygon === null) {
        $polygon = Polygon::all();
    }
    $district = [];

    foreach($polygon as $poly) {
        $vs = json_decode($poly->polygon);
        $inside = false;
        $x = $point[0]; // lng
        $y = $point[1]; // lat

        for ($i = 0, $j = count($vs) - 1; $i < count($vs); $j = $i++) {
            $xi = $vs[$i][0];
            $yi = $vs[$i][1];
            $xj = $vs[$j][0];
            $yj = $vs[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) $inside = !$inside;
        }

        if ($inside) {
            array_push($district, $poly->region);
        }
    }
    return implode(' ,', $district);
}

function isLocationInsidePolygon($driverId,$point)
{
    $polygon = PolygonDriver::where('user_id', $driverId)->with('polygon')->get();

    foreach($polygon as $poly) {
        $vs = json_decode($poly->polygon->polygon);
        $inside = false;
        $x = $point[0];
        $y = $point[1];

        for ($i = 0, $j = count($vs) - 1; $i < count($vs); $j = $i++) {
            $xi = $vs[$i][0];
            $yi = $vs[$i][1];
            $xj = $vs[$j][0];
            $yj = $vs[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect) $inside = !$inside;
        }

        if ($inside) {
            return $inside;
        }
    }
}

function sendJson($status = 'success', $message = '', $data = null,$state = null){
    if($state == null){
        //$state = $result == 1 ? 200 : 400;
        $state = 200;
    }
    return response()->json([
        'status'  => $status,
        'message' => $message,
        'data'    => $data,
    ], $state)->header('Content-type', 'application/json');
    die;
}


function wageToman()
{
    return 700;
}

function wageRial()
{
    return wageToman()*10;
}

function userRewardToman()
{
    return Cache::get('userReward');
}

function userRewardRial()
{
    return userRewardToman()*10;
}

function referrerRewardToman()
{
    return 30000;
}

function referrerRewardRial()
{
    return referrerRewardToman()*10;
}


function referrerRewardAbove50KiloToman()
{
    return 20000;
}

function referrerRewardAbove50KiloRial()
{
    return referrerRewardAbove50KiloToman()*10;
}

function minWithdrawCardToCardToman()
{
    return 10000;
}

function minWithdrawAapToman()
{
    return 10000;
}

function levelIcon($level = 2)
{
    if($level == 2){
        return "<i class='bx bxs-heart cr-heartbeat' style='color:#ff0303'></i>";
    }
}


function neshanGetDistance($distances)
{
    try {
        $from = implode('|',$distances['from']);
        $to = implode('|',$distances['to']);
        $response = Http::withHeaders([
            'Api-Key' => 'service.e4b73cca73c74310a5aa1b1b11793e65'
        ])->get("https://api.neshan.org/v1/distance-matrix/no-traffic?type=car&origins=$from&destinations=$to");
        if($response->status() == 200){
            $result= $response->json();
            if (!isset($result['rows'][0]['elements']))
            {
                return ['status'=>false,'index'=>null];
            }
            $points=$result['rows'][0]['elements'];
            $nearests=[];
            foreach ($points as $point)
            {
                $nearests[]=$point['distance']['value'];
            }
            $minValue = min($nearests);
            $minIndex = array_search($minValue, $nearests);
            return ['status'=>true,'index'=>$minIndex];
        }
    }
    catch (Exception $e){
        return ['status'=>false,'index'=>null];
    }
    return ['status'=>false,'index'=>null];
}

function getDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371) {
    // تبدیل درجات به رادیان
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    // محاسبه تفاوت مختصات
    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    // فرمول هارورسین
    $a = sin($latDelta / 2) * sin($latDelta / 2) +
        cos($latFrom) * cos($latTo) *
        sin($lonDelta / 2) * sin($lonDelta / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    // محاسبه فاصله
    return $earthRadius * $c; // فاصله به کیلومتر
}

function getDistanceHopper($points)
{
    $response = Http::get('https://graphhopper.com/api/1/route?'.$points.'&vehicle=car&key=ba38c972-229a-4e03-b1eb-ed2a9b483bdf');
    $route = $response->json();
    if (isset($route['paths']))
    {
        return $route['paths'][0]['distance']??0;
    }
    return 0;
}
function getVrpHopper($driver,$submits,$key)
{
    $url = "https://graphhopper.com/api/1/vrp?key=ba38c972-229a-4e03-b1eb-ed2a9b483bdf";
    $serviceCount = 1;
    $vehicles[] = [
        'vehicle_id' => 'v1' ,
        'type_id' => 'custom_vehicle_type',
        'start_address' => [
            'location_id' => 'v1',
            'lat' => $driver[0],
            'lon' => $driver[1]
        ],
        'earliest_start' => 1508839200,  // You can set this dynamically
    ];
// Create services
    foreach ($submits as $index => $location) {
        $services[] = [
            'id' => 's' . $serviceCount,
            'type' => 'service',
            'address' => [
                'location_id' => 's' . $serviceCount,
                'lat' => $location[0],
                'lon' => $location[1]
            ],
            'duration' => 120  // Example duration
        ];
        $serviceCount++;
    }

// Combine into the final data structure
    $data = [
        'vehicles' => $vehicles,
        'vehicle_types' => [
            [
                'type_id' => 'custom_vehicle_type',
                'profile' => 'car'
            ]
        ],
        'services' => $services
    ];
    $responseData=send_vrp($url,$data);
    if (isset($responseData['solution'])) {
        return $responseData['solution']['distance']??0;
    }
    else{
        $responseData=send_vrp("https://graphhopper.com/api/1/vrp?key=8425051f-6566-4f4c-af19-c96de9210f42",$data);
        if (isset($responseData['solution'])) {
            return $responseData['solution']['distance']??0;
        }
        else{
            return 0;
        }
    }
}
function send_vrp($url,$data)
{
    $jsonData = json_encode($data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Accept: application/json"
    ]);
    $response = curl_exec($ch);
    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);
    return json_decode($response, true);
}
function getDistanceOsrm($locations = [])
{
    $items = implode(';',$locations);
    $response = Http::get("https://router.project-osrm.org/route/v1/driving/$items?overview=false");
    $data = json_decode($response->getBody(), true);
    $distance = $data['routes'][0]['distance']; // Distance in meters
    $duration = $data['routes'][0]['duration']; // Duration in seconds
    return $data;
}

function getUsersWithAverageWeights()
{
    $usersWithAverageWeight = User::select(['users.id','users.guild_title','users.name','users.lastname',DB::raw('AVG(drivers.weights) as average_weight')])
        ->join('submits', function($join) {
            $join->on('users.id', '=', 'submits.user_id')
                ->where('submits.status', '=', 3);
        })
        ->join('drivers', 'submits.id', '=', 'drivers.submit_id')
        ->groupBy('users.id')
        ->having(DB::raw('AVG(drivers.weights)'), '>=', 50)
        ->orderBy('average_weight', 'desc');
    return $usersWithAverageWeight->paginate(10);
}function getTopUsers()
{
    $usersWithAverageWeight = User::select(['id','name','lastname','mobile'])
        ->where('level', '=', 2)
        ->orderBy('id', 'asc');
    return $usersWithAverageWeight->paginate(100);
}
function getPendInax()
{
    $pends = \App\Models\Inax::query()->select(['id','order_id','amount','operator','user_id','mobile','created_at'])
        ->where('status', '=', 'done')
        ->whereNull('trans_id')
        ->whereNull('ref_code')
        ->whereDate('created_at', '>', '2025-03-16')
        ->with('user')
        ->orderBy('id', 'desc');
    return $pends->paginate(100);
}
function create_transaction($source_user_id,$des_user_id,$amount,$d_pay_type,$w_pay_type,$reason,$reason_id)
{
    $transaction=new TransactionService();
    $transaction->AddTransaction($source_user_id,$des_user_id,$amount,$d_pay_type,$w_pay_type,$reason,$reason_id);
}
function getDriverRegionSubmits($polygones)
{
    $polygons = Polygon::whereIn('id',$polygones)->pluck('region')->toArray();
    $submitsPending = Submit::where('status', 1)->whereDate('start_deadline',now())
        ->with(['user', 'address' => function ($query) {
            $query->withTrashed();
        }])->get();
    $filtered = [];
    foreach ($submitsPending as $item) {
        if(in_array(xDistrict([$item->address->lat,$item->address->lon]),$polygons)){
            $filtered[] = $item;
        }
    }
    return $filtered;
}
function getAddressRegion($point)
{
    $polygon = Polygon::all();
    $district =null;

    foreach($polygon as $poly) {
        $vs = json_decode($poly->polygon);
        $inside = false;
        $x = $point[0]; // lng
        $y = $point[1]; // lat

        for ($i = 0, $j = count($vs) - 1; $i < count($vs); $j = $i++) {
            $xi = $vs[$i][0];
            $yi = $vs[$i][1];
            $xj = $vs[$j][0];
            $yj = $vs[$j][1];

            $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);
            if ($intersect){
                $inside = !$inside;
            }
        }

        if ($inside) {
            $district=$poly->id;
        }
    }
    return $district;

}
function driverNearRequest($driverLatitude,$driverLongitude,$polygons,$add_one=false,$except_ids=[])
{
    $currentTime=\Carbon\Carbon::now();
    $user = User::find(8);
    $driver_id=$user->id;
    $first_nearest =findNearest($driverLatitude,$driverLongitude,$polygons,$except_ids);
    if ($first_nearest)
    {
        $ids=[];
        $ids[]=$first_nearest->id;
        \App\Models\DriverSuggestedRequests::query()->create(['driver_id'=>$driver_id,'submit_id'=>$first_nearest->id]);
        $submit=Submit::where('id',$first_nearest->id)
            ->with(['address' => function ($query) {
                $query->withTrashed();
            }])->first();
        if (!$add_one)
        {
            $second_nearest=findNearest($submit->address->lat,$submit->address->lon,$polygons,[$first_nearest->id]);
            if ($second_nearest)
            {
                \App\Models\DriverSuggestedRequests::query()->create(['driver_id'=>$driver_id,'submit_id'=>$second_nearest->id]);
                $ids[]=$second_nearest->id;
            }
        }
        return $ids;
    }
    else{
        return [];
    }
}
function findNearest($driverLatitude,$driverLongitude,$polygons,$submit_id=[])
{
    $currentTime=now();
    $nearest= \Illuminate\Support\Facades\DB::table('submits as s')
        ->join('addresses as ad', 's.address_id', '=', 'ad.id')
        ->select(
            'ad.lat',
            'ad.lon',
            's.id',
            's.start_deadline',
            's.end_deadline',
            's.is_instant',
            \Illuminate\Support\Facades\DB::raw("HOUR(s.start_deadline) as h"),
            \Illuminate\Support\Facades\DB::raw("
          (6371 * acos(
                cos(radians($driverLatitude))
                * cos(radians(ad.lat))
                * cos(radians(ad.lon) - radians($driverLongitude))
                + sin(radians($driverLatitude))
                * sin(radians(ad.lat))
            )) AS distance
        "), // محاسبه فاصله از راننده
            \Illuminate\Support\Facades\DB::raw("TIMESTAMPDIFF(SECOND, '$currentTime', s.end_deadline) AS time_to_end")
        )
        ->whereDate('start_deadline', $currentTime) // فیلتر تاریخ
        ->whereIn('s.region_id', $polygons) // فیلتر مناطق
        ->where('s.status', 1) // فقط نقاط فعال
        ->orderBy('time_to_end')
        ->orderBy('distance') // مرتب‌سازی بر اساس فاصله
        ->orderByDesc('s.is_instant') // اولویت درخواست فوری
        ->orderBy('h');
    if (count($submit_id)>0)
    {
        $nearest=$nearest->whereNotIn('s.id',$submit_id);
    }
    return $nearest->first();
}
function move_first($data,$targetIds)
{

    $movedItems = $data->filter(function ($item) use ($targetIds) {
        return in_array($item['id'], $targetIds);
    });

// جدا کردن عناصر باقی‌مانده
    $remainingItems = $data->filter(function ($item) use ($targetIds) {
        return !in_array($item['id'], $targetIds);
    });

// ترکیب دو مجموعه
    return $movedItems->concat($remainingItems);

}
function getOptimizedRoute($locations)
{
    $requestData = [
        "configuration" => [
            "routing" => [
                "calc_points" => true,
                "return_snapped_waypoints" => true
            ]
        ],
        "objectives" => [
            [
                "type" => "min",
                "value" => "completion_time"
            ]
        ],
        "vehicles" => [
            [
                "vehicle_id" => "v1",
                "type_id" => "custom_vehicle_type",
                "earliest_start" => time(), // Current time
                "return_to_depot" => true
            ]
        ],
        "vehicle_types" => [
            [
                "type_id" => "custom_vehicle_type",
                "profile" => "car"
            ]
        ],
        "services" => []
    ];

    foreach ($locations as $index => $location) {
        $requestData['services'][] = [
            "id" => "s" . ($index + 1),
            "type" => "service",
            "address" => [
                "location_id" => "s" . ($index + 1),
                "lat" => $location['lat'],
                "lon" => $location['lon']
            ],
            "duration" => 60 // Default duration per service
        ];
    }

    $jsonData = json_encode($requestData);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://graphhopper.com/api/1/vrp?key=ba38c972-229a-4e03-b1eb-ed2a9b483bdf" );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "cURL error: " . curl_error($ch);
    }

    curl_close($ch);

    return json_decode($response, true);
}
function getOptimizedRouteWithOSRM($locations, $osrmUrl)
{
    // Build the coordinates string for the OSRM API
    $coordinates = array_map(function ($location) {
        return $location['lon'] . ',' . $location['lat']; // OSRM expects longitude,latitude
    }, $locations);

    $coordinatesString = implode(';', $coordinates);

    // OSRM API Endpoint for Trip optimization
    $url = $osrmUrl . "/trip/v1/driving/" . $coordinatesString ;

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return 0;
    }
    $data= json_decode($response, true);
    if (isset($data['trips'][0]['distance']))
    {
        return $data['trips'][0]['distance'];
    }
    return 0;
}
function test_drivers()
{
    $query = User::with('roles','cars')->whereHas('roles', function ($query) {
        $query->where('name', 'driver');
    })->whereHas('cars', function ($query){
        $query->where('is_active', 1);
    })->get()->pluck('id')->toArray();
    return $query;
}
function neshan_route($user_id,$start,$end)
{
    try {
        $response = Http::timeout(6)->withHeaders([
            'Api-Key' => 'service.e4b73cca73c74310a5aa1b1b11793e65'
        ])->get("https://api.neshan.org/v4/direction/no-traffic?origin={$start}&destination={$end}");
        if ($response->ok())
        {
            $route=$response->json();
            NeshanApiLog::query()->create(['is_driver_location'=>0,'user_id'=>$user_id,'start_info'=>0,'endpoint'=>'/v4/direction/no-traffic','request_data'=>"origin={$start}&destination={$end}",'response_data'=>$response->json(),'status_code'=>$response->status()]);
            if (isset($route['routes'][0]['legs'][0]['distance']['value']))
            {
                return $route['routes'][0]['legs'][0]['distance']['value'];
            }
            return 0;
        }
        else{
            NeshanApiLog::query()->create(['is_driver_location'=>0,'user_id'=>$user_id,'start_info'=>0,'endpoint'=>'/v4/direction/no-traffic','request_data'=>"origin={$start}&destination={$end}",'response_data'=>$response->json(),'status_code'=>$response->status()]);
            return 0;
        }
    }catch (Exception $exception){
        return 0;
    }

}
function current_driver_weight($user_id)
{
    return round(DB::table('drivers')
        ->where('user_id', $user_id)
        ->whereDate('collected_at', \Carbon\Carbon::now()->format('Y-m-d'))
        ->sum('weights'));
}
function today_fin($date)
{
    $results = DB::select("
    SELECT CONCAT_WS('_', 'inax', status) as type , sum(amount*10) as amount
    FROM inaxes
    WHERE DATE(created_at) = ?
    GROUP BY status

    UNION ALL

    SELECT CONCAT_WS('_', 'cashout', status) as type , sum(amount*10) as amount
    FROM cashouts
    WHERE DATE(updated_at) = ?
    GROUP BY status

    UNION ALL

    SELECT CONCAT_WS('_', 'asan_deposite', type) as type , sum(amount) as amount
    FROM asan_pardakhts
    WHERE DATE(created_at) = ? AND method = 'واریز'
    GROUP BY type

    UNION ALL

    SELECT CONCAT_WS('_', 'asan_withdraw', type) as type , sum(amount) as amount
    FROM asan_pardakhts
    WHERE DATE(created_at) = ? AND method = 'برداشت'
    GROUP BY type

    UNION ALL

    SELECT CONCAT_WS('_', 'bazist_deposite', type) as type , sum(amount) as amount
    FROM wallet_details
    WHERE DATE(created_at) = ? AND method = 'واریز'
    GROUP BY type

    UNION ALL

    SELECT CONCAT_WS('_', 'bazist_withdraw', type) as type , sum(amount) as amount
    FROM wallet_details
    WHERE DATE(created_at) = ? AND method = 'برداشت'
    GROUP BY type
", [$date, $date, $date, $date, $date, $date]);
    $insert=[];
    foreach ($results as $row) {
        $insert[$row->type]=$row->amount;
    }
    return $insert;

}
function current_driver_salary($user_id)
{
    $submits=DriverSuggestedRequests::query()->where('driver_id',$user_id)->whereDate('created_at',\Carbon\Carbon::now()->format('Y-m-d'))->where('status',2)->orderBy('id','asc')->get()->pluck('submit_id')->toArray();
    $salary=\Illuminate\Support\Facades\Cache::get('salary-'.auth()->id());
    if ($salary)
    {
        if ($salary['count'] != count($submits))
        {
            return round(calculate_driver_salary($submits,$user_id));
        }
        return round($salary['amount']??0) ;
    }
    else{
        return round(calculate_driver_salary($submits,$user_id));
    }

}
function driver_average_distance($user_id)
{
    return DB::table('drivers_salary_details')
        ->where('user_id', $user_id)
        ->where('distance', '!=', 0)
        ->avg('distance');
}
function calculate_driver_salary($submits,$user_id)
{
    $reward=0;
    if (count($submits)>0)
    {
        $start=null;
        foreach ($submits as $submit)
        {
            $weights=getWeights($submit);
            if (in_region($start,$submit,$user_id))
            {
//                $distance=current_driver_distance($start,$submit);
                $distance=driver_average_distance($user_id);
                if ($distance>0){
                    $distance=$distance-(($distance*30)/100);
                }

            }
            else{
                $distance=0;
            }
            $start=$submit;
            $reward+=getReward($weights,$distance);
        }
        \Illuminate\Support\Facades\Cache::set('salary-'.auth()->id(),['count'=>count($submits),'amount'=>$reward],900);
        $salary=\Illuminate\Support\Facades\Cache::get('salary-'.auth()->id());
        return $salary['amount']??0;
    }
    return 0;
}
function calculate_driver_salary_with_detail($submits,$user_id)
{
    $info=[];
    $reward=0;
    if (count($submits)>0)
    {
        $start=null;
        foreach ($submits as $submit)
        {
            $weights=getWeights($submit);
            if (in_region($start,$submit,$user_id))
            {
//                $distance=current_driver_distance($start,$submit);
                $distance=driver_average_distance($user_id);
                if ($distance>0){
                    $distance=$distance-(($distance*30)/100);
                }

            }
            else{
                $distance=0;
            }
            $start=$submit;
            $item_reward=getReward($weights,$distance);
            $reward+=$item_reward;
            $info[]=[$weights,$distance,$item_reward];
        }
        return $info;
    }
    return 0;
}
function current_driver_distance($start,$submit)
{
    if (!isset($start))
    {
        return 0;
    }
    $start_submit=Submit::query()->where('id',$start)->first();
    $end_submit=Submit::query()->where('id',$submit)->first();
    if ($start_submit)
    {
        $start_location=Address::query()->where('id',$start_submit->address_id)->first();
        if ($start_location)
        {
            $s=$start_location->lat.','.$start_location->lon;
        }
    }
    if ($end_submit)
    {
        $end_location=Address::query()->where('id',$end_submit->address_id)->first();
        if ($end_location)
        {
            $e=$end_location->lat.','.$end_location->lon;
        }
    }
    try {
        $url = "http://router.project-osrm.org/route/v1/driving/{$s};{$e}?overview=false";

        $response = file_get_contents($url);
        $data = json_decode($response, true);
        if (isset($data['routes'][0]['distance'])) {

            $avg= $data['routes'][0]['distance'];
        }
        else{
            $avg=0;
        }
    }catch (\Exception $exception){
        $avg=0;
    }
    return $avg;
}
function getReward($weight,$distance)
{
    $const=3500;
    return ($const*($weight*0.5))+(4*$const)+($const*(2*($distance/1000)));
}
function getWeights($submit)
{
    $driver=Driver::query()->where('submit_id',$submit)->first();
    if ($driver)
    {
        $recieves=Receive::query()->select(DB::raw('SUM(weight) as weight'))->where('driver_id',$driver->id)->first()->weight??0;
    }
    else{
        $recieves = 0;
    }
    return $recieves;
}
function in_region($start,$end,$driver_id)
{
    if (isset($start))
    {
        $start=DriverSuggestedRequests::query()->where('submit_id',$start)->where('driver_id',$driver_id)->whereDate('created_at',\Carbon\Carbon::now()->format('Y-m-d'))->where('status',2)->first();
    }
    $end=DriverSuggestedRequests::query()->where('submit_id',$end)->where('driver_id',$driver_id)->whereDate('created_at',\Carbon\Carbon::now()->format('Y-m-d'))->where('status',2)->first();
    if ( isset($end)  && $end->in_regions) {
        return true;
    }
    return false;

}

function get_waste_amount($date)
{
    return DB::select("
    SELECT SUM(total_amount) AS t_amount
    FROM submits
    WHERE id IN (
        SELECT submit_id
        FROM drivers
        WHERE DATE(collected_at) = ?
    )
", [$date])[0]->t_amount ?? 0;
}
function get_bazist_total_vaariz_amount($date)
{
    $total= App\Models\WalletDetails::whereDate('created_at', $date)->where('method','واریز')->sum('amount');
    return $total>0?$total/10:0;
}
function get_bazist_total_bardaasht_amount($date)
{
    $total=App\Models\WalletDetails::whereDate('created_at', $date)->where('method','برداشت')->sum('amount');
    return $total>0?$total/10:0;
}
function isValidDateFormat($date)
{
    try {
        $d = Carbon::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }catch (Exception $exception){
        return false;
    }
}
function today_prices($date)
{
    return DB::table('recyclable_histories')
        ->whereDate('created_at', '<=', $date)
        ->orderByRaw('ABS(DATEDIFF(created_at, ?))', [$date])
        ->limit(1)->first();
}
function schedule($lat,$lng,$date=null)
{
    $district = xDistrict([$lat,$lng]);
    $polygonDayHours = PolygonDayHour::all();
    $polygon = Polygon::where('region',$district)->first();
    if(!$polygon){
        response()->json(['status'=>403,'result'=>'','message'=>'شما خارج از محدوده هستید'])->send();
        exit();
    }
    $day = Day::all();
    $hour = Hour::all();
    $i=0;
    $h9  = true;
    $h11 = true;
    $h13 = true;
    $h15 = true;
    $h17 = true;
    if((new Verta($date))->format('w')+$i+1 > 7){
        $dayId  = ((new Verta($date))->format('w')+$i+1)-7;
    }
    else{
        $dayId = (new Verta($date))->format('w')+$i+1;
    }

    if($i == 0 && (new Verta($date))->format('G') >= 9){
        $h9 = false;
    }
    elseif(!$polygonDayHours
        ->where('city_id',1)
        ->where('polygon_id',$polygon->id)
        ->where('day_id',$day->where('id',$dayId)->first()->id)
        ->where('hour_id',$hour->where('start_at',9)->first()->id)
        ->first()->status){
        $h9 = false;
    }
    if($i == 0 && (new Verta($date))->format('G') >= 11){
        $h11 = false;
    }
    elseif(!$polygonDayHours
        ->where('city_id',1)
        ->where('polygon_id',$polygon->id)
        ->where('day_id',$day->where('id',$dayId)->first()->id)
        ->where('hour_id',$hour->where('start_at',11)->first()->id)
        ->first()->status){
        $h11 = false;
    }
    if($i == 0 && (new Verta($date))->format('G') >= 13){
        $h13 = false;
    }
    elseif(!$polygonDayHours
        ->where('city_id',1)
        ->where('polygon_id',$polygon->id)
        ->where('day_id',$day->where('id',$dayId)->first()->id)
        ->where('hour_id',$hour->where('start_at',13)->first()->id)
        ->first()->status){
        $h13 = false;
    }
    if($i == 0 && (new Verta($date))->format('G') >= 15){
        $h15 = false;
    }
    elseif(!$polygonDayHours
        ->where('city_id',1)
        ->where('polygon_id',$polygon->id)
        ->where('day_id',$day->where('id',$dayId)->first()->id)
        ->where('hour_id',$hour->where('start_at',15)->first()->id)
        ->first()->status){
        $h15 = false;
    }
    if($i == 0 && (new Verta($date))->format('G') >= 17){
        $h17 = false;
    }
    elseif(!$polygonDayHours
        ->where('city_id',1)
        ->where('polygon_id',$polygon->id)
        ->where('day_id',$day->where('id',$dayId)->first()->id)
        ->where('hour_id',$hour->where('start_at',17)->first()->id)
        ->first()->status){
        $h17 = false;
    }
    $data["From_0900_To_1200"]=$h9;
    $data["From_1100_To_1400"]=$h11;
    $data["From_1300_To_1600"]=$h13;
    $data["From_1500_To_1800"]=$h15;
    $data["From_1700_To_2000"]=$h17;
    if (isset($polygon->has_instant) and $polygon->has_instant == 1)
    {
        $data['Urgent']= (isset($polygon->has_legal_collect) and $polygon->has_legal_collect == 1);
    }else{
        $data['Urgent']=false;
    }
    return $data;
}

function add_request($registrantId,$user,$request,$address_id)
{
    $payMethod ='';

    if($request->scheduling == 'immediate'){
        $start_deadline = now()->format('Y-m-d H:i:s');
        $end_deadline = now()->addHour()->format('Y-m-d H:i:s');
        $is_instant = 1;
    }
    else{
        $start_deadline = verta()->parse($request->scheduling['day'])->addHours($request->scheduling['hour'])->toCarbon();
        $end_deadline = verta()->parse($request->scheduling['day'])->addHours($request->scheduling['hour']+3)->toCarbon();
        $is_instant = 0;
    }
    $address = Address::find($address_id);
    $district = getAddressRegion([$address->lat,$address->lon]);
    $submit = new Submit;
    $submit->registrant_id = $registrantId;
    $submit->user_id = $user->id;
    $submit->start_deadline = $start_deadline;
    $submit->end_deadline = $end_deadline;
    $submit->recyclables = json_encode(['GoodRef' => 1, 'Quantity' => 1, 'Price' => Percentage::where('recyclable_id', 1)->where('is_legal', false)->where('weight', 1)->first()->price * 10]);
    $city_id = $address->city_id;
    $submit->address_id = $address->id;
    $submit->region_id= @$district;
    $submit->city_id = $city_id;
    $submit->cashout_type = $payMethod;
    $submit->is_instant = $is_instant;
    $submit->cashout_instant = 0;
    $submit->submit_phone = 0;
    $submit->save();

    $archive_id = ReceiveArchive::new($submit);
    if ($user->legal) {
        ArchiveLegal::new($submit, $archive_id);
    } else {
        ArchiveNotLegal::new($submit, $archive_id);
    }

    if ($submit->submit_phone) {
        ArchivePhone::new($submit, $archive_id);
    } else {
        ArchiveApp::new($submit, $archive_id);
    }
    return $submit;
}
function get_start_date($start)
{
    $time=str_split($start,2);
    if (isset($time[0]) and (int) $time[0] > 0) {
        $hour=Hour::query()->where('start_at',(int) $time[0])->first();
        if ($hour) {
            return $hour->start_at;
        }
        else{
            response()->json(['status'=>400,'result'=>'','message'=>'بازه زمان ورودی موجود نیست'])->send();
            exit;
        }
    }
    else{
        response()->json(['status'=>400,'result'=>'','message'=>'فرمت زمان ورودی نامعتبر است'])->send();
        exit;
    }


}
function city_name($city_id)
{
    $cities=[1=>'مشهد',3=>'طرقبه'];
    return $cities[$city_id]??'-';
}
function weight_drivers()
{
    return[87326,84765,82579,81848,69198,67164,49718,42431,59853,94164];
}
function weight_drivers_prices($recycable_id)
{
    $recycables=[1=>10000,2=>6000,3=>9000,4=>25000,5=>0,6=>110000,7=>0,8=>9000,9=>4000,10=>40000,11=>4000,12=>8000,13=>0,14=>0,15=>0,16=>15000,17=>9000,18=>65000,19=>5000,20=>0,21=>0,22=>0];
    return $recycables[$recycable_id];
}
//function is_hooshmap($submit,$endpoint,$data)
//{
//    if ($submit->type==1)
//    {
//     $hoosh=new \App\Classes\HooshmapService($submit);
//     $hoosh->action($endpoint,$data);
//    }
//
//}
function transformPaginated($paginator)
{
    return collect([
        'data' => $paginator->items(),
        'pagination' => [
            'total' => $paginator->total(),
            'per_page' => $paginator->perPage(),
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'has_more' => $paginator->hasMorePages(),
        ],
    ]);
}
function walletTransaction($city_id,$user_id,$wallet_id,$type,$type_id,$amount,$new_balance,$method,$description)
{
    $wallet = new WalletDetails;
    $wallet->city_id = $city_id??1;
    $wallet->user_id = $user_id;
    $wallet->wallet_id = $wallet_id;
    $wallet->type = $type;
    $wallet->type_id = $type_id;
    $wallet->amount = $amount;
    $wallet->wallet_balance = $new_balance;
    $wallet->method = $method;
    $wallet->details = $description;
    $wallet->save();
}

 function success_response($data = null, $message = 'success', $status = 200)
{
    // اگر پاسخ صفحه‌بندی بود (paginate or simplePaginate)
    if ($data instanceof LengthAwarePaginator || $data instanceof Paginator) {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'pagination' => [
                'current_page' => $data->currentPage(),
                'per_page'     => $data->perPage(),
                'total'        => method_exists($data, 'total') ? $data->total() : null,
                'last_page'    => method_exists($data, 'lastPage') ? $data->lastPage() : null,
            ],
            'data' => $data->items(),
        ], $status);
    }

    // اگر صفحه‌بندی نبود:
    return response()->json([
        'status'  => true,
        'message' => $message,
        'data'    => $data
    ], $status);
}

 function error_response($message = 'error', $status = 400, $errors = null)
{
    return response()->json([
        'status'  => false,
        'message' => $message,
        'errors'  => $errors
    ], $status);
}
function predict_illnessV2($question)
{
    set_time_limit(300);

    $question = str_replace(["\r", "\n"], '', $question);

    $data = [
        "model" => "hf.co/ujjman/llama-3.2-3B-Medical-QnA-unsloth:Q8_0",
        "messages" => [
            [
                "role" => "system",
                "content" => "Based on symptoms, return possible diseases with short description and probability between 0 and 1 in valid JSON format."
            ],
            [
                "role" => "user",
                "content" => $question
            ]
        ],
        "stream" => false,
        "format" => [
            "type" => "object",
            "properties" => [
                "predicted_diseases" => [
                    "type" => "array",
                    "items" => [
                        "type" => "object",
                        "properties" => [
                            "name" => [
                                "type" => "string",
                                "description" => "Disease name"
                            ],
                            "description" => [
                                "type" => "string",
                                "description" => "Short explanation of disease"
                            ],
                            "probability" => [
                                "type" => "number",
                                "description" => "Probability between 0 and 1"
                            ]
                        ],
                        "required" => ["name", "description", "probability"]
                    ]
                ]
            ],
            "required" => ["predicted_diseases"]
        ]
    ];

    try {
        $response = Http::timeout(300)
            ->post('http://ollama:11434/api/chat', $data);

        if ($response->successful()) {
            $responseData = $response->json();

            $content = $responseData['message']['content'] ?? null;

            if ($content) {
                $decoded = json_decode($content, true);
                return $decoded['predicted_diseases'] ?? [];
            }
        }

        return [];
    } catch (\Exception $e) {
        return [
            [
                "name" => "Error",
                "description" => $e->getMessage(),
                "probability" => 0
            ]
        ];
    }
}
function predict_illnessV3($question)
{
    set_time_limit(300);

    $question = str_replace(["\r", "\n"], '', $question);

    $data = [
        "model" => "hf.co/ujjman/llama-3.2-3B-Medical-QnA-unsloth:Q8_0",
        "messages" => [
            [
                "role" => "system",
                "content" => "Based on symptoms, return a single disease with the highest probability along with a short description and probability between 0 and 1 in valid JSON format."
            ],
            [
                "role" => "user",
                "content" => $question
            ]
        ],
        "stream" => false,
        "format" => [
            "type" => "object",
            "properties" => [
                "predicted_disease" => [
                    "type" => "object",
                    "properties" => [
                        "name" => ["type" => "string", "description" => "Disease name"],
                        "description" => ["type" => "string", "description" => "Short explanation of disease"],
                        "probability" => ["type" => "number", "description" => "Probability between 0 and 1"]
                    ],
                    "required" => ["name", "description", "probability"]
                ]
            ],
            "required" => ["predicted_disease"]
        ]
    ];

    try {
        $response = Http::timeout(300)->post('http://ollama:11434/api/chat', $data);

        if ($response->successful()) {
            $responseData = $response->json();
            $content = $responseData['message']['content'] ?? null;

            if ($content) {
                $decoded = json_decode($content, true);

                // اگر مدل همچنان آرایه چندتایی داد، بیشترین probability را انتخاب کن
                if (isset($decoded['predicted_diseases']) && is_array($decoded['predicted_diseases'])) {
                    $top = collect($decoded['predicted_diseases'])
                        ->sortByDesc('probability')
                        ->first();

                    return $top ? [$top] : [];
                }

                // اگر مدل خودش predicted_disease یکتا فرستاده
                if (isset($decoded['predicted_disease'])) {
                    return [$decoded['predicted_disease']];
                }
            }
        }

        return [];
    } catch (\Exception $e) {
        return [
            [
                "name" => "Error",
                "description" => $e->getMessage(),
                "probability" => 0
            ]
        ];
    }
}
function predict_illness($question)
{
    set_time_limit(300);

// حذف خط جدید
    $question = str_replace(["\r", "\n"], '', $question);

// داده ارسالی
    $data = [
        "model" => "hf.co/ujjman/llama-3.2-3B-Medical-QnA-unsloth:Q8_0",
        "messages" => [
            [
                "role" => "user",
                "content" => $question
            ]
        ],
        "stream" => false,
        "format" => [
            "type" => "object",
            "properties" => [
                "predicted_diseases" => [
                    "type" => "array",
                    "items" => ["type" => "string"],
                    "description" => "List of predicted diseases based on symptoms"
                ]
            ],
            "required" => ["predicted_diseases"]
        ]
    ];

    try {
        $response = Http::timeout(300) // معادل set_time_limit
        ->post('http://ollama:11434/api/chat', $data);

        if ($response->successful()) {
            $responseData = $response->json();

            // اگر مدل، JSON را داخل content برمی‌گرداند
            $illnesses = isset($responseData['message']['content'])
                ? json_decode($responseData['message']['content'], true)
                : null;

            // آرایه بیماری‌ها را برگردان
            return $illnesses['predicted_diseases'] ?? [];
        }

        return []; // اگر پاسخ موفق نبود
    } catch (\Exception $e) {

        return [$e->getMessage()];
    }

}
function translateExample($text,$s='fa',$d='en')
{
    $tr = new GoogleTranslate($d); // زبان مقصد
    $tr->setSource($s);          // تشخیص خودکار زبان
    $result = $tr->translate($text);
    return $result; // "Hello, how are you?"
}
