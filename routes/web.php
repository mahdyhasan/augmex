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

Route::group(['middleware' => 'auth', 'role:superAdmin'], function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/user/profile', function () {
        return view('user.profile');
    })->name('user.profile');

    Route::get('/company', function () {
        return view('company');
    })->name('company');

    //Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



   // Accounts
    Route::prefix('accounts')->group(function () {
        Route::get('/', [AccountController::class, 'listAllAccounts']);
        Route::get('/incomeStatement', [AccountController::class, 'incomeStatement']);
        Route::get('/{id}', [AccountController::class, 'viewAccountDetails']);
        Route::post('/create', [AccountController::class, 'createNewAccount']);
        Route::put('/update/{id}', [AccountController::class, 'updateAccountDetails']);
        Route::delete('/delete/{id}', [AccountController::class, 'deleteAccount']);
    });


    Route::prefix('employees')->middleware(['auth'])->group(function () {
        Route::get('/', [EmployeeController::class, 'listAllEmployees']);
        Route::get('/profile', [EmployeeController::class, 'employeeProfile'])->name('employee.profile');
        Route::post('/profile', [EmployeeController::class, 'updateEmployeeProfile'])->name('employee.profile.update');
        Route::get('/list', [EmployeeController::class, 'employeeDetails'])->name('employee.list');
    });
        
    // Attendances
    Route::prefix('attendance')->middleware(['auth'])->group(function () {
        Route::get('/', [AttendanceController::class, 'listAllAttendance'])->name('attendance.index');
        Route::get('/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
        Route::post('/login', [AttendanceController::class, 'logIn'])->name('attendance.login');
        Route::post('/logout', [AttendanceController::class, 'logOut'])->name('attendance.logout');
    });
    
    //EXPENSES 
    Route::prefix('expenses')->group(function () {
        Route::get('/', [ExpenseController::class, 'listAllExpenses'])->name('expenses.index');
        Route::post('/record', [ExpenseController::class, 'recordNewExpense'])->name('expenses.store');
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

