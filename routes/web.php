<?php

use App\Http\Controllers\Admin\AllPaymentsController;
use App\Http\Controllers\Admin\IntermentPaymentController;
use App\Http\Controllers\Admin\LotPaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\AdminRegisteredUserController;
use App\Http\Controllers\ClientCommunicationController;
use App\Http\Controllers\ClientContractController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientFamilyLinkController;
use App\Http\Controllers\ClientLotOwnershipController;
use App\Http\Controllers\ClientMaintenanceController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ExhumationController;
use App\Http\Controllers\IntermentController;
use App\Http\Controllers\LotController;
use App\Http\Controllers\Master\AuditLogController as MasterAuditLogController;
use App\Http\Controllers\Master\MasterDashboardController;
use App\Http\Controllers\Master\UserController as MasterUserController;
use App\Http\Controllers\PaymentPlanController;
use App\Http\Controllers\PaymentReportController;
use App\Http\Controllers\PaymentTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VisitorLogController;
use App\Models\Lot;
use App\Models\Reservation;
use App\Services\LotStateService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
});

Route::view('/about-us', 'home.about')->name('about.page');
Route::view('/pricing', 'home.pricing')->name('pricing.page');

Route::view('/location', 'home.location')->name('location.page');

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
    $expiredLotIds = Reservation::expireDue(CarbonImmutable::today());
    $lotState = app(LotStateService::class);
    foreach ($expiredLotIds as $lotId) {
        $lotState->sync((int) $lotId);
    }

    $lots = Lot::with('deceased')->get();

    return view('map', compact('lots'));
})->name('public.map');

Route::get('/visit', [VisitorLogController::class, 'create'])->name('public.visit.create');
Route::post('/visit', [VisitorLogController::class, 'store'])->name('public.visit.store');
Route::get('/visit/{visitorLog}/locator', [VisitorLogController::class, 'locator'])->name('public.visit.locator');

