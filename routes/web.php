<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ClientCommunicationController;
use App\Http\Controllers\ClientContractController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientFamilyLinkController;
use App\Http\Controllers\ClientLotOwnershipController;
use App\Http\Controllers\ClientMaintenanceController;
use App\Http\Controllers\IntermentController;
use App\Http\Controllers\PaymentPlanController;
use App\Http\Controllers\PaymentReportController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Auth\AdminRegisteredUserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Master\AuditLogController as MasterAuditLogController;
use App\Http\Controllers\Master\MasterDashboardController;
use App\Http\Controllers\Master\UserController as MasterUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
});

Route::get('/contact-us', function () {
    return redirect('/');
})->name('contact.page');

Route::get('/privacy-policy', function () {
    return view('home.privacy-policy');
})->name('privacy.policy');

Route::get('/dashboard', AdminDashboardController::class)
    ->middleware(['auth', 'verified', 'role:admin,master_admin'])
    ->name('dashboard');

Route::get('/map', function () {
    $expiredLotIds = \App\Models\Reservation::expireDue(\Carbon\CarbonImmutable::today());
    $lotState = app(\App\Services\LotStateService::class);
    foreach ($expiredLotIds as $lotId) {
        $lotState->sync((int) $lotId);
    }

    $lots = \App\Models\Lot::with('deceased')->get();

    return view('map', compact('lots'));
})->name('public.map');

    Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin,master_admin')->group(function () {
    Route::prefix('admin/lots')->name('admin.lots.')->group(function () {
        Route::get('/', [LotController::class, 'index'])->name('index');
        Route::get('/create', [LotController::class, 'create'])->name('create');
        Route::post('/', [LotController::class, 'store'])->name('store');
        Route::get('/{lot}/edit', [LotController::class, 'edit'])->name('edit');
        Route::put('/{lot}', [LotController::class, 'update'])->name('update');
        Route::delete('/{lot}', [LotController::class, 'destroy'])->name('destroy');
        Route::post('/bulk-delete', [LotController::class, 'bulkDestroy'])->name('bulkDestroy');
        Route::get('/map', [LotController::class, 'map'])->name('map');
        Route::get('/next-lot-number', [LotController::class, 'nextLotNumber'])->name('nextLotNumber');
        Route::post('/with-deceased', [LotController::class, 'storeWithDeceased'])->name('storeWithDeceased');
    });

        Route::prefix('admin/clients')->name('admin.clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/create', [ClientController::class, 'create'])->name('create');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');

        Route::post('/{client}/ownerships', [ClientLotOwnershipController::class, 'store'])->name('ownerships.store');
        Route::delete('/{client}/ownerships/{ownership}', [ClientLotOwnershipController::class, 'destroy'])->name('ownerships.destroy');

        Route::post('/{client}/contracts', [ClientContractController::class, 'store'])->name('contracts.store');
        Route::get('/{client}/contracts/{contract}/pdf', [ClientContractController::class, 'pdf'])->name('contracts.pdf');
        Route::put('/{client}/contracts/{contract}', [ClientContractController::class, 'update'])->name('contracts.update');
        Route::delete('/{client}/contracts/{contract}', [ClientContractController::class, 'destroy'])->name('contracts.destroy');

        Route::post('/{client}/family-links', [ClientFamilyLinkController::class, 'store'])->name('familyLinks.store');
        Route::delete('/{client}/family-links/{link}', [ClientFamilyLinkController::class, 'destroy'])->name('familyLinks.destroy');

        Route::post('/{client}/communications', [ClientCommunicationController::class, 'store'])->name('communications.store');
        Route::delete('/{client}/communications/{communication}', [ClientCommunicationController::class, 'destroy'])->name('communications.destroy');

        Route::middleware('role:master_admin')->group(function () {
            Route::post('/{client}/maintenance', [ClientMaintenanceController::class, 'store'])->name('maintenance.store');
            Route::delete('/{client}/maintenance/{maintenanceRecord}', [ClientMaintenanceController::class, 'destroy'])->name('maintenance.destroy');
        });
        });

        Route::prefix('admin/payments')->name('admin.payments.')->group(function () {
            Route::get('/', [PaymentPlanController::class, 'index'])->name('index');
            Route::get('/create', [PaymentPlanController::class, 'create'])->name('create');
            Route::post('/', [PaymentPlanController::class, 'store'])->name('store');
            Route::get('/{paymentPlan}', [PaymentPlanController::class, 'show'])->name('show');
            Route::post('/{paymentPlan}/notify', [PaymentPlanController::class, 'notify'])->name('notify');

            Route::post('/{paymentPlan}/transactions', [PaymentTransactionController::class, 'store'])->name('transactions.store');
        });

        Route::prefix('admin/interments')->name('admin.interments.')->group(function () {
            Route::get('/', [IntermentController::class, 'index'])->name('index');
            Route::post('/', [IntermentController::class, 'store'])->name('store');
            Route::put('/{deceased}', [IntermentController::class, 'update'])->name('update');
            Route::delete('/{deceased}', [IntermentController::class, 'destroy'])->name('destroy');
            Route::get('/{deceased}/documents/{document}', [IntermentController::class, 'downloadDocument'])->name('documents.download');
        });

        Route::prefix('admin/reservations')->name('admin.reservations.')->group(function () {
            Route::get('/', [ReservationController::class, 'index'])->name('index');
            Route::post('/', [ReservationController::class, 'store'])->name('store');
            Route::put('/{reservation}', [ReservationController::class, 'update'])->name('update');
            Route::delete('/{reservation}', [ReservationController::class, 'destroy'])->name('destroy');
            Route::get('/{reservation}/contract', [ReservationController::class, 'downloadContract'])->name('contract.download');
        });

        Route::prefix('admin/payment-transactions')->name('admin.paymentTransactions.')->group(function () {
            Route::get('/{paymentTransaction}/receipt', [PaymentTransactionController::class, 'downloadReceipt'])->name('receipt');
            Route::get('/{paymentTransaction}/invoice', [PaymentTransactionController::class, 'invoice'])->name('invoice');
        });

        Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
            Route::get('/payments', [PaymentReportController::class, 'index'])->name('payments');
        });
    });
    });

Route::prefix('master')->name('master.')->middleware(['auth', 'role:master_admin'])->group(function () {
    Route::get('/dashboard', MasterDashboardController::class)->name('dashboard');
    Route::get('/audit-logs', [MasterAuditLogController::class, 'index'])->name('auditLogs.index');

    Route::get('/users', [MasterUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [MasterUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [MasterUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [MasterUserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__.'/auth.php';

Route::get('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');

// Route::post('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');
Route::middleware('guest')->post('/admin/register', [AdminRegisteredUserController::class, 'store'])->name('admin.register');

Route::get('/verify', [AdminController::class, 'ShowVerification'])->name('custom.verification.form');

Route::post('/verify', [AdminController::class, 'VerificationVerify'])->name('custom.verification.verify');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');
Route::post('/contact-us', [ContactController::class, 'storeInquiry'])->name('contact.inquiry.submit');
