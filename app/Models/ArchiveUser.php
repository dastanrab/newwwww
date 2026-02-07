<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchiveUser extends Model
{
    use HasFactory;

    protected $fillable = ['total', 'legal', 'not_legal', 'phone', 'app'];

    public static function newArchive($city_id, $legal, $not_legal, $phone, $app)
    {
        $date = now()->format('Y-m-d');
        $archive = ArchiveUser::where('date', $date)->where('city_id', $city_id)->first();
        if ($archive) {
            $archive->update([
                'total' => $archive->total + 1,
                'legal' => $archive->legal + $legal,
                'not_legal' => $archive->not_legal + $not_legal,
                'phone' => $archive->phone + $phone,
                'app' => $archive->app + $app
            ]);
        } else {
            $archive = new ArchiveUser;
            $archive->date = $date;
            $archive->city_id = $city_id;
            $archive->total = 1;
            $archive->legal = $legal;
            $archive->not_legal = $not_legal;
            $archive->phone = $phone;
            $archive->app = $app;
            $archive->save();
        }
    }
}
