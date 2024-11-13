<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnimationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SustainabilityController;
use Illuminate\Support\Facades\Auth;
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

// Public routes without authentication
Route::middleware(['web'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/donate', [HomeController::class, 'donate'])->name('donate');
    Route::get('/history', [HomeController::class, 'history'])->name('history');
    Route::get('/founders', [HomeController::class, 'founders'])->name('founders');
    Route::get('/team', [HomeController::class, 'team'])->name('team');
    Route::get('/board', [HomeController::class, 'board'])->name('board');
    Route::get('/children-profiles', [HomeController::class, 'children-profiles'])->name('children-profiles');
    Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('/contact', [HomeController::class, 'contact_message'])->name('contact_message');
    Route::get('/sponsor', [HomeController::class, 'sponsorship'])->name('sponsorship');
    Route::get('/sponsorship', [HomeController::class, 'sponsorship'])->name('sponsorship_old');
    Route::get('/child/profile/{id}', [HomeController::class, 'sponsorship_card'])->name('sponsorship_card');
    Route::get('/apply_internship', [HomeController::class, 'apply_intern'])->name('apply_intern');
    Route::post('/apply_internship', [HomeController::class, 'apply_intern_post'])->name('apply_intern_post');
    Route::get('/sustainability/{id}/{title}', [SustainabilityController::class, 'index'])->name('sustainability');
    Route::get('/volunteer', [HomeController::class, 'volunteer'])->name('volunteer');
    Route::get('/tla', [HomeController::class, 'tla'])->name('tla');
    Route::get('/whatsnew', [HomeController::class, 'latest'])->name('latest');
    Route::get('/program', [HomeController::class, 'program'])->name('program');
    Route::get('/gallery/{selectedYear?}', [HomeController::class, 'gallery'])->name('gallery');
    Route::get('/educationfund', [HomeController::class, 'edu_fund'])->name('edu_fund');
    Route::get('/readmore/{documentName}', [HomeController::class, 'readmore'])->name('readmore');
    Route::get('/aboutus_more', [HomeController::class, 'aboutus_home'])->name('aboutus_home');
    Route::get('/scholarship', [HomeController::class, 'scholarship'])->name('scholarship');
    Route::get('/error', [HomeController::class, 'showErrorPage'])->name('error');
    Route::post('/mark-animation-shown', [AnimationController::class, 'markAnimationShown'])->name('mark-animation-shown');

});

// Auth routes
Auth::routes();

// Authenticated routes
Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/newchild', [AdminController::class, 'newchild_index'])->name('newchild.index');
    Route::post('/newchild', [AdminController::class, 'newchild_store'])->name('newchild.store');
    Route::get('/children', [AdminController::class, 'children_list'])->name('children.index');
    Route::get('/children/{id}/edit', [AdminController::class, 'child_edit'])->name('child.edit.index');
    Route::put('children/{id}/update',[AdminController::class, 'child_update'])->name('child_update');
    Route::delete('/children/{id}', [AdminController::class, 'child_delete'])->name('child_delete');

    //system user routes
    Route::get('/users', [AdminController::class, 'user_index'])->name('admin.user.index');
    Route::get('/user/create', [AdminController::class, 'user_create'])->name('admin.user.create');
    Route::post('/user/store', [AdminController::class, 'user_store'])->name('admin.user.store');
    Route::get('/user/{id}/edit', [AdminController::class, 'user_edit'])->name('admin.user.edit');
    Route::patch('/user/update/{id}', [AdminController::class, 'user_update'])->name('admin.user.update');
    Route::delete('/user/{id}/delete', [AdminController::class, 'user_delete'])->name('admin.user.delete');
});
Route::middleware(['auth'])->prefix('admin/system')->group(function () {
    Route::get('/show', [AdminController::class, 'showSystemDetails'])->name('admin.system_details.index');
    Route::get('/add', [AdminController::class, 'addSystemDetails'])->name('admin.system_details.add');
    Route::post('/store', [AdminController::class, 'storeSystemDetails'])->name('admin.system_details.store');
    Route::get('/edit/{id}', [AdminController::class, 'editSystemDetails'])->name('admin.system_details.edit');
    Route::delete('/destroy/{id}', [AdminController::class, 'deleteSystemDetails'])->name('admin.system_details.destroy');
    Route::put('/update/{id}', [AdminController::class, 'updateSystemDetails'])->name('admin.system_details.update');

});