Route::middleware('auth')->group(function () {
    Route::get('/pending-approval', function () {
        return view('auth.pending-approval');
    })->name('approval.pending');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('role:admin,master_admin')->group(function () {
        Route::prefix('admin/analytics')->name('admin.analytics.')->group(function () {
            Route::get('/', [AnalyticsController::class, 'index'])->name('index');
            Route::get('/clients', [AnalyticsController::class, 'clients'])->name('clients');
            Route::get('/plots', [AnalyticsController::class, 'plots'])->name('plots');
            Route::get('/payments', [AnalyticsController::class, 'payments'])->name('payments');
            Route::get('/documents', [AnalyticsController::class, 'documents'])->name('documents');
            Route::get('/interments', [AnalyticsController::class, 'interments'])->name('interments');
            Route::get('/visitors', [AnalyticsController::class, 'visitors'])->name('visitors');
        });

        Route::prefix('admin/lots')->name('admin.lots.')->group(function () {
            Route::get('/', [LotController::class, 'index'])->name('index');
            Route::get('/create', [LotController::class, 'create'])->name('create');
            Route::post('/', [LotController::class, 'store'])->name('store');
            Route::get('/{lot}/edit', [LotController::class, 'edit'])->name('edit');
            Route::put('/{lot}', [LotController::class, 'update'])->name('update');
            Route::delete('/{lot}', [LotController::class, 'destroy'])->name('destroy');
            Route::post('/bulk-delete', [LotController::class, 'bulkDestroy'])->name('bulkDestroy');
            Route::get('/map', [LotController::class, 'map'])->name('map');
            Route::get('/{lot}/snapshot', [LotController::class, 'snapshot'])->name('snapshot')->whereNumber('lot');
            Route::get('/next-lot-number', [LotController::class, 'nextLotNumber'])->name('nextLotNumber');
            Route::post('/with-deceased', [LotController::class, 'storeWithDeceased'])->name('storeWithDeceased');
        });

        Route::prefix('admin/clients')->name('admin.clients.')->group(function () {
            Route::get('/', [ClientController::class, 'index'])->name('index');
            Route::get('/analytics', [AnalyticsController::class, 'clients'])->name('analytics');
            Route::get('/create', [ClientController::class, 'create'])->name('create');
            Route::post('/', [ClientController::class, 'store'])->name('store');
            Route::get('/export/csv', [ClientController::class, 'exportCsv'])->name('export.csv');
            Route::get('/export/pdf', [ClientController::class, 'exportPdf'])->name('export.pdf');
            Route::get('/{client}/edit', [ClientController::class, 'edit'])->name('edit');
            Route::put('/{client}', [ClientController::class, 'update'])->name('update');
            Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
            Route::get('/{client}', [ClientController::class, 'show'])->name('show');

            Route::post('/{client}/ownerships', [ClientLotOwnershipController::class, 'store'])->name('ownerships.store');
            Route::delete('/{client}/ownerships/{ownership}', [ClientLotOwnershipController::class, 'destroy'])->name('ownerships.destroy');
            Route::post('/{client}/ownerships/{ownership}/transfer', [ClientLotOwnershipController::class, 'transfer'])->name('ownerships.transfer');

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
            Route::get('/api/clients/{client}/lots', [IntermentController::class, 'clientLots'])->name('api.clientLots');
            Route::get('/api/lot-info', [IntermentController::class, 'lotInfo'])->name('lotInfo');
            Route::get('/api/check-lot-eligibility', [IntermentController::class, 'checkLotEligibility'])->name('checkLotEligibility');
            Route::get('/api/{deceased}/payments', [IntermentController::class, 'apiPayments'])->name('api.payments');
            Route::get('/{deceased}', [IntermentController::class, 'show'])->name('show');
            Route::put('/{deceased}', [IntermentController::class, 'update'])->name('update');
            Route::delete('/{deceased}', [IntermentController::class, 'destroy'])->name('destroy');
            Route::get('/{deceased}/documents/{document}', [IntermentController::class, 'downloadDocument'])->name('documents.download');
            Route::get('/{deceased}/contract', [IntermentController::class, 'pdf'])->name('contract.pdf');
            Route::get('/{deceased}/contract/download', [IntermentController::class, 'downloadContract'])->name('contract.download');
            Route::post('/{deceased}/contract/send', [IntermentController::class, 'sendContract'])->name('contract.send');
            Route::post('/{deceased}/payment', [IntermentController::class, 'updatePayment'])->name('storePayment');
            Route::get('/{deceased}/payments/{payment}/invoice', [IntermentController::class, 'paymentInvoice'])->name('payments.invoice');
            Route::get('/{deceased}/payments/{payment}/receipt', [IntermentController::class, 'paymentReceipt'])->name('payments.receipt');
            Route::post('/{deceased}/exhumations', [ExhumationController::class, 'store'])->name('exhumations.store');
        });

        Route::prefix('admin/exhumations')->name('admin.exhumations.')->group(function () {
            Route::get('/', [ExhumationController::class, 'index'])->name('index');
            Route::get('/{exhumation}', [ExhumationController::class, 'show'])->name('show');
            Route::put('/{exhumation}', [ExhumationController::class, 'update'])->name('update');
            Route::get('/{exhumation}/documents/{document}', [ExhumationController::class, 'downloadDocument'])->name('documents.download');
            Route::delete('/{exhumation}/documents/{document}', [ExhumationController::class, 'destroyDocument'])->name('documents.destroy');
            Route::post('/{exhumation}/transfer-certificate', [ExhumationController::class, 'generateTransferCertificate'])->name('transferCertificate.generate');
            Route::get('/{exhumation}/transfer-certificate/download', [ExhumationController::class, 'downloadTransferCertificate'])->name('transferCertificate.download');
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

        Route::prefix('admin/lot-payments')->name('admin.lot-payments.')->group(function () {
            Route::get('/', [LotPaymentController::class, 'index'])->name('index');
            Route::get('/create', [LotPaymentController::class, 'create'])->name('create');
            Route::post('/', [LotPaymentController::class, 'store'])->name('store');
            Route::get('/{lotPayment}', [LotPaymentController::class, 'show'])->name('show');
            Route::put('/{lotPayment}', [LotPaymentController::class, 'update'])->name('update');
            Route::delete('/{lotPayment}', [LotPaymentController::class, 'destroy'])->name('destroy');
            Route::post('/{lotPayment}/mark-paid', [LotPaymentController::class, 'markPaid'])->name('markPaid');
            Route::post('/{lotPayment}/verify', [LotPaymentController::class, 'verify'])->name('verify');
            Route::post('/{lotPayment}/complete', [LotPaymentController::class, 'complete'])->name('complete');
            Route::get('/{lotPayment}/receipt', [LotPaymentController::class, 'downloadReceipt'])->name('downloadReceipt');
        });

        Route::get('admin/all-payments', [AllPaymentsController::class, 'index'])->name('admin.all-payments.index');

        Route::prefix('admin/interment-payments')->name('admin.interment-payments.')->group(function () {
            Route::get('/', [IntermentPaymentController::class, 'index'])->name('index');
            Route::get('/{deceased}', [IntermentPaymentController::class, 'show'])->name('show');
            Route::post('/{deceased}', [IntermentPaymentController::class, 'storePayment'])->name('store');
            Route::get('/{deceased}/payments/{payment}/invoice', [IntermentPaymentController::class, 'paymentInvoice'])->name('invoice');
            Route::get('/{deceased}/payments/{payment}/receipt', [IntermentPaymentController::class, 'paymentReceipt'])->name('receipt');
        });

        Route::prefix('admin/reports')->name('admin.reports.')->group(function () {
            Route::get('/', [ReportsController::class, 'index'])->name('index');
            Route::get('/clients', [ReportsController::class, 'clients'])->name('clients');
            Route::get('/plots', [ReportsController::class, 'plots'])->name('plots');
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

Route::post('/admin/logout', [AdminController::class, 'AdminLogout'])->name('admin.logout');

// Route::post('/admin/login', [AdminController::class, 'AdminLogin'])->name('admin.login');
Route::middleware('guest')->post('/admin/register', [AdminRegisteredUserController::class, 'store'])->name('admin.register');

Route::get('/verify', [AdminController::class, 'ShowVerification'])->name('custom.verification.form');

Route::post('/verify', [AdminController::class, 'VerificationVerify'])->name('custom.verification.verify');

Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');
Route::post('/contact-us', [ContactController::class, 'storeInquiry'])->name('contact.inquiry.submit');
