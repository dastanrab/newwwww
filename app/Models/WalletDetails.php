<?php

namespace App\Models;

use App\RecordsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletDetails extends Model
{
    use HasFactory;
    use RecordsActivity;

    // type: model name, type_id: model id, method: واریز / برداشت
    protected $fillable = [
        'city_id', 'user_id', 'wallet_id', 'type', 'type_id', 'amount', /*rial*/  'wallet_balance', /*rial*/  'method', 'details'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public static function create($city_id, $user_id, $wallet_id, $type, $type_id, $amount, $wallet_balance, $method, $details)
    {
        $withdraw = new WalletDetails;
        $withdraw->city_id = $city_id;
        $withdraw->user_id = $user_id;
        $withdraw->wallet_id = $wallet_id;
        $withdraw->type = $type;
        $withdraw->type_id = $type_id;
        $withdraw->amount = $amount;
        $withdraw->wallet_balance = $wallet_balance;
        $withdraw->method = $method;
        $withdraw->details = $details;
        $withdraw->save();
        return $withdraw;
    }
    function createWalletRecord(
        $city_id,
        $user_id,
        $wallet_id,
        $method,      // 'deposit' یا 'withdraw'
        $relatedId,   // شناسه رکورد مرتبط
        $amount,
        $wallet_balance,
        $details
    ) {
        // پیدا کردن کد مالی مرتبط
        $financialCode = financialCode::where('type', $method)->first();

        $withdraw = new WalletDetails;
        $withdraw->city_id = $city_id;
        $withdraw->user_id = $user_id;
        $withdraw->wallet_id = $wallet_id;
        $withdraw->method = $method;                // نوع تراکنش واقعی
        $withdraw->type = $financialCode->id ?? null; // کد مالی
        $withdraw->type_id = $relatedId;           // شناسه جدول مربوطه
        $withdraw->amount = $amount;
        $withdraw->wallet_balance = $wallet_balance;
        $withdraw->details = $details;
        $withdraw->save();

        return $withdraw;
    }

}
