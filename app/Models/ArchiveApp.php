<?php

namespace App\Models;

use App\RecordsActivity;
use App\Models\Submit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveApp extends Model
{
    use HasFactory;
    use RecordsActivity;

    protected $fillable = [
        'city_id', 'receive_archive_id', 'date', 'type', 'submit_count', 'submit_done', 'submit_first', 'submit_cancel',
        'submit_delete', 'weight', 'value', 'user_pay', 'fava_pay', 'fava_pay_share', 'recyclable_1', 'recyclable_2',
        'recyclable_3', 'recyclable_4', 'recyclable_5', 'recyclable_6', 'recyclable_7', 'recyclable_8',
        'recyclable_9', 'recyclable_10', 'recyclable_11', 'recyclable_12', 'recyclable_13', 'recyclable_14',
        'recyclable_15', 'recyclable_16', 'recyclable_17', 'recyclable_18', 'recyclable_19', 'recyclable_20',
        'recyclable_21', 'recyclable_22'
    ];

    public function receiveArchive()
    {
        return $this->belongsTo(ReceiveArchive::class);
    }

    public static function new($submit, $archive_id) {
        $archive = ArchiveApp::where('date', Carbon::parse($submit->start_deadline)->format('Y-m-d'))->where('type', 1)->where('city_id', $submit->address->city_id)->first();
        if ($archive) {
            $archive->update([
                'submit_count' => $archive->submit_count + 1,
            ]);
        } else {
            $archive = new ArchiveApp;
            $archive->date = Carbon::parse($submit->start_deadline)->format('Y-m-d');
            $archive->type = 1;
            $archive->city_id = $submit->address->city_id;
            $archive->receive_archive_id = $archive_id;
            $archive->submit_count = 1;
            $archive->save();
        }
    }

    public static function submitDelete($submit) {
        $archive = ArchiveApp::where('date', Carbon::parse($submit->start_deadline)->format('Y-m-d'))->where('type', 1)->where('city_id', $submit->city_id)->first();
        if($archive) {
            $archive->update([
                'submit_delete' => $archive->submit_delete + 1,
            ]);
        }
    }

    public static function submitCancel($submit) {
        $archive = ArchiveApp::where('date', Carbon::parse($submit->start_deadline)->format('Y-m-d'))->where('type', 1)->where('city_id', $submit->city_id)->first();
        if($archive) {
            $archive->update([
                'submit_cancel' => $archive->submit_cancel + 1,
            ]);
        }
    }

    public static function add($pakban) {
        $archive = ArchiveApp::where('date', Carbon::parse($pakban->collected_at)->format('Y-m-d'))->where('type', 1)->where('city_id', $pakban->city_id)->first();

        $value = 0;
        foreach ($pakban->receives as $receive) {
            $value += RecyclableHistory::whereDate('created_at', '<=', $pakban->collected_at)->latest()->pluck($receive->fava_id)->first() * $receive->weight;
        }

        $submit_first = 0;
        $submit_count = Submit::where('user_id', $pakban->submit->user_id)->where('status', 3)->count();
        if ($submit_count == 1) {
            $submit_first = 1;
        }
        if($archive) {
            $archive->update([
                'submit_done' => $archive->submit_done + 1,
                'submit_first' => $archive->submit_first + $submit_first,
                'weight' => $archive->weight + $pakban->weights,
                'user_pay' => $archive->user_pay + $pakban->submit->total_amount * 10,
                'fava_pay_share' => $archive->fava_pay_share + $value * 10 * 0.05,
                'value' => $archive->value + $value * 10,
                'recyclable_1' => $archive->recyclable_1 + $pakban->receives->where('fava_id', 1)->pluck('weight')->sum(),
                'recyclable_2' => $archive->recyclable_2 + $pakban->receives->where('fava_id', 2)->pluck('weight')->sum(),
                'recyclable_3' => $archive->recyclable_3 + $pakban->receives->where('fava_id', 3)->pluck('weight')->sum(),
                'recyclable_4' => $archive->recyclable_4 + $pakban->receives->where('fava_id', 4)->pluck('weight')->sum(),
                'recyclable_5' => $archive->recyclable_5 + $pakban->receives->where('fava_id', 5)->pluck('weight')->sum(),
                'recyclable_6' => $archive->recyclable_6 + $pakban->receives->where('fava_id', 6)->pluck('weight')->sum(),
                'recyclable_7' => $archive->recyclable_7 + $pakban->receives->where('fava_id', 7)->pluck('weight')->sum(),
                'recyclable_8' => $archive->recyclable_8 + $pakban->receives->where('fava_id', 8)->pluck('weight')->sum(),
                'recyclable_9' => $archive->recyclable_9 + $pakban->receives->where('fava_id', 9)->pluck('weight')->sum(),
                'recyclable_10' => $archive->recyclable_10 + $pakban->receives->where('fava_id', 10)->pluck('weight')->sum(),
                'recyclable_11' => $archive->recyclable_11 + $pakban->receives->where('fava_id', 11)->pluck('weight')->sum(),
                'recyclable_12' => $archive->recyclable_12 + $pakban->receives->where('fava_id', 12)->pluck('weight')->sum(),
                'recyclable_13' => $archive->recyclable_13 + $pakban->receives->where('fava_id', 13)->pluck('weight')->sum(),
                'recyclable_14' => $archive->recyclable_14 + $pakban->receives->where('fava_id', 14)->pluck('weight')->sum(),
                'recyclable_15' => $archive->recyclable_15 + $pakban->receives->where('fava_id', 15)->pluck('weight')->sum(),
                'recyclable_16' => $archive->recyclable_16 + $pakban->receives->where('fava_id', 16)->pluck('weight')->sum(),
                'recyclable_17' => $archive->recyclable_17 + $pakban->receives->where('fava_id', 17)->pluck('weight')->sum(),
                'recyclable_18' => $archive->recyclable_18 + $pakban->receives->where('fava_id', 18)->pluck('weight')->sum(),
                'recyclable_19' => $archive->recyclable_19 + $pakban->receives->where('fava_id', 19)->pluck('weight')->sum(),
                'recyclable_20' => $archive->recyclable_20 + $pakban->receives->where('fava_id', 20)->pluck('weight')->sum(),
                'recyclable_21' => $archive->recyclable_21 + $pakban->receives->where('fava_id', 21)->pluck('weight')->sum(),
                'recyclable_22' => $archive->recyclable_22 + $pakban->receives->where('fava_id', 22)->pluck('weight')->sum()
            ]);
        }
    }
}
