<?php

namespace App\Http\Controllers;

use App\Exports\BalanceSheetExport;
use App\Exports\ProfitLossExport;
use App\Exports\SalesReportExport;
use App\Exports\TrialBalancExport;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountSubType;
use App\Models\ChartOfAccountType;
use App\Models\User;
use App\Traits\BalanceSheetReport;
use App\Traits\PayablesReport;
use App\Traits\ProfitLossReport;
use App\Traits\SalesReceivable;
use App\Traits\SalesReport;
use App\Traits\TrialBalanceReport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DoubleEntryReportController extends Controller
{
    use TrialBalanceReport;
    use BalanceSheetReport;
    use ProfitLossReport;
    use SalesReport;
    use SalesReceivable;
    use PayablesReport;

    public function getReportView($request, $view, $defaultView = 'vertical')
    {
        $validViews = ['vertical', 'horizontal'];
        $viewType   = $request->view ?? $view;
        
        if (in_array($viewType, $validViews)) {
            return $viewType;
        }        
        return $defaultView;
    }

    public function ledgerSummary(Request $request, $account = '')
    {

        if (\Auth::user()->can('ledger report')) {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
            } else {
                $start = date('Y-m-01');
                $end = date('Y-m-t');
            }
            if (!empty($request->account)) {
                $chart_accounts = ChartOfAccount::where('id', $request->account)->where('created_by', \Auth::user()->creatorId())->get();
                $accounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.parent')
                ->where('parent', '=', 0)
                ->where('created_by', \Auth::user()->creatorId())->get()
                ->toarray();

            } else {
                $chart_accounts = ChartOfAccount::where('created_by', \Auth::user()->creatorId())->get();
                $accounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_accounts.parent')
                ->where('parent', '=', 0)
                ->where('created_by', \Auth::user()->creatorId())->get()
                ->toarray();
            }

            $subAccounts = ChartOfAccount::select('chart_of_accounts.id', 'chart_of_accounts.code', 'chart_of_accounts.name', 'chart_of_account_parents.account');
            $subAccounts->leftjoin('chart_of_account_parents', 'chart_of_accounts.parent', 'chart_of_account_parents.id');
            $subAccounts->where('chart_of_accounts.parent', '!=', 0);
            $subAccounts->where('chart_of_accounts.created_by', \Auth::user()->creatorId());
            $subAccounts = $subAccounts->get()->toArray();

            $balance = 0;
            $debit = 0;
            $credit = 0;
            $filter['balance'] = $balance;
            $filter['credit'] = $credit;
            $filter['debit'] = $debit;
            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;
            return view('doubleentry_report.ledger_summary', compact('filter', 'chart_accounts', 'accounts', 'subAccounts'));

        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function balanceSheet(Request $request, $view = '', $collapseview = 'expand')
    {
        if (\Auth::user()->can('balance sheet report')) {
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
            } else {
                $start = date('Y-01-01');
                $end = date('Y-m-d');
            }
            $types = ChartOfAccountType::where('created_by', \Auth::user()->creatorId())->whereIn('name', ['Assets', 'Liabilities', 'Equity'])->get();
            $totalAccounts = [];
            foreach ($types as $type) {
                $subTypes     = ChartOfAccountSubType::where('type', $type->id)->get();
                $subTypeArray = $this->buildSubTypeArray($type, $subTypes, $start, $end);
                $totalAccounts[$type->name] = $subTypeArray;
                $mainTypeIds        = $types->pluck('id')->toArray();
                $otherAccounts      = $this->getOtherAccounts($mainTypeIds, $start, $end);
                $balanceTotal       = 0;
                $currentYearEarning = [];
                foreach ($otherAccounts as $account) {
                    $balance       = $account->totalCredit - $account->totalDebit;
                    $balanceTotal += $balance;
                }
                if ($balanceTotal != 0) {
                    $currentYearEarning[] = [[
                        'account_id'   => null,
                        'account_code' => null,
                        'account_name' => 'Current Year Earnings',
                        'account'      => '',
                        'totalCredit'  => 0,
                        'totalDebit'   => 0,
                        'netAmount'    => $balanceTotal,
                    ]];
                    $totalAccounts['Equity'][] = [
                        'account' => $currentYearEarning,
                    ];
                }
            }

            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;

            $viewType                 = $this->getReportView($request, $view);
            return view('doubleentry_report.balance_sheet' . ($viewType === 'horizontal' ? '_horizontal' : ''), compact('filter', 'totalAccounts', 'collapseview'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function balanceSheetExport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-m-01');
            $end = date('Y-m-t');
        }

        $types = ChartOfAccountType::where('created_by', \Auth::user()->creatorId())->whereIn('name', ['Assets', 'Liabilities', 'Equity'])->get();
        $totalAccounts = [];
        foreach ($types as $type) {
            $subTypes     = ChartOfAccountSubType::where('type', $type->id)->get();
            $subTypeArray = $this->buildSubTypeArray($type, $subTypes, $start, $end);
            $totalAccounts[$type->name] = $subTypeArray;
            $mainTypeIds        = $types->pluck('id')->toArray();
            $otherAccounts      = $this->getOtherAccounts($mainTypeIds, $start, $end);
            $balanceTotal       = 0;
            $currentYearEarning = [];
            foreach ($otherAccounts as $account) {
                $balance       = $account->totalCredit - $account->totalDebit;
                $balanceTotal += $balance;
            }
            if ($balanceTotal != 0) {
                $currentYearEarning[] = [[
                    'account_id'   => null,
                    'account_code' => null,
                    'account_name' => 'Current Year Earnings',
                    'account'      => '',
                    'totalCredit'  => 0,
                    'totalDebit'   => 0,
                    'netAmount'    => $balanceTotal,
                ]];
                $totalAccounts['Equity'][] = [
                    'account' => $currentYearEarning,
                ];
            }
        }

        $companyName = User::where('id', \Auth::user()->creatorId())->first();
        $companyName = $companyName->name;

        $name = 'balance_sheet_' . date('Y-m-d i:h:s');
        $data = Excel::download(new BalanceSheetExport($totalAccounts, $start, $end, $companyName), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function profitLoss(Request $request, $view = '', $collapseView = 'expand')
    {
        if (\Auth::user()->can('loss & profit report')) {
            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
            } else {
                $start = date('Y-01-01');
                $end = date('Y-m-d');
            }
            $types = ChartOfAccountType::where('created_by', \Auth::user()->creatorId())->whereIn('name', ['Income', 'Expenses', 'Costs of Goods Sold'])->get();

            $totalAccounts = $this->processProfitLossData($types, $start, $end);

            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;

            $viewType = $this->getReportView($request, $view);
            return view('doubleentry_report.profit_loss' . ($viewType === 'horizontal' ? '_horizontal' : ''), 
                compact('filter', 'totalAccounts', 'collapseView'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function profitLossExport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d');
        }

        $types = ChartOfAccountType::where('created_by', \Auth::user()->creatorId())->whereIn('name', ['Income', 'Expenses', 'Costs of Goods Sold'])->get();

        $totalAccounts = $this->processProfitLossData($types, $start, $end);

        $companyName = User::where('id', \Auth::user()->creatorId())->first();
        $companyName = $companyName->name;

        $name = 'profit & loss_' . date('Y-m-d i:h:s');
        $data = Excel::download(new ProfitLossExport($totalAccounts, $start, $end, $companyName), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }
    
    public function trialBalanceSummary(Request $request, $view = "expand")
    {
        if (\Auth::user()->can('trial balance report')) {

            if (!empty($request->start_date) && !empty($request->end_date)) {
                $start = $request->start_date;
                $end = $request->end_date;
            } else {
                $start = date('Y-01-01');
                $end = date('Y-m-d');
            }

            $types = $this->getAccountTypes();
            $totalAccounts = $this->processAccountTypes($types, $start, $end);
            $filter['startDateRange'] = $start;
            $filter['endDateRange'] = $end;
            return view('doubleentry_report.trial_balance', compact('filter', 'totalAccounts', 'view'));
        } else {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function trialBalanceExport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d');
        }

        $types         = $this->getAccountTypes();
        $totalAccounts = $this->processAccountTypes($types, $start, $end);
        
        $companyName = User::where('id', \Auth::user()->creatorId())->first();
        $companyName = $companyName->name;

        $name = 'trial_balance_' . date('Y-m-d i:h:s');
        $data = Excel::download(new TrialBalancExport($totalAccounts, $start, $end, $companyName), $name . '.xlsx');
        ob_end_clean();

        return $data;
    }

    public function salesReport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d');
        }

        $invoiceItems     = $this->getInvoiceItems($start, $end);
        $invoiceCustomers = $this->getInvoiceCustomers($start, $end);

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;

        return view('doubleentry_report.sales_report', compact('filter', 'invoiceItems', 'invoiceCustomers'));
    }

    public function salesReportExport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d');
        }
        if ($request->report == '#item') {
            $invoiceItems     = $this->getInvoiceItems($start, $end);
            $reportName = 'Item';
        } else {
            $invoiceItems = $this->getInvoiceCustomers($start, $end);
            $reportName = 'Customer';
        }
        $companyName = User::where('id', \Auth::user()->creatorId())->first();
        $companyName = $companyName->name;

        $name = 'Sales By ' . $reportName . '_ ' . date('Y-m-d i:h:s');
        $data = Excel::download(new SalesReportExport($invoiceItems, $start, $end, $companyName, $reportName), $name . '.xlsx');
        ob_end_clean();

        return $data;

    }

    public function ReceivablesReport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d');
        }

        $customers           = $this->getCustomers();
        $receivableCustomers = $this->getReceivableCustomers($start, $end);
        $receivableSummaries = $this->getReceivableSummaries($start, $end);
        $receivableDetails   = $this->getReceivableDetails($start, $end);
        $agingSummaries      = $this->getAgingSummaries($start, $end);
        $agingDetails        = $this->getAgingDetails($start, $end);
        
        $moreThan45 = $agingDetails['moreThan45'] ?? [];
        $days31to45 = $agingDetails['days31to45'] ?? [];
        $days16to30 = $agingDetails['days16to30'] ?? [];
        $days1to15  = $agingDetails['days1to15'] ?? [];
        $currents   = $agingDetails['currents'] ?? [];

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;

        return view('doubleentry_report.receivable_report', compact('filter', 'receivableCustomers', 'receivableSummaries', 'receivableDetails', 'agingSummaries', 'currents', 'days1to15', 'days16to30', 'days31to45', 'moreThan45'));
    }

    public function PayablesReport(Request $request)
    {
        if (!empty($request->start_date) && !empty($request->end_date)) {
            $start = $request->start_date;
            $end = $request->end_date;
        } else {
            $start = date('Y-01-01');
            $end = date('Y-m-d');
        }

        $vendor           = $this->getVendor();
        $payableVendors   = $this->getPayableVendors($start, $end);
        $payableSummaries = $this->getPayableSummaries($start, $end);
        $payableDetails   = $this->getPayableDetails($start, $end);
        $agingSummaries   = $this->getPayableAgingSummaries($start, $end);
        $agingDetails     = $this->getPayableAgingDetails($start, $end);
        
        $moreThan45 = $agingDetails['moreThan45'] ?? [];
        $days31to45 = $agingDetails['days31to45'] ?? [];
        $days16to30 = $agingDetails['days16to30'] ?? [];
        $days1to15  = $agingDetails['days1to15'] ?? [];
        $currents   = $agingDetails['currents'] ?? [];

        $filter['startDateRange'] = $start;
        $filter['endDateRange'] = $end;

        return view('doubleentry_report.payable_report', compact('filter', 'payableVendors','payableSummaries', 'payableDetails', 'agingSummaries', 'moreThan45', 'days31to45', 'days16to30', 'days1to15', 'currents', 'vendor'));
    }
}
