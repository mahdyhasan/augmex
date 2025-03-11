<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\TransactionController;


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

Auth::routes(['register' => false]);

Route::group(['middleware' => 'auth'], function () {
    // Dashboard
    Route::get('/', [HomeController::class, 'dashboard'])->name('dashboard');

    //Settings
    Route::group(['prefix' => 'setting', 'as' => 'setting.'], function () {
        Route::get('/show', [SettingController::class, 'show'])->name('show');
        Route::post('/list', [SettingController::class, 'list'])->name('list');
        Route::get('/create', [SettingController::class, 'create'])->name('create');
        Route::post('/store', [SettingController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SettingController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [SettingController::class, 'update'])->name('update');
    });

    // Accounts
    Route::prefix('accounts')->group(function () {
        Route::get('/', [AccountController::class, 'listAllAccounts']);
        Route::get('/{id}', [AccountController::class, 'viewAccountDetails']);
        Route::post('/create', [AccountController::class, 'createNewAccount']);
        Route::put('/update/{id}', [AccountController::class, 'updateAccountDetails']);
        Route::delete('/delete/{id}', [AccountController::class, 'deleteAccount']);
    });


    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'listAllEmployees']);
        Route::get('/{id}', [EmployeeController::class, 'viewEmployeeProfile']);
        Route::post('/register', [EmployeeController::class, 'registerNewEmployee']);
        Route::put('/update/{id}', [EmployeeController::class, 'updateEmployeeDetails']);
        Route::delete('/delete/{id}', [EmployeeController::class, 'removeEmployee']);
    });
    
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'listAllAttendance']);
        Route::get('/{employee_id}', [AttendanceController::class, 'listEmployeeAttendance']);
        Route::post('/check-in', [AttendanceController::class, 'recordEmployeeCheckIn']);
        Route::post('/check-out', [AttendanceController::class, 'recordEmployeeCheckOut']);
    });
    
    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'listAllExpenses']);
        Route::post('/record', [ExpenseController::class, 'recordNewExpense']);
        Route::put('/update/{id}', [ExpenseController::class, 'updateExpenseRecord']);
        Route::delete('/delete/{id}', [ExpenseController::class, 'deleteExpense']);
    });
    
    Route::prefix('payroll')->group(function () {
        Route::post('/generate', [PayrollController::class, 'generatePayrollForPeriod']);
        Route::get('/{id}', [PayrollController::class, 'viewPayrollDetails']);
        Route::put('/update/{id}', [PayrollController::class, 'updatePayrollRecord']);
    });
    
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'listAllTransactions']);
        Route::post('/record', [TransactionController::class, 'recordNewTransaction']);
        Route::put('/update/{id}', [TransactionController::class, 'updateTransactionDetails']);
        Route::delete('/delete/{id}', [TransactionController::class, 'deleteTransaction']);
    });
    
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'listAllClients'])->name('clients.index'); 
        Route::get('/create', [ClientController::class, 'showClientCreationForm'])->name('clients.create');
        Route::post('/', [ClientController::class, 'storeNewClient'])->name('clients.store');
        Route::get('/{id}', [ClientController::class, 'viewClientDetails'])->name('clients.show');
        Route::get('/{id}/edit', [ClientController::class, 'editClient'])->name('clients.edit');
        Route::put('/{id}', [ClientController::class, 'updateClient'])->name('clients.update');
        Route::delete('/{id}', [ClientController::class, 'deleteClient'])->name('clients.destroy');
    });

    

    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'listAllInvoices'])->name('invoices.index');
        Route::get('/create', [InvoiceController::class, 'showInvoiceCreationForm'])->name('invoices.create');
        Route::post('/', [InvoiceController::class, 'storeNewInvoice'])->name('invoices.store');
        Route::get('/{id}', [InvoiceController::class, 'viewInvoiceDetails'])->name('invoices.show');
        Route::get('/{id}/edit', [InvoiceController::class, 'editInvoice'])->name('invoices.edit');
        Route::put('/{id}', [InvoiceController::class, 'updateInvoice'])->name('invoices.update');
        Route::delete('/{id}', [InvoiceController::class, 'deleteInvoice'])->name('invoices.destroy');
    
        // Additional invoice routes
        Route::post('/{id}/mark-paid', [InvoiceController::class, 'markInvoiceAsPaid'])->name('invoices.markPaid');
        Route::get('/client/{client_id}', [InvoiceController::class, 'getInvoicesByClient'])->name('invoices.byClient');
    });
    




});
