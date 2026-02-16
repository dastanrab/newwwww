<?php

namespace App\Providers;


use App\Livewire\Dashboard\Home\HomeIndex;
use App\Livewire\Dashboard\Settings\AreaIndex;
use App\Livewire\Dashboard\Settings\GeneralIndex;
use App\Livewire\Dashboard\Settings\InstantIndex;
use App\Livewire\Dashboard\Settings\MapIndex;
use App\Livewire\Dashboard\Settings\RecyclableIndex;
use App\Livewire\Dashboard\Settings\RolesIndex;
use App\Livewire\Dashboard\Settings\SubmitTimeIndex;
use App\Livewire\Dashboard\Stats\StatDriverPerformanceIndex;
use App\Livewire\Dashboard\Stats\StatSubmitIndex;
use App\Livewire\Dashboard\Track\TrackIndex;
use App\Livewire\Dashboard\Track\TrackReportIndex;
use App\Livewire\Dashboard\Wallet\WalletIndex;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Submit;
use App\Models\User;
use App\Policies\ActivityPolicy;
use App\Policies\ContactPolicy;
use App\Policies\DriverPolicy;
use App\Policies\HomePolicy;
use App\Policies\MessagePolicy;
use App\Policies\SettingPolicy;
use App\Policies\StatPolicy;
use App\Policies\SubmitPolicy;
use App\Policies\TrackPolicy;
use App\Policies\UserPolicy;
use App\Policies\WalletPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Activity::class => ActivityPolicy::class,
        HomeIndex::class => HomePolicy::class,
        TrackIndex::class => TrackPolicy::class,
        TrackReportIndex::class => TrackPolicy::class,
        Message::class => MessagePolicy::class,
        Contact::class => ContactPolicy::class,
        User::class => UserPolicy::class,
        WalletIndex::class => WalletPolicy::class,
        Submit::class => SubmitPolicy::class,
        StatSubmitIndex::class => StatPolicy::class,
        SubmitTimeIndex::class => SettingPolicy::class,
        RecyclableIndex::class => SettingPolicy::class,
        AreaIndex::class => SettingPolicy::class,
        RolesIndex::class => SettingPolicy::class,
        InstantIndex::class=>SettingPolicy::class,
        GeneralIndex::class => SettingPolicy::class,
        MapIndex::class => SettingPolicy::class,
        StatDriverPerformanceIndex::class=>StatPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
