<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $activeUsers       = User::where('active', true)->count();
        $inactiveUsers     = User::where('active', false)->count();
        $totalTransactions = Transaction::count();
        $totalAmount       = Transaction::where('status', 'COMPLETED')->sum('amount');
        $allUsers          = User::orderBy('firstname')->get();

        // Transactions mensuelles (12 derniers mois)
        $monthlyData = Transaction::select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('YEAR(transaction_date) as year'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('transaction_date', date('Y'))
            ->groupBy('year', 'month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $monthLabels  = ['Jan','Fév','Mar','Avr','Mai','Juin','Juil','Août','Sep','Oct','Nov','Déc'];
        $monthCounts  = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthCounts[] = $monthlyData->has($m) ? (int)$monthlyData[$m]->total : 0;
        }

        return view('admin.dashboard', compact(
            'activeUsers', 'inactiveUsers',
            'totalTransactions', 'totalAmount',
            'allUsers', 'monthLabels', 'monthCounts'
        ));
    }
}
