<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class ChartController extends Controller
{
    public function monthlyBalance(Request $request)
    {
        $period = $request->period ?? 'month';
        $year = $request->year ?? now()->year;

        $currentMonth = now()->month;
        $date = \Carbon\Carbon::create($year, $currentMonth, 1);

        $daysInMonth = $date->daysInMonth;

        $userId = auth()->id();
        $meiId = session('mei_id');

        if ($period === 'year') {
            return $this->balanceByYear($userId, $meiId, $year);
        }

        if ($period === 'month') {
            return $this->balanceByMonth($userId, $meiId, $year);
        }

        if ($period === 'week') {
            return $this->balanceByWeek($userId, $meiId);
        }
    }

    private function balanceByWeek($userId, $meiId)
    {
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        // Criar labels como: "Seg (18)"
        $weekdayLabels = [];
        $cursor = $start->copy();

        for ($i = 0; $i < 7; $i++) {
            $weekdayLabels[] = $cursor->format('d/m');
            $cursor->addDay();
        }

        $receitas = array_fill(0, 7, 0);
        $despesas = array_fill(0, 7, 0);

        $daily = DB::table('transactions')
            ->select(
                DB::raw('DATE(transaction_date) as day'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as receitas'),
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as despesas')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereBetween('transaction_date', [$start, $end])
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        foreach ($daily as $d) {
            $weekday = \Carbon\Carbon::parse($d->day)->dayOfWeekIso - 1;
            $receitas[$weekday] = (float) $d->receitas;
            $despesas[$weekday] = (float) $d->despesas;
        }

        return $this->buildChartResponse($weekdayLabels, $receitas, $despesas);
    }


    private function balanceByMonth($userId, $meiId, $year)
    {
        $currentMonth = now()->month;

        // Corrige quantidade de dias do mês
        $date = \Carbon\Carbon::create($year, $currentMonth, 1);
        $daysInMonth = $date->daysInMonth;

        $daily = DB::table('transactions')
            ->select(
                DB::raw('DAY(transaction_date) as day'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as receitas'),
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as despesas')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $currentMonth)
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $labels = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $labels[] = $i;
        }

        $receitas = array_fill(0, $daysInMonth, 0);
        $despesas = array_fill(0, $daysInMonth, 0);

        foreach ($daily as $d) {
            $i = $d->day - 1;
            $receitas[$i] = (float) $d->receitas;
            $despesas[$i] = (float) $d->despesas;
        }

        return $this->buildChartResponse($labels, $receitas, $despesas);
    }


    private function balanceByYear($userId, $meiId, $year)
    {
        $monthly = DB::table('transactions')
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as receitas'),
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as despesas')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('transaction_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $labels = [
            'Jan',
            'Fev',
            'Mar',
            'Abr',
            'Mai',
            'Jun',
            'Jul',
            'Ago',
            'Set',
            'Out',
            'Nov',
            'Dez'
        ];

        $receitas = array_fill(0, 12, 0);
        $despesas = array_fill(0, 12, 0);

        foreach ($monthly as $m) {
            $i = $m->month - 1;
            $receitas[$i] = (float) $m->receitas;
            $despesas[$i] = (float) $m->despesas;
        }

        return $this->buildChartResponse($labels, $receitas, $despesas);
    }

    private function buildChartResponse($labels, $receitas, $despesas)
    {
        $saldo = [];


        foreach ($receitas as $i => $valor) {
            $saldo[$i] = $receitas[$i] - $despesas[$i];
        }

        $totalSaldo = array_sum($saldo);

        return [
            'chartData' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Receitas',
                        'data' => $receitas,
                        'borderColor' => '#3DA700',
                        'backgroundColor' => '#3DA700',
                    ],
                    [
                        'label' => 'Despesas',
                        'data' => $despesas,
                        'borderColor' => '#ef4444',
                        'backgroundColor' => '#ef4444',
                    ],
                    [
                        'label' => 'Saldo',
                        'data' => $saldo,
                        'borderColor' => '#3B82F6',
                        'backgroundColor' => '#3B82F6',
                        'fill' => false,
                        'tension' => 0.4
                    ]
                ]
            ],
            'final_balance' => $totalSaldo,
            'chartOptions' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'display' => true,
                        'position' => 'bottom',
                        'labels' => [
                            'usePointStyle' => true,
                            'boxWidth' => 12,
                            'padding' => 20,
                        ],
                    ],
                ],
            ]
        ];
    }

    public function billingStatus(Request $request)
    {
        $period = $request->period ?? 'month';
        $year = $request->year ?? now()->year;

        if ($period === 'year') {
            return $this->billingByYear($year);
        }

        if ($period === 'month') {
            return $this->billingByMonth($year);
        }

        if ($period === 'week') {
            return $this->billingByWeek($year);
        }
    }

    public function billingByYear($year)
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        // Mapa de status → grupo final
        $statusGroup = [
            0 => 'erro',
            1 => 'pendente_envio',
            2 => 'pendente_pagamento',
            3 => 'pago',
            4 => 'atrasado',
            5 => 'cancelado',
            6 => 'pago',
        ];

        $labels = [
            'erro' => 'Erro de Envio',
            'pendente_envio' => 'Pendente Envio',
            'pendente_pagamento' => 'Pendente Pagamento',
            'pago' => 'Pago',
            'atrasado' => 'Atrasado',
            'cancelado' => 'Envio Cancelado',
        ];

        $colors = [
            'erro' => '#6B7280',
            'pendente_envio' => '#F59E0B',
            'pendente_pagamento' => '#3B82F6',
            'pago' => '#3DA700',
            'atrasado' => '#EF4444',
            'cancelado' => '#A855F7',
        ];

        $monthly = DB::table('payments')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('created_at', $year)
            ->groupBy(DB::raw('MONTH(created_at)'), 'status')
            ->get();

        $total = DB::table('payments')
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('created_at', $year)
            ->count();

        // inicializa arrays
        $data = [];
        foreach (array_keys($labels) as $grp) {
            $data[$grp] = array_fill(0, 12, 0);
        }

        // preenche baseado nos status válidos
        foreach ($monthly as $item) {

            if (!isset($statusGroup[$item->status])) {
                continue;
            }

            $group = $statusGroup[$item->status];
            $index = $item->month - 1;

            $data[$group][$index] += $item->total;
        }

        // monta datasets
        $datasets = [];
        foreach (array_keys($labels) as $grp) {
            $datasets[] = [
                'label' => $labels[$grp],
                'data' => array_values($data[$grp]),
                'backgroundColor' => $colors[$grp],
                'borderColor' => $colors[$grp],
            ];
        }

        return [
            'totalCharges' => $total,
            'chartData' => [
                'labels' => ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                'datasets' => $datasets
            ],
            'chartOptions' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]
        ];
    }

    public function billingByMonth($year)
    {
        $userId = auth()->id();
        $meiId = session('mei_id');
        $month = now()->month;
        $daysInMonth = now()->daysInMonth;

        // Mapa interno -> status final agrupado
        $statusGroup = [
            0 => 'erro',
            1 => 'pendente_envio',
            2 => 'pendente_pagamento',
            3 => 'pago',          // cliente
            4 => 'atrasado',
            5 => 'cancelado',
            6 => 'pago',          // usuário
        ];

        $labels = [
            'erro' => 'Erro de Envio',
            'pendente_envio' => 'Pendente Envio',
            'pendente_pagamento' => 'Pendente Pagamento',
            'pago' => 'Pago',
            'atrasado' => 'Atrasado',
            'cancelado' => 'Cancelado',
        ];

        $colors = [
            'erro' => '#6B7280', // cinza
            'pendente_envio' => '#F59E0B',
            'pendente_pagamento' => '#3B82F6',
            'pago' => '#10B981',
            'atrasado' => '#EF4444',
            'cancelado' => '#A855F7', // roxo
        ];

        $daily = DB::table('payments')
            ->select(
                DB::raw('DAY(created_at) as day'),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy(DB::raw('DAY(created_at)'), 'status')
            ->get();

        $total = DB::table('payments')
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();


        $data = [];

        foreach (array_keys($labels) as $grp) {
            $data[$grp] = array_fill(0, $daysInMonth, 0);
        }

        foreach ($daily as $item) {
            $index = $item->day - 1;
            $group = $statusGroup[$item->status];
            $data[$group][$index] += $item->total;
        }

        // ===== RETORNO =====
        return [
            'totalCharges' => $total,
            'chartData' => [
                'labels' => range(1, $daysInMonth),
                'datasets' => array_map(function ($group) use ($data, $labels, $colors) {
                    return [
                        'label' => $labels[$group],
                        'data' => $data[$group],
                        'backgroundColor' => $colors[$group],
                        'borderColor' => $colors[$group],
                    ];
                }, array_keys($labels))
            ],
            'chartOptions' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]
        ];
    }


    public function billingByWeek($year)
    {
        $userId = auth()->id();
        $meiId = session('mei_id');

        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        // Mapa status → grupo final
        $statusGroup = [
            0 => 'erro',
            1 => 'pendente_envio',
            2 => 'pendente_pagamento',
            3 => 'pago',
            4 => 'atrasado',
            5 => 'cancelado',
            6 => 'pago',
        ];

        $labelsDisplay = [
            'erro' => 'Erro de Envio',
            'pendente_envio' => 'Pendente Envio',
            'pendente_pagamento' => 'Pendente Pagamento',
            'pago' => 'Pago',
            'atrasado' => 'Atrasado',
            'cancelado' => 'Envio Cancelado',
        ];

        $colors = [
            'erro' => '#6B7280',
            'pendente_envio' => '#F59E0B',
            'pendente_pagamento' => '#3B82F6',
            'pago' => '#10B981',
            'atrasado' => '#EF4444',
            'cancelado' => '#A855F7',
        ];

        $daily = DB::table('payments')
            ->select(
                DB::raw('DATE(created_at) as date'),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
            ->groupBy(DB::raw('DATE(created_at)'), 'status')
            ->get();

        $labels = [];
        $dates = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $start->copy()->addDays($i);
            $labels[] = $day->format('d/m');
            $dates[] = $day->toDateString();
        }

        $data = [];
        foreach (array_keys($labelsDisplay) as $grp) {
            $data[$grp] = array_fill(0, 7, 0);
        }

        foreach ($daily as $item) {
            $group = $statusGroup[$item->status];
            $index = array_search($item->date, $dates);

            if ($index !== false) {
                $data[$group][$index] += $item->total;
            }
        }

        $total = DB::table('payments')
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereBetween(DB::raw('DATE(created_at)'), [$start, $end])
            ->count();


        return [
            'totalCharges' => $total,
            'chartData' => [
                'labels' => $labels,
                'datasets' => array_map(function ($grp) use ($data, $labelsDisplay, $colors) {
                    return [
                        'label' => $labelsDisplay[$grp],
                        'data' => $data[$grp],
                        'backgroundColor' => $colors[$grp],
                        'borderColor' => $colors[$grp],
                    ];
                }, array_keys($labelsDisplay))
            ],
            'chartOptions' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
            ]
        ];
    }

    public function expenseCategories(Request $request)
    {
        $period = $request->period ?? 'month';
        $year = $request->year ?? now()->year;
        $userId = auth()->id();

        $month = $request->month ?? now()->month;

        $query = DB::table('categories as c')
            ->leftJoin('transactions as t', function ($join) use ($userId, $period, $year, $month, $request) {
                $join->on('c.id', '=', 't.category_id')
                    ->on('c.type', '=', 't.type');

                $join->where('t.user_id', $userId)
                    ->where('t.type', 1); // despesas

                if ($period === 'year') {
                    $join->whereRaw('YEAR(t.transaction_date) = ?', [$year]);

                } elseif ($period === 'month') {
                    $join->whereRaw('YEAR(t.transaction_date) = ? AND MONTH(t.transaction_date) = ?', [$year, $month]);

                } elseif ($period === 'week') {
                    $start = now()->startOfWeek()->toDateString();
                    $end = now()->endOfWeek()->toDateString();
                    $join->whereBetween(DB::raw('DATE(t.transaction_date)'), [$start, $end]);
                }
            })
            ->select(
                'c.id',
                'c.name',
                DB::raw('COALESCE(SUM(t.amount), 0) as total_amount')
            )
            ->where('c.type', 1) // categorias do tipo despesa
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('total_amount');

        $expenseCategories = $query->get();

        $totalExpenses = $expenseCategories->sum('total_amount');

        $colors = [
            '#ef4444',
            '#f97316',
            '#facc15',
            '#22c55e',
            '#0ea5e9',
            '#6366f1',
            '#a855f7',
            '#ec4899',
            '#14b8a6',
            '#f43f5e',
            '#ec4899',
            '#14b8a6',
            '#f43f5e'
        ];

        $chartData = [
            'labels' => $expenseCategories->pluck('name'),
            'datasets' => [
                [
                    'data' => $expenseCategories->pluck('total_amount'),
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
        ];

        return [
            'chartData' => $chartData,
            'final_expenses' => $totalExpenses,
            'chartOptions' => [
                'responsive' => true,
                'maintainAspectRatio' => false,

                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => [
                            'usePointStyle' => true,
                            'pointStyle' => 'circle',
                            'boxWidth' => 12,
                            'padding' => 16,
                        ]
                    ],
                ],
            ],
        ];
    }



    public function incomeCategories(Request $request)
    {
        $period = $request->period ?? 'month';
        $year = $request->year ?? now()->year;
        $userId = auth()->id();
        $month = $request->month ?? now()->month;

        $query = DB::table('categories as c')
            ->leftJoin('transactions as t', function ($join) use ($userId, $period, $year, $month) {
                $join->on('c.id', '=', 't.category_id')
                    ->on('c.type', '=', 't.type')
                    ->where('t.user_id', $userId)
                    ->where('t.type', 2); // receitas (type 2)

                // Filtros aplicados dentro do ON para preservar LEFT JOIN
                if ($period === 'year') {
                    $join->whereRaw('YEAR(t.transaction_date) = ?', [$year]);

                } elseif ($period === 'month') {
                    $join->whereRaw(
                        'YEAR(t.transaction_date) = ? AND MONTH(t.transaction_date) = ?',
                        [$year, $month]
                    );

                } elseif ($period === 'week') {
                    $start = now()->startOfWeek()->toDateString();
                    $end = now()->endOfWeek()->toDateString();
                    $join->whereBetween(DB::raw('DATE(t.transaction_date)'), [$start, $end]);
                }
            })
            ->select(
                'c.id',
                'c.name',
                DB::raw('COALESCE(SUM(t.amount), 0) as total_amount')
            )
            ->where('c.type', 2) // só categorias de receita
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('total_amount');

        $incomeCategories = $query->get();

        $totalRevenues = $incomeCategories->sum('total_amount');

        $colors = [
            '#ef4444',
            '#f97316',
            '#facc15',
            '#22c55e',
            '#0ea5e9',
            '#6366f1',
            '#a855f7',
            '#ec4899',
            '#14b8a6',
            '#f43f5e'
        ];

        $chartData = [
            'labels' => $incomeCategories->pluck('name'),
            'datasets' => [
                [
                    'data' => $incomeCategories->pluck('total_amount'),
                    'backgroundColor' => $colors,
                    'borderWidth' => 0,
                ],
            ],
        ];

        return [
            'chartData' => $chartData,
            'final_revenues' => $totalRevenues,
            'chartOptions' => [
                'responsive' => true,
                'maintainAspectRatio' => false,

                'plugins' => [
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => [
                            'usePointStyle' => true,
                            'pointStyle' => 'circle',
                            'boxWidth' => 12,
                            'padding' => 16,
                        ]
                    ],
                ],
            ],
        ];
    }

}
