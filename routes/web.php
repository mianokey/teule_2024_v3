<?php

use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnimationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MerchandiseController;
use App\Http\Controllers\PettycashController;
use App\Http\Controllers\SustainabilityController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DonorController;
use App\Http\Controllers\Admin\DonationController;
use App\Http\Controllers\Admin\DonationCommunicationController;
use App\Http\Controllers\Admin\MpesaTransactionController;
use App\Http\Controllers\Admin\StoreItemCategoryController;
use App\Http\Controllers\Admin\StoreUnitController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\StoreItemController;
use App\Http\Controllers\Admin\StoreItemVariantController;
use App\Http\Controllers\Admin\StoreReceiptController;
use App\Http\Controllers\Admin\StoreReceiptItemController;
use App\Http\Controllers\Admin\StoreStockController;
use App\Http\Controllers\Admin\StoreRequisitionController;
use App\Http\Controllers\Admin\StoreFulfillmentController;
use App\Http\Controllers\Admin\StoreLpoController;
use App\Http\Controllers\Admin\SupplierController;

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
    Route::get('/blog', [HomeController::class, 'blog'])->name('blog');
    Route::get('/blog/show', [HomeController::class, 'blogshow'])->name('blogshow');
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
    Route::get('/careers', [HomeController::class, 'careers'])->name('careers');
    Route::get('/needboard', [HomeController::class, 'history'])->name('history');


    // Merchandise routes
     Route::get('/merch', [MerchandiseController::class,'index'])->name('merch.index');
    Route::get('/merch/{product}', [MerchandiseController::class,'show'])->name('merch.show');
    Route::post('/merch/checkout', [MerchandiseController::class,'checkout'])->name('merch.checkout');
    Route::post('/merch/place-order', [MerchandiseController::class,'placeOrder'])->name('merch.placeOrder');
    Route::get('/merch/thankyou/{order}', [MerchandiseController::class,'thankYou'])->name('merch.thankyou');

    // M-PESA routes
    Route::post('/merch/mpesa/pay/{order}', [MerchandiseController::class,'mpesaPay'])->name('merch.mpesaPay');
    Route::post('/merch/mpesa/callback', [MerchandiseController::class,'mpesaCallback'])->name('merch.mpesaCallback');
});

Route::get('/careers/{slug}', function ($slug) {
    $jobs = include resource_path('data/jobs.php');

    if (!isset($jobs[$slug])) {
        abort(404);
    }

    $job = $jobs[$slug];
    return view('careers.job-detail', compact('job', 'slug'));
})->name('careers.show');



// Auth routes
Auth::routes();

// Authenticated routes
Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/newchild', [AdminController::class, 'newchild_index'])->name('newchild.index');
    Route::post('/newchild', [AdminController::class, 'newchild_store'])->name('newchild.store');
    Route::get('/children', [AdminController::class, 'children_list'])->name('children.index');
    Route::get('/children/sponscard', [AdminController::class, 'spons_card'])->name('children.sponscard');
    Route::get('/children/{id}/edit', [AdminController::class, 'child_edit'])->name('child.edit.index');
    Route::put('children/{id}/update',[AdminController::class, 'child_update'])->name('child_update');
    Route::delete('/children/{id}', [AdminController::class, 'child_delete'])->name('child_delete');
    Route::post('/children/card/bio', [AdminController::class, 'saveBio']);
    Route::get('/children/cards/select', [AdminController::class, 'child_card_select']);


    

    //system user routes
    Route::get('/users', [AdminController::class, 'user_index'])->name('admin.user.index');
    Route::get('/user/create', [AdminController::class, 'user_create'])->name('admin.user.create');
    Route::post('/user/store', [AdminController::class, 'user_store'])->name('admin.user.store');
    Route::get('/user/{id}/edit', [AdminController::class, 'user_edit'])->name('admin.user.edit');
    Route::patch('/user/update/{id}', [AdminController::class, 'user_update'])->name('admin.user.update');
    Route::delete('/user/{id}/delete', [AdminController::class, 'user_delete'])->name('admin.user.delete');
    Route::put('user/{userId}/assign-role', [AdminController::class, 'updateRoles'])->name('admin.user.updateRoles');


    //donation routes
    Route::resource('donors', DonorController::class) ->names('admin.donors'); 
    Route::resource('donations', DonationController::class) ->names('admin.donations');
    Route::get('donation-communications', [ DonationCommunicationController::class, 'index'])->name('admin.donation-communications.index');
    Route::get('donation-communications/{communication}', [ DonationCommunicationController::class,'show'])->name('admin.donation-communications.show');
    Route::post('donation-communications/{communication}/cancel', [ DonationCommunicationController::class,'cancel'])->name('admin.donation-communications.cancel');
    Route::post('donations/{donation}/resend-thank-you', [ DonationController::class, 'resendThankYou',])->name('admin.donations.resend-thank-you');
    Route::get('/donations/{donation}/receipt',[DonationController::class, 'receipt'])->name('admin.donations.receipt');
