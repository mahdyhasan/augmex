<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingController;

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DivanjCRMController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DivanjController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\EmployeeIncentiveFineController;


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

    // Career Page for Candidates
    Route::get('career/sales/apply', [CareerController::class, 'applyForPosition'])->name('career.apply');
    Route::post('career/sales/apply', [CareerController::class, 'storeCandidatesData'])->name('career.store');
    Route::post('/check-application', function(Request $request) {
        $exists = \App\Models\CareerApplicant::where('position', $request->position)
            ->where(function($query) use ($request) {
                $query->where('email', $request->email)
                    ->orWhere('phone', $request->phone);
            })
            ->exists();
            
        return response()->json(['exists' => $exists]);
    })->middleware('web');




Route::group(['middleware' => 'auth'], function () {

    Route::get('/', [HomeController::class, 'index'])->name('dashboard');
    

    // HR - Career
    Route::prefix('career')->middleware(['auth'])->group(function () {
        Route::get('/applicants', [CareerController::class, 'careerPageIndex'])->name('career-applicants.index');
        Route::post('/applicants/{id}/status', [CareerController::class, 'updateStatus'])->name('career-applicants.update-status');
        Route::post('/applicants/{id}/note', [CareerController::class, 'addNote'])->name('career-applicants.add-note');
        Route::get('/applicants/export', [CareerController::class, 'export'])->name('career-applicants.export');
    });

    // Accounts
    Route::prefix('accounts')->middleware(['auth'])->group(function () {

        Route::get('/income-statement', [AccountController::class, 'incomeStatement'])->name('accounts.incomeStatement');

        Route::get('/expenses', function(Request $request) {
            $request->validate([
                'category_id' => 'required|integer|exists:expense_categories,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);
        
            $expenses = Expense::with('expenseCategory')
                ->where('category_id', $request->category_id)
                ->whereBetween('expense_date', [$request->start_date, $request->end_date])
                ->orderBy('expense_date', 'desc')
                ->get();
        
            return response()->json($expenses);
        })->middleware('auth:sanctum');

    });


    // Bank Accounts
    Route::prefix('accounts/bank-accounts')->middleware(['auth'])->group(function () {
        Route::get('/', [AccountController::class, 'bankAccountsIndex'])->name('bank_accounts.index');
        Route::post('/', [AccountController::class, 'bankAccountsStore'])->name('bank_accounts.store');
        Route::get('/{id}/edit', [AccountController::class, 'bankAccountsEdit'])->name('bank_accounts.edit');
        Route::put('/{id}', [AccountController::class, 'bankAccountsUpdate'])->name('bank_accounts.update');
    });

    // Fixed Assets
    Route::prefix('accounts/fixed-assets')->middleware(['auth'])->group(function () {
        Route::get('/', [AccountController::class, 'fixedAssetsIndex'])->name('fixed_assets.index');
        Route::post('/', [AccountController::class, 'fixedAssetsStore'])->name('fixed_assets.store');
        Route::get('/{id}/edit', [AccountController::class, 'fixedAssetsEdit'])->name('fixed_assets.edit');
        Route::put('/{id}', [AccountController::class, 'fixedAssetsUpdate'])->name('fixed_assets.update');
    });

    //DEPRECIATION
    Route::prefix('accounts/depreciation-records')->middleware(['auth'])->group(function () {
        Route::get('/{asset_id}', [AccountController::class, 'depreciationRecordsIndex'])->name('depreciation_records.index');
        Route::post('/{asset_id}', [AccountController::class, 'depreciationRecordsStore'])->name('depreciation_records.store');
        Route::get('/{id}/edit', [AccountController::class, 'depreciationRecordsEdit'])->name('depreciation_records.edit');
        Route::put('/{id}', [AccountController::class, 'depreciationRecordsUpdate'])->name('depreciation_records.update');
        Route::delete('/{id}', [AccountController::class, 'depreciationRecordsDestroy'])->name('depreciation_records.destroy');
    });
    
    //PETTY CASH
    Route::prefix('accounts/petty-cash')->middleware(['auth'])->group(function () {
        Route::get('/', [AccountController::class, 'pettyCashIndex'])->name('petty_cash.index');
        Route::post('/', [AccountController::class, 'pettyCashStore'])->name('petty_cash.store');
        Route::get('/{id}/edit', [AccountController::class, 'pettyCashEdit'])->name('petty_cash.edit');
        Route::put('/{id}', [AccountController::class, 'pettyCashUpdate'])->name('petty_cash.update');
        Route::delete('/{id}', [AccountController::class, 'pettyCashDestroy'])->name('petty_cash.destroy');
    });
    
    //EMPLOYEES
    Route::prefix('employees')->middleware(['auth'])->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('employees.index');
        Route::get('/create', [EmployeeController::class, 'create'])->name('employees.create'); 
        Route::post('/', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit'); 
        Route::put('/{id}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::get('/profile', [EmployeeController::class, 'employeeProfile'])->name('employees.profile'); 
        Route::post('/profile', [EmployeeController::class, 'updateEmployeeProfile'])->name('employees.profile.update'); 



        Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.show');
        Route::get('/employees/{employee}/attendance', [EmployeeController::class, 'attendance'])->name('employees.attendance');
        Route::get('/employees/{employee}/payroll', [EmployeeController::class, 'payroll'])->name('employees.payroll');


    });
        
    
    
    //Tax Payments
    Route::prefix('accounts/tax-payments')->middleware(['auth'])->group(function () {
        Route::get('/', [AccountController::class, 'taxPaymentsIndex'])->name('tax_payments.index');
        Route::post('/', [AccountController::class, 'taxPaymentsStore'])->name('tax_payments.store');
        Route::get('/{id}/edit', [AccountController::class, 'taxPaymentsEdit'])->name('tax_payments.edit');
        Route::put('/{id}', [AccountController::class, 'taxPaymentsUpdate'])->name('tax_payments.update');
        Route::delete('/{id}', [AccountController::class, 'taxPaymentsDestroy'])->name('tax_payments.destroy');
    });
    

    //EXPENSES 
    Route::prefix('accounts/expenses')->middleware(['auth'])->group(function () {
        Route::get('/', [ExpenseController::class, 'expensesIndex'])->name('expenses.index');
        Route::post('/', [ExpenseController::class, 'expensesStore'])->name('expenses.store');
        Route::get('/{id}/edit', [ExpenseController::class, 'expensesEdit'])->name('expenses.edit');
        Route::put('/{id}', [ExpenseController::class, 'expensesUpdate'])->name('expenses.update');
        Route::delete('/{id}', [ExpenseController::class, 'expensesDestroy'])->name('expenses.destroy');
    });

    // CLIENT PAYMENTS
    Route::prefix('clients/client-payments')->middleware(['auth'])->group(function () {
        Route::get('/', [ClientController::class, 'clientPaymentsIndex'])->name('client_payments.index');
        Route::post('/', [ClientController::class, 'clientPaymentsStore'])->name('client_payments.store');
        Route::get('/{id}/edit', [ClientController::class, 'clientPaymentsEdit'])->name('client_payments.edit');
        Route::put('/{id}', [ClientController::class, 'clientPaymentsUpdate'])->name('client_payments.update');
        Route::delete('/{id}', [ClientController::class, 'clientPaymentsDestroy'])->name('client_payments.destroy');
    });
    
    // INVOICES
    Route::prefix('accounts/invoices')->middleware(['auth'])->group(function () {
        Route::get('/', [InvoiceController::class, 'invoicesIndex'])->name('invoices.index');
        Route::get('/generate', [InvoiceController::class, 'invoiceGeneration'])->name('invoices.generate');
        Route::post('/generate-invoice', [InvoiceController::class, 'generateInvoice'])->name('invoices.generate.store'); // Fixed route
        Route::get('/{id}/edit', [InvoiceController::class, 'invoicesEdit'])->name('invoices.edit');
        Route::put('/{id}', [InvoiceController::class, 'invoicesUpdate'])->name('invoices.update');
        Route::get('/{id}', [InvoiceController::class, 'viewInvoice'])->name('invoices.view');
        Route::post('/{id}/mark-as-paid', [InvoiceController::class, 'markInvoicePaid'])->name('invoices.markInvoicePaid');
    });
    
    



    // Attendances
    Route::prefix('attendance')->middleware(['auth'])->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/clock-in', [AttendanceController::class, 'clockIn'])->name('attendance.clockIn');
        Route::post('/login', [AttendanceController::class, 'logIn'])->name('attendance.login');
        Route::post('/logout', [AttendanceController::class, 'logOut'])->name('attendance.logout');
        Route::get('/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit'); 
        Route::put('/{id}', [AttendanceController::class, 'update'])->name('attendance.update');
        Route::post('/', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::post('/late-summary', [AttendanceController::class, 'lateSummary'])->name('attendance.late.summary');

        // Leave Management Routes
        Route::get('/leaves', [AttendanceController::class, 'leavesIndex'])->name('attendance.leaves');
        Route::post('/leaves', [AttendanceController::class, 'storeLeave'])->name('leaves.store');
        Route::get('/leaves/{leave}/edit', [AttendanceController::class, 'editLeave'])->name('leaves.edit');
        Route::put('/leaves/{leave}', [AttendanceController::class, 'updateLeave'])->name('leaves.update');
        Route::delete('/leaves/{leave}', [AttendanceController::class, 'destroyLeave'])->name('leaves.destroy');
        Route::post('/leaves/{leave}/approve', [AttendanceController::class, 'approveLeave'])->name('leaves.approve');

        Route::delete('/delete-filtered', [AttendanceController::class, 'deleteFiltered'])->name('attendance.delete.filtered');


    });
    
    
    // PAYROLL
    Route::prefix('payroll')->middleware(['auth'])->group(function () {
        Route::get('/', [PayrollController::class, 'index'])->name('payrolls.index'); 
        Route::get('/{id}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit'); 
        Route::put('/salary-sheet', [PayrollController::class, 'updateSalarySheet'])->name('payrolls.update.salary.sheet');
        Route::put('/{id}', [PayrollController::class, 'update'])->name('payrolls.update');
        Route::get('/{id}/view', [PayrollController::class, 'view'])->name('payrolls.view');
        Route::post('/{id}/mark-as-paid', [PayrollController::class, 'markAsPaid'])->name('payrolls.markAsPaid');
        Route::post('/mark-month-as-paid', [PayrollController::class, 'markMonthAsPaid'])->name('payrolls.markMonthAsPaid');

        Route::post('/generate-all', [PayrollController::class, 'generateAll'])->name('payrolls.generate.all');
 
        Route::get('/salary-sheet', [PayrollController::class, 'salarySheet'])->name('payrolls.salary.sheet'); 
        Route::get('/salary-sheet/export', [PayrollController::class, 'exportSalarySheet'])->name('payrolls.salary.sheet.export'); 
        Route::get('/salary-sheet/print', [PayrollController::class, 'printSalarySheet'])->name('payrolls.salary.sheet.print'); 
        Route::get('/cash-signature-sheet', [PayrollController::class, 'downloadCashSignatureSheet'])->name('payrolls.cash.signature');

        Route::get('/{payroll}/deductions', [PayrollController::class, 'showDeductions'])->name('payrolls.deductions');
    });

    
    
    Route::prefix('clients')->middleware(['auth'])->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('clients.index'); 
        Route::get('/create', [ClientController::class, 'showClientCreationForm'])->name('clients.create'); 
        Route::post('/', [ClientController::class, 'store'])->name('clients.store');
        Route::get('/{id}/edit', [ClientController::class, 'edit'])->name('clients.edit'); 
        Route::put('/{id}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });
    


    Route::prefix('user')->middleware(['auth'])->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('user.index');
        Route::get('/create', [SettingController::class, 'create'])->name('user.create');
        Route::post('/', [SettingController::class, 'store'])->name('user.store');
        Route::get('/{id}/edit', [SettingController::class, 'edit'])->name('user.edit');
        Route::put('/{id}', [SettingController::class, 'update'])->name('user.update');
        Route::get('/password-change', [SettingController::class, 'passwordChange'])->name('user.passwordChange');
        Route::post('/update-password', [SettingController::class, 'updatePassword'])->name('user.updatePassword');
    });

    //DEPRICATED
    Route::get('/commission-for-dillon', [ReportController::class, 'commissionforDillon'])->name('commission.for.dillon');
    Route::post('/update-commission-dillon', [ReportController::class, 'updateCommissionForDillon'])->name('update.commission.dillon');


    Route::prefix('divanj')->middleware(['auth'])->group(function () {

        Route::get('/commissions', [DivanjController::class, 'showCommissionListDivanj'])->name('divanj.commission.index');
        Route::post('/commissions/generate', [DivanjController::class, 'generateCommissionDivanj'])->name('divanj.commission.generate');
        // Route::get('/commissions/{id}/view', [DivanjController::class, 'viewCommissionDivanj'])->name('divanj.commission.view');
        // Route::get('/commissions/{id}/edit', [DivanjController::class, 'editCommissionDivanj'])->name('divanj.commission.edit');
        // Route::put('/commissions/update/{id}', [DivanjController::class, 'updateCommissionDivanj'])->name('divanj.commission.update');
        Route::get('/sales-summary', [DivanjController::class, 'salesSummaryDivanj'])->name('divanj.sales.summary');
        Route::get('/narrative-report', [DivanjController::class, 'narrativeReport'])->name('divanj.narrative.report');
        Route::get('/narrative-report-all', [DivanjController::class, 'narrativeReportForAll'])->name('divanj.narrative.report.all');
        Route::get('/sales-report', [DivanjController::class, 'salesReport'])->name('divanj.sales.report');        
        Route::get('/dashboard', [DivanjController::class, 'divanjDashboard'])->name('divanj.dashboard');
        Route::get('/predictive-report', [PredictionController::class, 'salesPredictionReport'])->name('divanj.predictive.report');

        Route::post('/sales-report', [DivanjController::class, 'importSalesDivanj'])->name('divanj.sales.import');
        Route::post('/sales-report/admin', [DivanjController::class, 'importSalesDivanjAdmin'])->name('divanj.admin.sales.import');
        Route::post('/sales/preview-import', [DivanjController::class, 'previewSalesImport'])->name('divanj.sales.preview-import');
        Route::post('/sales/process-import', [DivanjController::class, 'processSalesImport'])->name('divanj.sales.process-import');
        Route::get('/sales/preview', [DivanjController::class, 'showSalesPreview'])->name('divanj.show.sales.preview');
        

    });

    Route::prefix('agent')->middleware(['auth'])->group(function () {
        Route::get('/commission-history', [DivanjController::class, 'salesCommissionForAgent'])->name('divanj.agent.commission.history');

   
    });



    Route::prefix('incentives-fines')->middleware(['auth'])->group(function () {
        Route::get('/', [EmployeeIncentiveFineController::class, 'index'])->name('employees.incentives.fines');
        Route::get('/create', [EmployeeIncentiveFineController::class, 'create'])->name('employees.incentives.fines.create');
        Route::post('/store', [EmployeeIncentiveFineController::class, 'store'])->name('employees.incentives.fines.store');
        Route::get('/summary', [EmployeeIncentiveFineController::class, 'summary'])->name('employees.incentives.fines.summary');

    });







    
    // CRM ROUTES
    
    Route::prefix('divanj/crm')->middleware(['auth'])->group(function () {

        Route::get('/leads', [DivanjCRMController::class, 'leadIndex'])->name('divanj.crm.leads.index');
        Route::get('/leads/{lead}', [DivanjCRMController::class, 'leadShow'])->name('divanj.crm.leads.show')->whereNumber('lead');
        Route::get('/leads/{lead}/edit', [DivanjCRMController::class, 'leadEdit'])->name('divanj.crm.leads.edit')->whereNumber('lead');
        Route::put('/leads/{lead}', [DivanjCRMController::class, 'leadUpdate'])->name('divanj.crm.leads.update')->whereNumber('lead');
        Route::get('/leads/pm', [DivanjCRMController::class, 'pmLeads'])->name('divanj.crm.leads.pm');
    
        // Show add new lead form
        Route::get('/leads/add', [DivanjCRMController::class, 'addLeadForm'])->name('divanj.crm.leads.add');

        // Store new lead (single or bulk)
        Route::post('/leads', [DivanjCRMController::class, 'leadStore'])->name('divanj.crm.leads.store');
        Route::post('/leads/check-phone', [DivanjCRMController::class, 'checkPhoneNumber'])->name('divanj.crm.leads.checkPhone');


        Route::post('/call-reports', [DivanjCRMController::class, 'storeCallReport'])->name('divanj.crm.call-report.store');
    
        Route::get('/leads/call-back-sheet', [DivanjCRMController::class, 'callBackSheet'])->name('divanj.crm.call-back-sheet');
    
        Route::put('/leads/payment-details/{lead_id}', [DivanjCRMController::class, 'paymentMethodUpdate'])->name('divanj.crm.payment.update')->whereNumber('lead_id');
    
        Route::get('/followups', [DivanjCRMController::class, 'followupIndex'])->name('divanj.crm.followups.index');


    });

















});



