<?php

use App\Classes\BaleService;
use App\Classes\CaluculatedriverSalary;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankSamanController;
use App\Http\Controllers\Dashboard\FcmController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FavaController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PageController;
use App\Livewire\Dashboard\Activity\ActivityIndex;
use App\Livewire\Dashboard\Auth\Login;
use App\Livewire\Dashboard\Club\ClubCategoriesIndex;
use App\Livewire\Dashboard\Club\ClubCategoryCreate;
use App\Livewire\Dashboard\Club\ClubCategoryEdit;
use App\Livewire\Dashboard\Club\ClubCloseOffersIndex;
use App\Livewire\Dashboard\Club\ClubItemCreate;
use App\Livewire\Dashboard\Club\ClubItemEdit;
use App\Livewire\Dashboard\Club\ClubItemsIndex;
use App\Livewire\Dashboard\Club\ClubOffersCreate;
use App\Livewire\Dashboard\Club\ClubOffersIndex;
use App\Livewire\Dashboard\Club\ScoreHistoriesIndex;
use App\Livewire\Dashboard\Drivers\DriverCreate;
use App\Livewire\Dashboard\Drivers\DriverFeiledRollcallMap;
use App\Livewire\Dashboard\Drivers\DriverIndex;
use App\Livewire\Dashboard\Drivers\DriverRollcall;
use App\Livewire\Dashboard\Drivers\DriverSingle;
use App\Livewire\Dashboard\Home\HomeIndex;
use App\Livewire\Dashboard\Messages\ContactIndex;
use App\Livewire\Dashboard\Messages\ContactSingle;
use App\Livewire\Dashboard\Messages\NotificationCreate;
use App\Livewire\Dashboard\Messages\NotificationIndex;
use App\Livewire\Dashboard\Messages\SubmitSurveyIndex;
use App\Livewire\Dashboard\Settings\AreaIndex;
use App\Livewire\Dashboard\Settings\GeneralIndex;
use App\Livewire\Dashboard\Settings\InstantIndex;
use App\Livewire\Dashboard\Settings\MapIndex;
use App\Livewire\Dashboard\Settings\MapSortRegionIndex;
use App\Livewire\Dashboard\Settings\RolesIndex;
use App\Livewire\Dashboard\Settings\RecyclableIndex;
use App\Livewire\Dashboard\Settings\RolesSingle;
use App\Livewire\Dashboard\Settings\SubmitTimeIndex;
use App\Livewire\Dashboard\Stats\StatAreaIndex;
use App\Livewire\Dashboard\Stats\StatDailyIndex;
use App\Livewire\Dashboard\Stats\StatDistanceIndex;
use App\Livewire\Dashboard\Stats\StatHighAverageIndex;
use App\Livewire\Dashboard\Stats\StatMonthlyIndex;
use App\Livewire\Dashboard\Stats\StatSubmitIndex;
use App\Livewire\Dashboard\Stats\StatTotalCostIndex;
use App\Livewire\Dashboard\Stats\StatTotalCostIndex2;
use App\Livewire\Dashboard\Stats\StatTotalIndex;
use App\Livewire\Dashboard\Stats\StatTotalUserIndex;
use App\Livewire\Dashboard\Stats\StatUserIndex;
use App\Livewire\Dashboard\Stats\StatWarehouseCreate;
use App\Livewire\Dashboard\Stats\StatWarehouseDailyIndex;
use App\Livewire\Dashboard\Stats\StatWarehouseDriverDetailIndex;
use App\Livewire\Dashboard\Stats\StatWarehouseDriverIndex;
use App\Livewire\Dashboard\Stats\StatWarehouseDriverIndexListSubmit;
use App\Livewire\Dashboard\Stats\StatWarehouseIndex;
use App\Livewire\Dashboard\Stats\StatWasteIndex;
use App\Livewire\Dashboard\Submits\SubmitAddressEdit;
use App\Livewire\Dashboard\Submits\SubmitAllIndex;
use App\Livewire\Dashboard\Submits\SubmitMapIndex;
use App\Livewire\Dashboard\Submits\SubmitTelIndex;
use App\Livewire\Dashboard\Supervisor\SupervisorDriverIndex;
use App\Livewire\Dashboard\Supervisor\SupervisorDriverSingle;
use App\Livewire\Dashboard\Track\TrackIndex;
use App\Livewire\Dashboard\Track\TrackReportIndex;
use App\Livewire\Dashboard\Users\create;
use App\Livewire\Dashboard\Users\UserCreate;
use App\Livewire\Dashboard\Users\UserIndex;
use App\Livewire\Dashboard\Users\UserSingle;
use App\Livewire\Dashboard\Wallet\AsanpardakhtIndex;
use App\Livewire\Dashboard\Wallet\CashoutAll;
use App\Livewire\Dashboard\Wallet\CashoutIndex;
use App\Livewire\Dashboard\Wallet\CashoutSingle;
use App\Livewire\Dashboard\Wallet\WalletDriverIndex;
use App\Livewire\Dashboard\Wallet\WalletIndex;
use App\Models\BankSaman;
use App\Models\Cashout;
use App\Models\Driver;
use App\Models\DriverSuggestedRequests;
use App\Models\Fava;
use App\Models\Iban;
use App\Models\Inax;
use App\Models\Recyclable;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/sos',function (){


    dd(request()->server,request()->headers,request()->all(),request()->ip());
});
Route::get('pa/login', Login::class)->name('d.login');
Route::middleware([\App\Http\Middleware\IsAdmin::class])->group(function () {
    Route::get('/docs', function () {
        return view('scribe.index');
    })->name('docs');
});
Route::group(['prefix' => 'pa', 'as' => 'd.', 'middleware' => [\App\Http\Middleware\IsAdmin::class]],function () {

//    Route::post('/fcm', [FcmController::class,'store'])->name('fcm');
    //dashboard
    Route::get('/', function (){return redirect(route('d.home'));});
    Route::get('/home', HomeIndex::class)->name('home');
    //submits
    Route::get('/submits/all', SubmitAllIndex::class)->name('submits.all');
    Route::get('/submits/tel', SubmitTelIndex::class)->name('submits.tel');
    Route::get('/submits/map', SubmitMapIndex::class)->name('submits.map');
    //wallet
    Route::get('/wallet/all', WalletIndex::class)->name('wallet');
    Route::get('/wallet/cashout', CashoutIndex::class)->name('wallet.cashout');
    Route::get('/wallet/cashout/{cashout}', CashoutSingle::class)->name('wallet.cashout.single');
    Route::get('/wallet/ap', AsanpardakhtIndex::class)->name('wallet.ap');
    Route::get('/wallet/drivers', WalletDriverIndex::class)->name('wallet.drivers');
    Route::get('/wallet/manual_transaction', \App\Livewire\Dashboard\Wallet\WalletManualTransactionIndex::class)->name('wallet.manual_transaction');
    //stats
    Route::get('/charts', \App\Livewire\Dashboard\Home\ChartsManagerIndex::class)->name('chart');
    Route::get('/stats/submit', StatSubmitIndex::class)->name('stats.submit');
    Route::get('/stats/total', StatTotalIndex::class)->name('stats.total');
    Route::get('/stats/monthly', StatMonthlyIndex::class)->name('stats.monthly');
    Route::get('/stats/daily', StatDailyIndex::class)->name('stats.daily');
    Route::get('/stats/area', StatAreaIndex::class)->name('stats.area');
    Route::get('/stats/salary-driver', \App\Livewire\Dashboard\Stats\StatSalaryDriverIndex::class)->name('stats.salary-driver');
    Route::get('/stats/salary-driver/{driver}',\App\Livewire\Dashboard\Stats\StatSalaryDriverDetail::class)->name('stats.salary-driver-detail');
    Route::get('/stats/warehouse-driver', StatWarehouseDriverIndex::class)->name('stats.warehouse-driver');
    Route::get('/stats/attendance-driver', \App\Livewire\Dashboard\Stats\StatAttendanceDriverIndex::class)->name('stats.attendance-driver');
    Route::get('/stats/warehouse-driver/detail', StatWarehouseDriverDetailIndex::class)->name('stats.warehouse-driver-detail');
    Route::get('/stats/warehouse-driver/{user}', StatWarehouseDriverIndexListSubmit::class)->name('stats.warehouse-driver.submit');
    Route::get('/stats/waste', StatWasteIndex::class)->name('stats.waste');
    Route::get('/stats/total-cost', StatTotalCostIndex::class)->name('stats.total-cost');
    Route::get('/stats/total-cost2', StatTotalCostIndex2::class)->name('stats.total-cost2');
    Route::get('/stats/total-user', StatTotalUserIndex::class)->name('stats.total.user');
    Route::get('/stats/user', StatUserIndex::class)->name('stats.user');
    Route::get('/stats/warehouse-daily', StatWarehouseDailyIndex::class)->name('stats.warehouse-daily');
    Route::get('/stats/warehouse', StatWarehouseIndex::class)->name('stats.warehouse');
    Route::get('/stats/warehouse/create', StatWarehouseCreate::class)->name('stats.warehouse.create');
    Route::get('/stats/distances', StatDistanceIndex::class)->name('stats.distances');
    Route::get('/stats/other/total', \App\Livewire\Dashboard\Stats\StatOtherTotalIndex::class)->name('stats.other-total');
    Route::get('/stats/other/high-average', StatHighAverageIndex::class)->name('stats.high-average');
    Route::get('/stats/other/top-users', \App\Livewire\Dashboard\Stats\StatTopUsersIndex::class)->name('stats.top-users');
    Route::get('/stats/other/driver-performance', \App\Livewire\Dashboard\Stats\StatDriverPerformanceIndex::class)->name('stats.driver-performance');
    Route::get('/stats/other/pend-inax', \App\Livewire\Dashboard\Stats\StatPendInaxIndex::class)->name('stats.pend-inax');

    //address
    Route::get('/submits/address/{address}/edit', SubmitAddressEdit::class)->name('address.edit');
    //track
    Route::get('/track/online', TrackIndex::class)->name('track');
    Route::get('/track/report', TrackReportIndex::class)->name('track.report');
    //users
    Route::get('/users/all', UserIndex::class)->name('users');
    Route::get('/users/drivers', DriverIndex::class)->name('drivers');
    Route::get('/users/drivers/create', DriverCreate::class)->name('drivers.create');
    Route::get('/users/drivers/{driver}', DriverSingle::class)->name('drivers.single');
    Route::get('/users/drivers/{driver}/rollcall', DriverRollcall::class)->name('drivers.rollcall');
    Route::get('/users/drivers/failedRollcall/{failedRollcall}', DriverFeiledRollcallMap::class)->name('drivers.failedRollcall');
    Route::get('/users/create', UserCreate::class)->name('users.create');
    Route::get('/users/{user}', UserSingle::class)->name('users.single');
    //clubs
    Route::get('/club/items', ClubItemsIndex::class)->name('club.items');
    Route::get('/club/item/create', ClubItemCreate::class)->name('club.create');
    Route::get('/club/item/{club}', ClubItemEdit::class)->name('club.edit');
    Route::get('/club/categories', ClubCategoriesIndex::class)->name('club.categories');
    Route::get('/club/category/create', ClubCategoryCreate::class)->name('club.category.create');
    Route::get('/club/category/{clubCategory}', ClubCategoryEdit::class)->name('club.category.edit');
    Route::get('/club/open_offers', ClubOffersIndex::class)->name('club.offers-open');
    Route::get('/club/close_offers', ClubCloseOffersIndex::class)->name('club.offers-close');
    Route::get('/club/offers/create', ClubOffersCreate::class)->name('club.offers.create');
    Route::get('/club/score_histories',ScoreHistoriesIndex::class)->name('club.score-histories');


    //messages
    Route::get('/messages/notifications', NotificationIndex::class)->name('notifications');
    Route::get('/messages/submit-surveys', SubmitSurveyIndex::class)->name('submit-surveys');
    Route::get('/messages/notifications/create', NotificationCreate::class)->name('notifications.create');
    Route::get('/messages/contacts', ContactIndex::class)->name('contacts');
    Route::get('/messages/contacts/{contact}', ContactSingle::class)->name('contacts.single');
    //settings
    Route::get('/settings/area', AreaIndex::class)->name('settings.area');
    Route::get('/settings/instants', InstantIndex::class)->name('settings.instants');
    Route::get('/settings/submit-time', SubmitTimeIndex::class)->name('settings.submit-time');
    Route::get('/settings/recyclable', RecyclableIndex::class)->name('settings.recyclable');
    Route::get('/settings/roles', RolesIndex::class)->name('settings.roles');
    Route::get('/settings/roles/{role}', RolesSingle::class)->name('settings.roles.single');
    Route::get('/settings/map', MapIndex::class)->name('settings.map');
    Route::get('/settings/map/sort', MapSortRegionIndex::class)->name('settings.region');
    Route::get('/settings/general', GeneralIndex::class)->name('settings.general');
    //logs
    Route::get('/logs', ActivityIndex::class)->name('logs');

    //supervisor diver
    Route::get('/supervisor-drivers', SupervisorDriverIndex::class)->name('supervisor.drivers');
    Route::get('/supervisor-drivers/{driver}', SupervisorDriverSingle::class)->name('supervisor.drivers.single');
    //logout
    Route::get('/logout', [AuthController::class,'logout'])->name('logout');

    //export
    Route::get('/export/stats/daily', [ExportController::class,'statDaily'])->name('export.statDaily');
    Route::get('/export/stats/daily-division', [ExportController::class,'statDailyDivision'])->name('export.statDailyDivision');
    Route::get('/export/driver/rollCall', [ExportController::class,'driverRollCall'])->name('export.driver.rollCall');
    Route::get('/export/driver/salary', [ExportController::class,'driverSalary'])->name('export.driver.salary');
    Route::get('/export/stat/waste', [ExportController::class,'statWastes'])->name('export.stat.waste');
    Route::get('/export/stat/warehouse_driver', [ExportController::class,'warehouseDriver'])->name('export.stat.warehouse_driver');
    Route::get('/export/stat/warehouse_driver-detail', [ExportController::class,'warehouseDriverDetail'])->name('export.stat.warehouse_driver_detail');
    Route::get('/export/stat/montly_fin',[ExportController::class,'statMonthly'])->name('export.stat.monthly_fin');
    Route::get('/export/stat/manual_transaction',[ExportController::class,'statManualTransactions'])->name('export.stat.manual_transaction');
    Route::get('/export/stat/fin_archive',[ExportController::class,'statFinArchive'])->name('export.stat.fin_archive');
    Route::get('/export/stat/fin_archive2',[ExportController::class,'statFinArchive2'])->name('export.stat.fin_archive2');
    Route::get('/export/stat/driver_waste',[ExportController::class,'DriversWastesWeight'])->name('export.stat.driver_waste');
    Route::get('/export/stat/cashout', [ExportController::class,'statCashout'])->name('export.stat.cashout');
    Route::get('/export/stat/charity', [ExportController::class,'statCharity'])->name('export.stat.charity');
    Route::get('/export/stat/fava_miss', [ExportController::class,'statFavaMiss'])->name('export.stat.favamiss');
    Route::get('/export/stat/reward', [ExportController::class,'statReward'])->name('export.stat.reward');
    Route::get('/export/stat/hotels', [ExportController::class,'statHotels'])->name('export.stat.hotels');
    Route::get('/export/stat/schools', [ExportController::class,'statSchools'])->name('export.stat.schools');
    Route::get('/export/stat/fava_sum', [ExportController::class,'statFavaSum'])->name('export.stat.favasum');
    Route::get('/export/stat/admin_deposit', [ExportController::class,'statAdminDeposit'])->name('export.stat.admindepo');
    Route::get('/export/stat/inax', [ExportController::class,'statInax'])->name('export.stat.inax');
    Route::get('/export/stat/saman_untrace', [ExportController::class,'statSamanUnTrace'])->name('export.stat.saman_untrace');
    Route::get('/export/stat/daily_submits', [ExportController::class,'statDailySubmits'])->name('export.stat.daily_submits');

    //    Route::get('/export/stat/fava_submits', [ExportController::class,'statFavaSubmits'])->name('export.stat.favasubmits');

    //driver
    Route::get('/driver/map/{driver}', \App\Livewire\Dashboard\Drivers\Mapsuggestedpage::class)->name('driver.map');

});