// Stores - Item Categories

Route::resource('store-categories', StoreItemCategoryController::class)->parameters(['store-categories' => 'storeCategory'])->names('admin.store-categories');

Route::patch('store-categories/{storeCategory}/toggle-status', [StoreItemCategoryController::class, 'toggleStatus'])->name('admin.store-categories.toggle-status');


// Stores - Units

Route::resource('store-units', StoreUnitController::class)->parameters(['store-units' => 'storeUnit'])->names('admin.store-units');

Route::patch('store-units/{storeUnit}/toggle-status', [StoreUnitController::class, 'toggleStatus'])->name('admin.store-units.toggle-status');


// Stores - Physical/Logical Stores

Route::get('stores/store-requisitions', [StoreRequisitionController::class, 'index'])->name('admin.stores.store-requisitions.index');
Route::get('stores/store-requisitions/create', [StoreRequisitionController::class, 'create'])->name('admin.stores.store-requisitions.create');
Route::post('stores/store-requisitions', [StoreRequisitionController::class, 'store'])->name('admin.stores.store-requisitions.store');
Route::get('stores/store-requisitions/{storeRequisition}/edit', [StoreRequisitionController::class, 'edit'])->name('admin.stores.store-requisitions.edit');
Route::put('stores/store-requisitions/{storeRequisition}', [StoreRequisitionController::class, 'update'])->name('admin.stores.store-requisitions.update');
Route::post('stores/store-requisitions/{storeRequisition}/submit', [StoreRequisitionController::class, 'submit'])->name('admin.stores.store-requisitions.submit');
Route::post('stores/store-requisitions/{storeRequisition}/approve', [StoreRequisitionController::class, 'approve'])->name('admin.stores.store-requisitions.approve');
Route::post('stores/store-requisitions/{storeRequisition}/reject', [StoreRequisitionController::class, 'reject'])->name('admin.stores.store-requisitions.reject');
Route::post('stores/store-requisitions/{storeRequisition}/send-back', [StoreRequisitionController::class, 'sendBack'])->name('admin.stores.store-requisitions.send-back');
Route::get('stores/store-requisitions/{storeRequisition}', [StoreRequisitionController::class, 'show'])->name('admin.stores.store-requisitions.show');
Route::delete( '/stores/store-requisitions/{storeRequisition}',[StoreRequisitionController::class, 'destroy'])->name('admin.stores.store-requisitions.destroy');
// Stores - Fulfillments
Route::post('/stores/store-requisitions/{storeRequisition}/fulfill', [StoreFulfillmentController::class, 'store'])->name('admin.stores.store-requisitions.fulfill');
Route::get( '/stores/store-requisitions/fulfillments/{fulfillment}/receipt',[StoreFulfillmentController::class, 'receipt'])->name('admin.stores.store-requisitions.fulfillments.receipt');
Route::post('/stores/store-requisitions/{storeRequisition}/cancel',[StoreRequisitionController::class, 'cancel'])->name('admin.stores.store-requisitions.cancel');



Route::resource('stores', StoreController::class)->parameters(['stores' => 'store'])->names('admin.stores');

Route::patch('stores/{store}/toggle-status', [StoreController::class, 'toggleStatus'])->name('admin.stores.toggle-status');


// Stores - Items

Route::resource('store-items', StoreItemController::class)->parameters(['store-items' => 'storeItem'])->names('admin.store-items');

Route::patch('store-items/{storeItem}/toggle-status', [StoreItemController::class, 'toggleStatus'])->name('admin.store-items.toggle-status');


// Stores - Item Variants

Route::resource('store-items.variants', StoreItemVariantController::class)->parameters(['store-items' => 'storeItem', 'variants' => 'variant'])->names('admin.store-item-variants');

