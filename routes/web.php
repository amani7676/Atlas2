<?php

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
Route::get("/", Home::class)->name('home');
Route::get("/lists", Tablelists::class)->name('table_list');
Route::get("/BedStatistic", \App\Livewire\Pages\BedStatistics\BedStatistics::class)->name('Bed_statistic');
Route::get("/Reservations", Reservations::class)->name('reservations');
Route::get("/report/list-current-resident", \App\Livewire\Pages\Reports\ListCurrentResident::class)->name('report.list_current_resident');
Route::get('/coolers', \App\Livewire\Pages\Coolers\CoolerRoomManager::class)->name('coolers');
Route::get('/keys', \App\Livewire\Pages\Keys\KeyRoomTable::class)->name('keys');
Route::get("/report/chart-one", \App\Livewire\Pages\Reports\ChartOne::class)->name('report.chart_one');
