<?php

namespace App\Http\Controllers\Api\Dashboard\Drivers;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\City;
use App\Models\Club;
use App\Models\ClubCategory;
use App\Models\Driver;
use App\Models\Offer;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DriversController extends Controller
{
    use AuthorizesRequests;


    public function index(Request $request)
    {
        $activeCount = Car::where('is_active', true)->count();
        $deActiveCount = Car::where('is_active', false)->count();
        $presentTodayCount = Car::where('is_active', true)->where('rollcall_status', '!=', 0)->count();
        $currentPresentCount = Car::where('is_active', true)->where('rollcall_status', 2)->count();
        $absentCount = Car::where('is_active', true)->where('rollcall_status', 0)->count();
        $drivers=$this->getDrivers($request);
        return success_response(['activeCount'=>$activeCount,'deActiveCount'=>$deActiveCount,'drivers'=>$drivers,'presentTodayCount'=>$presentTodayCount,'currentPresentCount'=>$currentPresentCount,'absentCount'=>$absentCount]);
    }
    public function getDrivers(Request $request)
    {
        $validated = $request->validate([
            'status' => 'nullable|in:active,inActive,all',
            'roll_call_status' => 'nullable|in:presentToday,currentPresent,absent',
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $status = $validated['status'] ?? null;
        $rollCallStatus = $validated['roll_call_status'] ?? null;
        $search = $validated['search'] ?? null;
        $perPage = $validated['per_page'] ?? 20;

        $isActive = $status === 'active' ? 1 : 0;

        $query = User::with('roles', 'cars')
            ->whereHas('roles', fn($q) => $q->where('name', 'driver'));

        if ($status !== 'all' && $status !== null) {
            $query->whereHas('cars', fn($q) => $q->where('is_active', $isActive));
        }

        if (!empty($rollCallStatus) && $status === 'active') {
            $query->whereHas('cars', function ($q) use ($rollCallStatus) {
                if ($rollCallStatus === 'presentToday') {
                    $q->where('rollcall_status', '!=', 0);
                } elseif ($rollCallStatus === 'currentPresent') {
                    $q->where('rollcall_status', 2);
                } elseif ($rollCallStatus === 'absent') {
                    $q->where('rollcall_status', 0);
                }
            });
        }

        if (!empty($search)) {
            if (is_numeric($search)) {
                $query->where('mobile', 'LIKE', "%{$search}%");
            } else {
                $query->whereRaw("CONCAT(name, ' ', lastname) LIKE ?", ["%{$search}%"]);
            }
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }
    public function getPresentor($refNum)
    {
        $ref= User::find(User::getUserIdByReferral($refNum));
        return success_response($ref);

    }
    public function addCar(Request $request,$driver){
        $user = User::find($driver);
        if($user->getRole('name') == 'driver'){
            return error_response('کاربر مجاز به ثبت ماشین نیست',403);
        }
        $this->validate([
            'type' => 'required|in:1,2,3,4,5,6,7,8,9',
            'status' => 'required|in:active,deactive',
            'plaque1' => 'required|min:2|max:2',
            'plaque2' => 'required',
            'plaque3' => 'required|min:3|max:3',
            'plaque4' => 'required|min:2|max:2',
        ],[
            'type' => 'نوع خودرو را انتخاب نمایید',
            'status' => 'وضعیت را انتخاب کنید',
            'plaque1' => 'پلاک را به درستی وارد کنید',
            'plaque2' => 'پلاک را به درستی وارد کنید',
            'plaque3' => 'پلاک را به درستی وارد کنید',
            'plaque4' => 'پلاک را به درستی وارد کنید',
        ]);
        $car = Car::insert([
            'driverId' => $request->userId,
            'type' => $request->type,
            'isActive' => $request->status == 'active' ? 1 : 0,
            'plaque1' => $request->plaque1,
            'plaque2' => $request->plaque2,
            'plaque3' => $request->plaque3,
            'plaque4' => $request->plaque4,
        ]);
        return success_response($car);
    }
    public function updateCar(Request $request,$driver){
        $user = User::find($driver);
        $this->validate([
            'type'    => 'required|in:1,2,3,4,5,6,7,8,9',
            'status'  => 'required|in:active,deactive',
            'plaque1' => 'required|min:2|max:2',
            'plaque2' => 'required',
            'plaque3' => 'required|min:3|max:3',
            'plaque4' => 'required|min:2|max:2',
        ],[
            'type'    => 'نوع خودرو را انتخاب نمایید',
            'status'  => 'وضعیت را انتخاب کنید',
            'plaque1' => 'پلاک را به درستی وارد کنید',
            'plaque2' => 'پلاک را به درستی وارد کنید',
            'plaque3' => 'پلاک را به درستی وارد کنید',
            'plaque4' => 'پلاک را به درستی وارد کنید',
        ]);
        $carType = Car::types()->pluck('label','name');
        if($request->status == 'active'){
            $data = [
                'plaque_1'  => $request->plaque1,
                'plaque_2'  => $request->plaque2,
                'plaque_3'  => $request->plaque3,
                'plaque_4'  => $request->plaque4,
                'plaque'    => "$request->plaque1/$request->plaque2/$request->plaque3/$request->plaque4",
                'type'      => $carType[$request->type],
                'type_id'   => $request->type,
                'is_active' => 1,
            ];
        }
        elseif ($request->status == 'deactive'){
            $data = [
                'plaque_1'        => $request->plaque1,
                'plaque_2'        => $request->plaque2,
                'plaque_3'        => $request->plaque3,
                'plaque_4'        => $request->plaque4,
                'plaque'          => "$request->plaque1/$request->plaque2/$request->plaque3/$request->plaque4",
                'type'            => $carType[$request->type],
                'type_id'         => $request->type,
                'is_active'       => 0,
                'rollcall_status' => 0,
            ];
        }

        $user->car->update($data);
        return success_response($user);
    }
    public function car_form_data()
    {

        return success_response([ City::all(), Car::alphabet(), Car::types()]);
    }

}
