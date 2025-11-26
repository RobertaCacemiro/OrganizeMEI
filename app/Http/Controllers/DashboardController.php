<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\DB;
use Laravel\Pail\ValueObjects\Origin\Console;

class DashboardController extends Controller
{
    public function index()
    {
        $charts = new ChartController();

        return inertia('Home', [
            'monthlyBalance' => $charts->monthlyBalance(request()),
            'billingStatus' => $charts->billingStatus(request()),
            'expenseCategories' => $charts->expenseCategories(request()),
            'incomeCategories' => $charts->incomeCategories(request()),
        ]);
    }
}
