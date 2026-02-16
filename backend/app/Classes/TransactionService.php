<?php

namespace App\Classes;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    const APP_TYPE =1;
    const BAZIST_TYPE =2;
    const CASHOUT_TYPE =3;
    const WASTE_RREASON = 1;
    const FIRST_SUBMIT_RREASON = 3;
    const SUBMIT_USER_REASON = 2;
    const DRIVER_REWARD_RREASON = 4;
    const DEPOSITE = 1;
    const WITHDRAWAL = 0;
    private $showError;
    private Transaction $transaction;

    public function __construct($showError = false)
  {
      $this->showError = $showError;
      $this->transaction=new Transaction();
  }
  public function PolyName($id)
  {
      $names = [1=>'App\Models\Submit',4=>'App\Models\DriversSalaryPay'];
      return $names[$id];
  }
  public function SafeAddTransaction($source_user_id,$des_user_id,$amount,$d_pay_type,$w_pay_type,$reason,$reason_id)
  {
      try {
          DB::beginTransaction();
          $this->AddTransaction($source_user_id,$des_user_id,$amount,$d_pay_type,$w_pay_type,$reason,$reason_id);
          DB::commit();
      }catch (\Exception $exception)
      {
          DB::rollBack();
          $this->ThrowTransactionError($exception->getMessage());
      }

  }
  public function AddTransaction($source_user_id,$des_user_id,$amount,$d_pay_type,$w_pay_type,$reason,$reason_id)
  {
      try {
          $data=$this->PrepareData($source_user_id,$des_user_id,$amount,$reason,$reason_id);
          $deposite_data=$this->SetTransactionType($data,1,$d_pay_type);
          $this->AddDeposite($deposite_data);
          $withdrawal_data=$this->SetTransactionType($data,0,$w_pay_type);
          $this->AddWithdrawal($withdrawal_data);
      }catch (\Exception $exception)
      {
          $this->ThrowTransactionError($exception->getMessage());
      }
  }
  public function AddDeposite($data)
  {
      try {
          $this->transaction->create($data);
      }catch (\Exception $exception){
          $this->ThrowTransactionError($exception->getMessage());
      }
  }
  public function AddWithdrawal($data)
  {
      try {
          $this->transaction->create($data);
      }catch (\Exception $exception){
          $this->ThrowTransactionError($exception->getMessage());
      }
  }
  public function PrepareData($source_user_id,$des_user_id,$amount,$reason,$reason_id)
  {
      $data['s_user_id']=$source_user_id;
      $data['d_user_id']=$des_user_id;
      $data['amount']=$amount;
      $data['reason']=$reason;
      $data['transactionable_id']=$reason_id;
      $data['transactionable_type']=$this->PolyName($reason);
      return $data;
  }
  public function SetTransactionType(array $data,$type,$pay_type)
  {
      if ($type == self::DEPOSITE)
      {
          $data['type'] = 1;
          $data['pay_type']=$pay_type;
          return $data;
      }
      $data['type'] = 0;
      $data['pay_type']=$pay_type;
      $data['amount']=-$data['amount'];
      return $data;

  }
  private function ThrowTransactionError($message)
  {
      if ($this->showError)
      {
          throw new \Exception($message??'خطا در تراکنش');
      }

  }
}
