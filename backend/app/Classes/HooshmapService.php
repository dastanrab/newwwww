<?php

namespace App\Classes;

use App\Models\Address;
use App\Models\Car;
use App\Models\Driver;
use App\Models\Receive;
use App\Models\Submit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class HooshmapService
{
    private mixed $submit;
    /**
     * @var mixed|null
     */
    private mixed $order_id;

    public function __construct($submit)
    {
        $this->submit = $submit;
        $this->address_id = $submit->address_id;
        $this->order_id=DB::table('hoosh_map_orders')->where('submit_id',$submit->id)->value('hoosh_order_id');
        $this->header=['X-Secret-Key'=>config('services.hooshmap.secret_key','salman')];
        $this->base_url=config('services.hooshmap.base_url','5.252.216.163');
    }

    public function action($method,$data)
    {
        try {
            if (isset($this->end_points()[$method])) {
                $data = $this->{$method.'_inputs'}($data);
                dd($data);
                $result=$this->post($this->end_points()[$method],$data);
                dd('stop');
                if ($result['status']==200) {
                    return true;
                }else{
                    throw new \Exception($result['result']['Message']);
                }
            }
            else{
                throw new \Exception('این متود موجود نیست');
            }

        }catch (\Exception $e){
            dd($e->getMessage());
            $this->log_error($e,json_encode($data));
            return false;
        }
    }

    private function end_points(){
        return ['verify'=>'api/v1/verify','assign'=>'api/v1/assign','abort'=>'api/v1/abort','update'=>'api/v1/update','collect'=>'api/v1/collect','pay'=>'api/v1/pay'];
    }
    private function collect_inputs($data){
       $address=$this->get_address();
        return ['OrderId'=>$this->order_id,'RequestId'=>$this->submit->id,'Amount'=>$this->submit->total_amount,'CollectingDate'=>gmdate('Y-m-d H:i:s'),'DetailJson'=>$this->get_detail(),'Lat'=>$address->lat,'Long'=>$address->lon,'Address'=>$address->address,'Region'=>$address->region,'District'=>$address->district];

    }
    private function abort_inputs($data)
    {
        return ['OrderId'=>$this->order_id,'RequestId'=>$this->submit->id,'DriverCancellationReasonRef'=>$data['cancel_id'],'Message'=>$data['message']];
    }
    private function assign_inputs($data)
    {
        $driver= $this->get_driver_detail();
        return ['OrderId'=>$this->order_id,'RequestId'=>$this->submit->id,'CarTypeRef'=>$driver['car_type'],'Plaque'=>$driver['plaque'],'DriverMobile'=>$driver['mobile'],'Message'=>'درخواست شما توسط راننده قبول شد'];
    }

    private function verify_inputs($data)
    {
        return ['OrderId'=>$this->order_id,'RequestId'=>$this->submit->id,'DeliveryCode'=>$data['delivery_code'],'DetailJson'=>$data['detail']];
    }

    public function post($endpoint,$data)
    {
        try {
            $resquest=Http::timeout(4)->withHeaders($this->header)->post($this->base_url.$endpoint,$data);
            $response=$resquest->json();
        }catch (\Exception $e){
            $this->log_error($e,$data);
        }

    }
    public function get($endpoint){
        try {
            $request=Http::timeout(4)->withHeaders($this->header)->get($this->base_url.$endpoint);
            $response=$request->json();
        }catch (\Exception $e){
             $this->log_error($e,[]);
        }
    }

    private function log_error($e,$data)
    {
        $bale=new BaleService();
        $bale->HooshLog($e,$data);
    }

    private function get_address()
    {
        return Address::withTrashed()->where('id',$this->address_id)->first();
    }

    private function get_detail()
    {
        $driver = Driver::query()->where('submit_id', $this->submit->id)->first();
        $receives = Receive::query()->where('driver_id', $driver->id)->get();
        $detail = [];
        foreach ($receives as $receive) {
            $detail[] = ["Quantity" => $receive->weight, 'Price' =>$receive->price , "GoodRef"=>$receive->fava_id];
        }
        return $detail;
    }

    private function get_driver_detail()
    {
        $driver = Driver::query()->where('submit_id', $this->submit->id)->first();
        $user=User::query()->where('id',$driver->user_id)->first();
        $car = Car::query()->where('id',$driver->car_id)->first();
        return ['mobile'=>$user->mobile,'plaque'=>$car->plaque,'car_type'=>$car->type_id];
    }
}
