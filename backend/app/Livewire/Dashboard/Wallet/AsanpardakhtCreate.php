<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\AsanPardakht;
use Hekmatinasser\Verta\Verta;
use Livewire\Component;

class AsanpardakhtCreate extends Component
{
    public $amount;
    public $bank;
    public $rrn;
    public $date;
    public function render()
    {
        return view('livewire.dashboard.wallet.asanpardakht-create');
    }

    public function store()
    {
        $this->validate([
            'amount' => 'string',
            'rrn'    => 'string',
            'date'   => 'jdate:Y/m/d',
            'bank'   => 'string'
        ]);

        $created_at = Verta::parse($this->date);
        $created_at = $created_at->formatGregorian('Y-m-d');
        $created_at .= now()->format(' H:i:s');
        $amount = str_replace(',', '', $this->amount);

        $asan_pardakht = new AsanPardakht;
        $asan_pardakht->user_id = auth()->id();
        $asan_pardakht->type = 'asanpardakht_sharj';
        $asan_pardakht->type_id = null;
        $asan_pardakht->host_id = 2408;
        $asan_pardakht->host_tran_id = '1';
        $asan_pardakht->host_req_time = '1';
        $asan_pardakht->host_opcode = 1;
        $asan_pardakht->status_code = 0;
        $asan_pardakht->amount = $amount*10;
        $asan_pardakht->wallet_balance = 0;
        $asan_pardakht->settle_token = '';
        $asan_pardakht->rrn = $this->rrn;
        $asan_pardakht->status_message = $this->bank;
        $asan_pardakht->details = 'واریز به مخزن کیف پول آپ';
        $asan_pardakht->method = 'واریز';
        $asan_pardakht->save();
        $asan_pardakht->created_at = $created_at;
        $asan_pardakht->save();
        //todo add transaction create
        sendToast(1,'با موفقیت ثبت شد');
        $this->dispatch('reload-page');

    }
}
