<?php

namespace App\Livewire\Dashboard\Wallet;

use App\Models\Iban;
use App\Models\User;
use Livewire\Component;

class WalletIndexAddCard extends Component
{
    public $user;
    public $card = '';
    public $inquiry = false;
    public $inquiryShaba;
    public $inquiryCard;
    public $inquiryName;
    public $inquiryBank;
    public $btnText = 'استعلام';
    public $btnIcon = 'bx bxs-badge-check';
    public $inquiryInfo;
    public function render()
    {
        return view('livewire.dashboard.wallet.wallet-index-add-card');
    }

    public function deleteAll()
    {
        $this->card = '';
        $this->inquiryInfo = null;
        $this->inquiry = false;
        $this->inquiryShaba = '';
        $this->inquiryCard = '';
        $this->inquiryName = '';
        $this->inquiryBank = '';
        $this->btnText = 'استعلام';
        $this->btnIcon = 'bx bxs-badge-check';
    }

    public function addIban(User $user)
    {
        if($this->inquiry):
            $name = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', explode(' / فعال', $this->inquiryInfo['result']['depositOwners'])[0]);
            $iban = new Iban;
            $iban->user_id = $user->id;
            $iban->name = $name;
            $iban->bank = $this->inquiryInfo['result']['bankName'];
            $iban->deposit = $this->inquiryInfo['result']['deposit'];
            $iban->card = $this->card;
            $iban->iban = $this->inquiryInfo['result']['IBAN'];
            $iban->status = $this->inquiryInfo['result']['depositStatus'];
            $iban->save();
            $this->dispatch('prev-modal', userId : $user->id);
            $this->dispatch('reload-iban', data : [
                'userId' => $user->id,
                'card' => $this->card,
                'ibanId' => $iban->id,
                'name' => $name,
                'bank' => $this->inquiryInfo['result']['bankName'],
            ]);
            $this->deleteAll();
        else:
            $this->validate([
                'card' => 'required|string|size:16',
            ],
            [
                'card' => 'کارت به درستی وارد نشده'
            ]);

            $ibanExists = $user->ibans()->where('card',$this->card)->count();
            if ($ibanExists) {
                sendToast(0,'این شماره کارت قبلا ثبت شده');
            } else {
                $card_to_iban = Iban::cardToIban($this->card);
                //$card_to_iban = false;
                if(!$card_to_iban){
                    sendToast(0,'شماره کارت وارد شده نادرست است.');
                    return;
                }
                $this->inquiryInfo = $card_to_iban;
                $this->inquiry = true;
                $this->inquiryShaba = $card_to_iban['result']['IBAN'];
                $this->inquiryCard = $this->card;
                $this->inquiryName = $card_to_iban['result']['depositOwners'];
                $this->inquiryBank = $card_to_iban['result']['bankName'];
                $this->btnText = 'ثبت اطلاعات کارت';
                $this->btnIcon = 'bx bx-plus';
            }
        endif;
    }
}
