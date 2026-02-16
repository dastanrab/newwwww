<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cache extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'value', 'expired_at'];

    public $timestamps = false;

    public static function internetPackage()
    {
        $expired = 3; // hour
        $internet = Cache::where('name', 'internetPackage')->first();
        if(!$internet){
            $getPackage = Inax::getProducts();
            $json = json_encode(Inax::filterProduct($getPackage));
            $internet = Cache::create(['name' => 'internetPackage', 'value' => $json, 'expired_at' => now()->addHours($expired)]);
        }
        if($internet->expired_at < now()){
            $getPackage = Inax::getProducts();
            $json = json_encode(Inax::filterProduct($getPackage));
            $internet->update(['name' => 'internetPackage', 'value' => $json, 'expired_at' => now()->addHours($expired)]);
            $internet = Cache::where('name', 'internetPackage')->first();
        }
        $internet = json_decode($internet->value);

        return $internet;
    }

    public static function findInternetPackage($productId,$operator,$internetType,$simType)
    {
        $packages = Cache::internetPackage();
        $packages = collect($packages);
        $packages = $packages->filter(function ($item) use ($operator){
            return $item->value == $operator;
        })->first();
        if($packages) {
            $packages = collect($packages->packages)->filter(function ($package) use ($productId, $internetType, $simType) {
                return $package->id == $productId && $package->type->value == $internetType && $package->simType->value == $simType;
            });
            return $packages->first();
        }
        return null;
    }

    public static function get($name)
    {
        $data = Cache::where('name',$name)->first();
        if($data)
            return $data->value;
        return null;
    }

}
