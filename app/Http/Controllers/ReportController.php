<?php

namespace App\Http\Controllers;

use App\Exports\ReportDonationExport;
use App\Exports\ReportFinansialExportort;
use App\Models\Donation;
use App\Models\Finance;
use App\Models\Team;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function financials()
    {
        $data['transactions'] = Finance::type()->range()->month()->year()->get();

        // all
        $data['total_income'] = Finance::type('income')->sum('amount');
        $data['total_expense'] = Finance::type('expense')->sum('amount');
        $data['total_balance'] = $data['total_income'] - $data['total_expense'];

        // this month
        $data['total_income_month'] = Finance::where('type', 'income')->range()->month()->year()->sum('amount');
        $data['total_expense_month'] = Finance::where('type', 'expense')->range()->month()->year()->sum('amount');
        $data['total_balance_month'] = $data['total_income_month'] - $data['total_expense_month'];

        // get distinct years
        $data['years'] = Finance::selectRaw('YEAR(created_at) as year')->groupBy('year')->get();

        return view('report.financial-report', $data);
    }


    public function donations()
    {
        $querystring = request()->getQueryString();

        // dd($querystring);
        // get distinct years
        $data['years'] = Donation::selectRaw('YEAR(created_at) as year')->groupBy('year')->get();
        // get all team
        $data['teams'] = Team::all();
        // get donation
        $data['donations'] = Donation::with(['donor.district', 'donor.regency', 'donor.province'])->month()->year()->team()->range()->startDate()->endDate()->get();
        // sum donation
        $data['total_donation'] = $data['donations']->sum('amount');
        // return data to view
        return view('report.donation-report', $data);
    }



    public function exportExcel()
    {
        $month = isset(request()->month) ? request()->month : date('m');
        $year = isset(request()->year) ? request()->year : date('Y');
        $name = 'Laporan Donasi-'.$month.'-'.$year;
        return (new ReportDonationExport)->download($name.'.xlsx');
    }

    public function exportExcelFinance()
    {
        $month = isset(request()->month) ? request()->month : date('m');
        $year = isset(request()->year) ? request()->year : date('Y');
        $name = 'Laporan Keuangan-'.$month.'-'.$year;
        return (new ReportFinansialExportort)->download($name.'.xlsx');
    }
}
