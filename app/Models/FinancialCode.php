<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialCode extends Model
{
    use HasFactory;

    // نام جدول (اگر نام جدول با convention لاراول متفاوت باشد)
    protected $table = 'financial_codes';

    // ستون‌هایی که قابل Mass Assignment هستند
    protected $fillable = [
        'status',
        'type',   // deposit یا withdraw
        'title',
    ];

    // رابطه با BazistWallet
    public function walletTransactions()
    {
        return $this->hasMany(WalletDetails::class, 'type', 'id');
    }
}
