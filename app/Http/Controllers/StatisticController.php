<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Finance;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teams = Team::all();

        $datas =  [];

        foreach ($teams as $team) {
            $a = Donation::
                whereHas('donor', function ($query) use ($team){
                    $query->where('team_id', $team->id);
                })->
                select(
                    DB::raw('sum(donations.amount) as sums'), 
                    DB::raw("DATE_FORMAT(donations.created_at,'%m %M %Y') as months")
                )
                ->groupBy('months')->year()
                ->orderBy('months', 'asc')
                ->get()->toArray();
       
            $datas[$team->name] = $a;
        }

        $data['recaps'] = Donation::select(
                DB::raw('sum(donations.amount) as sums'), 
                DB::raw("DATE_FORMAT(donations.created_at,'%m %M %Y') as months")
            )->groupBy('months')->year()
            ->orderBy('months', 'asc')
            ->get();
                

        $data['income'] = Finance::select(
                DB::raw('sum(amount) as sums'), 
                DB::raw("DATE_FORMAT(created_at,'%m %M %Y') as months")
            )->groupBy('months')->year()
            ->where('type', 'income')
            ->orderBy('months', 'asc')
            ->get()->toArray();

        $data['expense'] = Finance::select(
                DB::raw('sum(amount) as sums'), 
                DB::raw("DATE_FORMAT(created_at,'%m %M %Y') as months")
            )->groupBy('months')->year()
            ->where('type', 'expense')
            ->orderBy('months', 'asc')
            ->get()->toArray();


        $data['sum_donation'] = Donation::year()->sum('amount');
        $data['sum_income'] = Finance::year()->where('type', 'income')->sum('amount');
        $data['sum_expense'] = Finance::year()->where('type', 'expense')->sum('amount');
        $data['sum_balance'] = $data['sum_income'] - $data['sum_expense'];

        $data['years'] = Donation::selectRaw('YEAR(created_at) as year')->groupBy('year')->get();

        $data['donations'] = $datas;

        return view('statistic.index', $data);
    }

}
