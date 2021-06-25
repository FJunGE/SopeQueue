<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TestKafkaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('kafka-produce', [TestKafkaController::class, 'produce'])->name('kafka.produce');

Route::get('animal', [HomeController::class, 'animal'])->name('animal');
Route::get('userlist', [HomeController::class, 'userList'])->name('userList');
Route::get('pipe', [HomeController::class, 'pipelineUpdateUser'])->name('pipe');
Route::get('ioc', [HomeController::class, 'ioc'])->name('ioc');
