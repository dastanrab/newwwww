<?php

namespace App\Livewire\Dashboard\Stats;

use App\Models\BazistWallet;
use App\Models\Inax;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class StatPendInaxIndex extends Component
{
    use WithPagination;


    public $breadCrumb = [['کاربران منتخب','d.stats.top-users']];
    public $trans_id;
    public $ref_id;
    public $showModal = false;



    #[Title('سایر آمار')]

    public function render()
    {
        return view('livewire.dashboard.stats.stat-pend-inax-index');
    }

    #[Computed]
    public function users()
    {
        return  getPendInax();
    }

    public function save(Inax $inax)
    {
        $this->validate([
            'ref_id' => 'required|unique:inaxes,ref_code',
            'trans_id' => 'required|unique:inaxes,trans_id'
        ],[
            'ref_id' => 'شماره مرجع ضروری است',
            'trans_id' => 'شماره تراکنش ضروری است'
        ]);
        $record=Inax::query()->where('id',$inax->id)->first();
        if ($record)
        {
           $record->update(['ref_code'=>$this->ref_id,'trans_id'=>$this->trans_id]);
        }else{

        }
        $this->dispatch('remove-modal');
        return  sendToast(1,'انجام شد');

    }
    public function check_inax(Inax $inax)
    {
        $order=Inax::verifyOrder($inax->order_id);
        if(isset($order['code']) and $order['code']==1){
            if (  $order['payment_status']=='unpaid'){
                $this->returnWallet($inax);
              return  sendToast(1,'درخواست ناموفق بود و به حساب کاربر برگشت داده شد');
            } elseif ($order['payment_status']=='paid')
            {
                $record=Inax::query()->where('id',$inax->id)->first();
                if ($record)
                {
                    DB::transaction(function () use ($record,$order) {
                        $record->update(['ref_code'=>$order['ref_code'],'trans_id'=>$order['trans_id']]);
                    });
                }
                return  sendToast(1,'درخواست موفق بود و شماره تراکنش اضافه شد');
            }
        }elseif(isset($order['code']) and $order['code']==-12){
            $this->returnWallet($inax);
            return  sendToast(1,'درخواست سمت آینکس نبود و به حساب کاربر برگشت داده شد');
        }
        else{
            return  sendToast(1,'نتیجه نا مشخص');
        }

    }
  private function returnWallet($inax)
  {
      if ($inax->method == 'topup')
      {
          $type='back_mobile';
          $text='موبایل';
      }
      else{
          $type='back_internet';
          $text='اینترنت';
      }
      $user=User::query()->where('id',$inax->user_id)->first();
      $city_id = $user->city->id;
      $amount=$inax->amount;
      DB::transaction(function () use ($amount, $user, $city_id,$inax,$type,$text) {
          Inax::where('order_id',$inax->order_id)->update(['status' => 'cancel', 'description' => 'درخواست به علت عدم پاسخگویی انجام نشد']);
          $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
          BazistWallet::create(
              $city_id,
              $user->id,
              $wallet->id,
              $type,
              $wallet->id,
              $amount * 10, // Rial
              ($wallet->wallet + $amount)*10,
              'واریز',
              "برگشت شارژ $text کنسل شده به کیف پول کاربر "
          );
          $wallet->wallet += $amount;
          $wallet->save();
      });
  }

}
