<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;
use NotificationChannels\WebPush\PushSubscription;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable , SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'legal', 'fava_id', 'guild_id', 'guild_title', 'name', 'lastname', 'gender', 'birthday', 'referral_code', 'mobile', 'card_number', 'shaba_number', 'cardholder', 'level', 'email', 'password', 'national_code', 'city_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected static $recordableEvents = ['updated', 'deleted'];

    public static function refCode()
    {
        return Cache::get('refCode');
    }

    public function referral()
    {
        return $this->id+self::refCode();
    }

    public static function getUserIdByReferral($referralCode)
    {
        return $referralCode-self::refCode();
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function inaxes()
    {
        return $this->hasMany(Inax::class);
    }

    public function roles() {
        return $this->belongsToMany(Role::class);
    }

    public function messages() {
        return $this->hasMany(Message::class);
    }

    public function scores()
    {
        return $this->hasMany(ScoreHistory::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function clubs()
    {
        return $this->hasMany(Club::class);
    }

    public function failedRollcall()
    {

        return $this->hasMany(FailedRollcall::class);
    }

    public function getRoles($index = null) {
        $roles = $this->roles()->get()->pluck('name');
        return $index === null ? $roles : $roles[$index];
    }

    public function getRoleName()
    {
        return $this->roles()->get()->pluck('label')->first();
    }

    public function getRole($key)
    {
        return $this->roles()->get()->pluck($key)->first();
    }

    public function getLegalType()
    {
        return $this->legal ? 'صنفی' : 'شهروند';
    }

    public function getLegalName()
    {
        return $this->legal ? 'guild' : 'citizen';
    }

    public static function genders(){
        return [
            (object)[
                'label' => 'آقا',
                'name' => 1,
            ],
            (object)[
                'label' => 'خانم',
                'name' => 2,
            ]
        ];
    }

    public static function levels()
    {
        return [1,2];
    }

    public function getPermissions() {
        return $this->getRoles(0) == 'superadmin'
            ? Permission::all()->pluck('name')
            : $this->roles()->first()->permissions()->get()->pluck('name');
    }

    public function getPermissionsId() {
        return $this->getRoles(0) == 'superadmin'
            ? Permission::all()->pluck('id')
            : $this->roles()->first()->permissions()->get()->pluck('id');
    }

    public function submits()
    {
        return $this->hasMany(Submit::class);
    }

    public function drivers()
    {
        return $this->hasMany(Driver::class);
    }

    public function driverWallet()
    {
        return $this->hasOne(DriverWallet::class);
    }

    public function wallets()
    {
        return $this->hasMany(Wallet::class);
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    public function referrers()
    {
        return $this->hasMany(Referrer::class);
    }

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    public function cars()
    {
        return $this->hasMany(Car::class);
    }

    public function car()
    {
        return $this->hasOne(Car::class);
    }

    public function polygonDrivers()
    {
        return $this->hasMany(PolygonDriver::class);
    }

    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class, 'subscribable_id');
    }

    public function guilds()
    {
        return $this->hasOne(Guild::class, 'fava_id');
    }

    public function guild()
    {
        return $this->belongsTo(Guild::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function rollcalls()
    {
        return $this->hasMany(Rollcall::class);
    }

    public function userComments()
    {
        return $this->hasMany(UserComment::class);
    }

    public function warehouseDailies()
    {
        return $this->hasMany(WarehouseDaily::class);
    }

    public function ibans()
    {
        return $this->hasMany(Iban::class);
    }

    public function firebases()
    {
        return $this->hasMany(Firebase::class);
    }

    public static function register($data = [])
    {
        $referralCode = null;
        $userRef = null;
        if ($data['referral']) {
            $userRef = User::find(User::getUserIdByReferral($data['referral']));
            $referralCode = $userRef->referral();
        }

        $user = User::create([
            'legal' => $data['userType'],
            'guild_id' => $data['guildId'] ?? null,
            'guild_title' => $data['guildId'] ? $data['guildTitle'] : null,
            'name' => $data['name'],
            'lastname' => $data['lastname'],
            'gender' => $data['gender'],
            'city_id' => $data['cityId'],
            'referral_code' => $referralCode,
            'mobile' => $data['mobile'],
            'password' => in_array($data['roleId'],Role::passwordRequiredIds()) ? Hash::make($data['password']) : Hash::make(strRandom())
        ]);
        if($user){
            $user->roles()->sync([$data['roleId']]);
            $user->wallets()->create(['wallet' => 0]);

            if ($referralCode) {
                $userRef->referrers()->create([
                    'user_id' => $userRef->id,
                    'referrer_id' => $user->id
                ]);
            }
            ArchiveUser::newArchive($data['cityId'], $data['userType'], $data['userType'] == 1 ? 0 : 1, 0, 1);

            $user->fava_id = Fava::createUser([
                'userId' => $user->id,
                'guildId' => $data['guildId'],
                'isLegal' => $data['userType'],
                'name' => $data['name'],
                'lastname' => $data['lastname'],
                'guildTitle' => $data['guildTitle'],
                'cityId' => $data['cityId'],
                'mobile' => $data['mobile'],
            ]);
            $user->save();
            return $user;
        }
        return false;

    }

    public function profileUpdate($data = [])
    {
        if($data['roleId'] && $this->getRole('id') !== $data['roleId']){
            $this->roles()->sync([$data['roleId']]);
        }
        if($data['password'] !== null) {
            $this->password = $data['password'];
        }
        $this->gender = $data['gender'];
        $this->level = $data['level'];
        $this->name = $data['name'];
        $this->lastname = $data['lastname'];
        $this->legal = $data['userType'];
        $this->guild_id = $data['userType'] ? $data['guildId'] : null;
        $this->guild_title = $data['userType'] ? $data['guildTitle'] : null;
        if(!empty($data['referral']) && $this->referral_code != $data['referral']) {
            $userRef = User::find(User::getUserIdByReferral($data['referral']));
            $referralCode = $userRef->referral();
            $userRef->referrers()->create([
                'user_id' => $userRef->id,
                'referrer_id' => $this->id
            ]);
            $this->referral_code = $referralCode;
        }
        return $this->save();
    }

    public function rollCallCurrentMonth()
    {

        $start_month = verta()->startMonth();
        $start_month = Carbon::parse($start_month->datetime());
        $rollCallsMonth = $this->rollcalls()->where('end_at', '!=', null)->whereDate('start_at', '>=', $start_month)->get();
        $sumRollCall = 0;
        foreach ($rollCallsMonth as $rollCallMonth){
            $sumRollCall += Carbon::parse($rollCallMonth->start_at)->diffInMinutes($rollCallMonth->end_at);
        }
        $hours = floor($sumRollCall / 60);
        $mins = $sumRollCall % 60;
        return (object)['hour' => $hours,'min' => $mins];

    }

    public function isFirstSubmit()
    {
        return Submit::where('user_id', $this->id)->where('status', 3)->count() ? false : true;
    }

    public static function cityId()
    {
        return $_COOKIE['city_id'];
    }

    public static function azadiId()
    {
        return [
            56778,// انبار آزادی
            66668, // کربلایی
            60868, // کلالی
        ];
    }

    public static function mayameyId()
    {
        return [
            56012 // سجاد خانی
        ];
    }

    public static function warehouserId()
    {
        return array_merge(Self::mayameyId(),Self::azadiId());
    }


    public function isRegistered()
    {
        return $this->name && $this->lastname;
    }

    public function currentSubmit()
    {
        $currentRequest = null;
        $submit = $this->submits()->whereIn('status',[1,2])->first();
        if($submit) {
            $startDeadline = verta()->instance($submit->start_deadline);
            $endDeadline = verta()->instance($submit->end_deadline);
            $driver = $submit->drivers();
            $currentRequest = [
                'id' => $submit->id,
                'requestDate' => [
                    'day' => $startDeadline->format('d F'),
                    'range' => $startDeadline->format('G:i').' الی '.$endDeadline->format('G:i'),
                ],
                'address' => $submit->address->address,
                'collectDate' => null,
                'status' => $submit->status(),
                'driver' => $driver->exists() ? [
                    'name' => $driver->first()->car->user->name.' '.$driver->first()->car->user->lastname,
                    'plaque' => in_array($submit->status,[1,2]) ? [
                        'part1' => $driver->first()->car->plaque_1,
                        'part2' => $driver->first()->car->plaque_2,
                        'part3' => $driver->first()->car->plaque_3,
                        'part4' => $driver->first()->car->plaque_4,
                    ] : null,
                    'mob' => in_array($submit->status,[1,2]) ? $driver->first()->car->user->mobile : null,
                    'avatar' => asset('assets/img/avatar/avatar-driver-3.png'),
                ] : null,
                'cancelable' => $submit->status == 1 ? true : false,
            ];
        }
        return $currentRequest;
    }

    public function rollcallEnabled()
    {
        $limiter = $this->id == 2 ? 100 : 2;
        return $this->rollcalls()->whereDate('start_at',now())->where('end_at','!=',null)->count() < $limiter;
    }

    public function driverIsPresence()
    {
        return $this->rollcalls()->whereDate('start_at',now())->where('end_at','=',null)->first();
    }

    public function rollCallData()
    {
        return [
            'status' => $this->driverIsPresence() ? 'present' : 'absent',
            'enabled' => [
                'value' => $this->rollcallEnabled(),
                'warning' => 'در روز فقط ۲بار میتوانید حضور ثبت کنید'
            ]
        ];
    }

    public function isDeveloper()
    {
        //return $this->id == developerId();
        return $this->getRoles(0) == 'superadmin';
    }
}
