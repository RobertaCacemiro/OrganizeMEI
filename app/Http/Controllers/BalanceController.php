<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class BalanceController extends Controller
{
    public function monthlyBalance()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');
        $year = now()->year;

        $monthly = DB::table('transactions')
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(CASE WHEN type = 2 THEN amount ELSE 0 END) as total_receitas'),
                DB::raw('SUM(CASE WHEN type = 1 THEN amount ELSE 0 END) as total_despesas')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('transaction_date', $year)
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->orderBy(DB::raw('MONTH(transaction_date)'))
            ->get();

        $labels = [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ];

        $receitas = array_fill(0, 12, 0);
        $despesas = array_fill(0, 12, 0);
        $saldoAtual = array_fill(0, 12, 0); // inicializa o saldo

        $saldo = array_fill(0, 12, 0);

        foreach ($monthly as $item) {
            $index = $item->month - 1;
            $receitas[$index] = (float) $item->total_receitas;
            $despesas[$index] = (float) $item->total_despesas;
            $saldo[$index] = $receitas[$index] - $despesas[$index]; // saldo do mês
        }


        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Receitas',
                    'data' => $receitas,
                    'borderColor' => '#3DA700',
                    'backgroundColor' => '#3DA700',
                    'tension' => 0.2,
                    'fill' => false,
                ],
                [
                    'label' => 'Despesas',
                    'data' => $despesas,
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef4444',
                    'tension' => 0.2,
                    'fill' => false,
                ],
                [
                    'label' => 'Saldo Atual',
                    'data' => $saldo,
                    'borderColor' => '#1E40AF',
                    'backgroundColor' => '#1E40AF',
                    'tension' => 0.2,
                    'fill' => false,
                    'borderDash' => [5, 5], // linha tracejada
                ],
            ]
        ];

        $options = [
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(context) {
                        let value = context.raw;
                        return value.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
                    }",
                    ],
                ],
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'color' => '#000000',
                        'backgroundColor' => '#000000',
                        'boxWidth' => 50,
                        'textAlign' => 'left'
                    ],
                ],
            ],
        ];

        return [
            'chartData' => $chartData,
            'chartOptions' => $options
        ];
    }


    public function monthlyStatusChart()
    {
        $userId = auth()->id();
        $meiId = session('mei_id');
        $year = now()->year;

        // Pega total de charges por mês e status
        $monthly = DB::table('charges')
            ->select(
                DB::raw('MONTH(due_date) as month'),
                'status',
                DB::raw('COUNT(*) as total')
            )
            ->where('user_id', $userId)
            ->where('mei_id', $meiId)
            ->whereYear('due_date', $year)
            ->groupBy(DB::raw('MONTH(due_date)'), 'status')
            ->orderBy(DB::raw('MONTH(due_date)'))
            ->get();

        // Inicializa arrays de 12 meses para cada status
        $statuses = [
            1 => 'Pendente Envio',
            2 => 'Pendente Pagamento',
            3 => 'Pago',
            4 => 'Vencido',
        ];

        $data = [];
        foreach ($statuses as $key => $label) {
            $data[$key] = array_fill(0, 12, 0); // 12 meses
        }

        // Preenche os dados
        foreach ($monthly as $item) {
            $index = $item->month - 1;
            $data[$item->status][$index] = $item->total;
        }

        // Labels dos meses
        $labels = [
            'Janeiro',
            'Fevereiro',
            'Março',
            'Abril',
            'Maio',
            'Junho',
            'Julho',
            'Agosto',
            'Setembro',
            'Outubro',
            'Novembro',
            'Dezembro'
        ];

        // Monta datasets
        $datasets = [];
        $colors = [
            1 => '#F59E0B', // amarelo → Pendente Envio
            2 => '#3B82F6', // azul → Pendente Pagamento
            3 => '#10B981', // verde → Pago
            4 => '#EF4444', // vermelho → Vencido
        ];

        foreach ($statuses as $key => $label) {
            $datasets[] = [
                'label' => $label,
                'data' => $data[$key],
                'backgroundColor' => $colors[$key],
            ];
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => $datasets,
        ];

        $chartOptions = [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];

        return [
            'chargesChartData' => $chartData,
            'chargesChartOptions' => $chartOptions,
        ];
    }

}