Route::patch('store-items/{storeItem}/variants/{variant}/toggle-status', [StoreItemVariantController::class, 'toggleStatus'])->name('admin.store-item-variants.toggle-status');


// Stores - Receipts

Route::resource('store-receipts', StoreReceiptController::class)->parameters(['store-receipts' => 'storeReceipt'])->names('admin.store-receipts');

Route::post('store-receipts/{storeReceipt}/items', [StoreReceiptItemController::class, 'store'])->name('admin.store-receipt-items.store');

Route::put('store-receipts/{storeReceipt}/items/{storeReceiptItem}', [StoreReceiptItemController::class, 'update'])->name('admin.store-receipt-items.update');

Route::delete('store-receipts/{storeReceipt}/items/{storeReceiptItem}', [StoreReceiptItemController::class, 'destroy'])->name('admin.store-receipt-items.destroy');

Route::post('store-receipts/{storeReceipt}/post', [StoreReceiptController::class, 'post'])->name('admin.store-receipts.post');


// Stores - Stock

Route::get('store-stock', [StoreStockController::class, 'index'])->name('admin.store-stock.index');

Route::get('store-stock/{storeStock}/ledger', [StoreStockController::class, 'ledger'])->name('admin.store-stock.ledger');


//supplier routes

Route::resource('suppliers', SupplierController::class) ->names('admin.suppliers');

Route::patch( 'suppliers/{supplier}/activate', [SupplierController::class, 'activate'])->name('admin.suppliers.activate');

Route::patch('suppliers/{supplier}/deactivate',[SupplierController::class, 'deactivate'])->name('admin.suppliers.deactivate');


Route::resource('store-lpos', StoreLpoController::class)->names('admin.store-lpos');
Route::post('store-lpos/{storeLpo}/submit',[StoreLpoController::class, 'submit'])->name('admin.store-lpos.submit');
Route::post('store-lpos/{storeLpo}/approval',[StoreLpoController::class, 'approval'])->name('admin.store-lpos.approval');
Route::get('store-lpos/{storeLpo}/pdf',[StoreLpoController::class, 'pdf'])->name('admin.store-lpos.pdf');


/*
| M-Pesa transaction review routes
|--------------------------------------------------------------------------
*/
Route::get( 'mpesa-transactions',  [MpesaTransactionController::class, 'index'] )->name('admin.mpesa-transactions.index');
Route::get('mpesa-transactions/{mpesaTransaction}', [MpesaTransactionController::class, 'show'])->name('admin.mpesa-transactions.show');
Route::post('mpesa-transactions/{mpesaTransaction}/confirm',[MpesaTransactionController::class, 'confirm'])->name('admin.mpesa-transactions.confirm');
Route::post( 'mpesa-transactions/{mpesaTransaction}/reject', [MpesaTransactionController::class, 'reject'])->name('admin.mpesa-transactions.reject');

});

Route::middleware(['auth'])->prefix('admin/system')->group(function () {
    Route::get('/show', [AdminController::class, 'showSystemDetails'])->name('admin.system_details.index');
    Route::get('/add', [AdminController::class, 'addSystemDetails'])->name('admin.system_details.add');
    Route::post('/store', [AdminController::class, 'storeSystemDetails'])->name('admin.system_details.store');
    Route::get('/edit/{id}', [AdminController::class, 'editSystemDetails'])->name('admin.system_details.edit');
    Route::delete('/destroy/{id}', [AdminController::class, 'deleteSystemDetails'])->name('admin.system_details.destroy');
    Route::put('/update/{id}', [AdminController::class, 'updateSystemDetails'])->name('admin.system_details.update');

});



Route::get('/admin/merchandise', function () { return 'Merchandise module route is working.';})->name('admin.merchandise.index');
Route::get('/admin/merchandise/create', function () { return 'Merchandise module route is working.';})->name('admin.merchandise.create');




Route::prefix('admin')->name('admin.')->group(function () {
    // Roles
    Route::resource('/roles', RoleController::class);
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

    // Permissions
    Route::resource('/permissions', PermissionController::class);
    

    // Assign Role to User
    Route::put('/users/{user}/roles', [AdminController::class, 'updateRoles'])->name('users.roles.update');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Roles
    Route::resource('/pettycash', PettycashController::class);
    Route::post('pettycash/addPayee', [PettyCashController::class, 'addPayee'])->name('pettycash.addPayee');

});





