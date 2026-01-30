<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\SmsController;
use App\Livewire\Pages\Home\Home;
use App\Livewire\Pages\Reservations\Reservations;
use App\Livewire\Pages\Tablelists\Tablelists;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public routes (no authentication required)
Route::get('/info', [InfoController::class, 'index'])->name('info.index');

// روت‌های عمومی
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Rate limiting برای login - حداکثر 3 تلاش در 1 دقیقه
    Route::post('/login', [AuthController::class, 'login'])->middleware('custom.throttle:3,1');
});


// روت‌های محافظت شده
Route::middleware(['auth.custom'])->group(function () {
    Route::get("/", action: Home::class)->name('home');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get("/lists", Tablelists::class)->name('table_list');
    Route::get("/BedStatistic", \App\Livewire\Pages\BedStatistics\BedStatistics::class)->name('Bed_statistic');
    Route::get("/Reservations", Reservations::class)->name('reservations');
    Route::get("/report/list-current-resident", \App\Livewire\Pages\Reports\ListCurrentResident::class)->name('report.list_current_resident');
    Route::get("/report/exited-residents", \App\Livewire\Pages\Reports\ExitedResidents::class)->name('report.exited_residents');
    Route::get('/coolers', \App\Livewire\Pages\Coolers\CoolerRoomManager::class)->name('coolers');
    Route::get('/keys', \App\Livewire\Pages\Keys\KeyRoomTable::class)->name('keys');
    Route::get('/heaters', \App\Livewire\Pages\Heaters\HeaterRoomManager::class)->name('heaters');
    Route::get("/report/chart-one", \App\Livewire\Pages\Reports\ChartOne::class)->name('report.chart_one');
    Route::get('/dormitory-builder', \App\Livewire\Pages\Dormitory\DormitoryBuilder::class)->name('dormitory.builder');
    Route::get('/message-system', \App\Livewire\MessageSystem::class)->name('message.system');
    Route::get('/message-sender', \App\Livewire\MessageSender::class)->name('message.sender');
    Route::get('/rules', \App\Livewire\Rules\SimpleRules::class)->name('rules.manager');
    Route::get('/categories', \App\Livewire\Rules\CategoryManager::class)->name('categories.manager');
    Route::get('/test', \App\Livewire\Rules\TestPage::class)->name('test.page');
    Route::get('/simple-categories', \App\Livewire\Rules\SimpleCategories::class)->name('simple.categories');
    Route::get('/category-management', \App\Livewire\Rules\CategoryManagement::class)->name('category.management');
    Route::get('/resident-contacts', \App\Livewire\Pages\ResidentContacts\ResidentContacts::class)->name('resident.contacts');
    
    // SMS Credit Routes
    Route::get('/api/sms/credit', [SmsController::class, 'getCredit'])->name('sms.credit');
    Route::post('/api/sms/refresh', [SmsController::class, 'refreshCredit'])->name('sms.refresh');
});
