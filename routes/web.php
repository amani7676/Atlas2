<?php

use App\Http\Controllers\AuthController;
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


// روت‌های عمومی
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    // Rate limiting برای login - حداکثر 5 تلاش در 15 دقیقه
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,15');
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
});
