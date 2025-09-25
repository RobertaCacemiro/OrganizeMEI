<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Categories;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $categories = [
            // Receitas
            ['name' => 'Vendas de Produtos', 'type' => 2],
            ['name' => 'Prestação de Serviços', 'type' => 2],
            ['name' => 'Outras Receitas', 'type' => 2],

            // Despesas - Custos Fixos
            ['name' => 'Aluguel', 'type' => 1],
            ['name' => 'Energia elétrica', 'type' => 1],
            ['name' => 'Água', 'type' => 1],
            ['name' => 'Internet/Telefone', 'type' => 1],
            ['name' => 'Contabilidade', 'type' => 1],
            ['name' => 'DAS/Impostos', 'type' => 1],

            // Despesas - Custos Variáveis
            ['name' => 'Matéria-prima / Insumos', 'type' => 1],
            ['name' => 'Transporte / Combustível', 'type' => 1],
            ['name' => 'Manutenção de equipamentos', 'type' => 1],
            ['name' => 'Taxas de banco / maquininhas', 'type' => 1],
            ['name' => 'Marketing / Divulgação', 'type' => 1],
            ['name' => 'Embalagens / Material de escritório', 'type' => 1],

            // Despesas - Pessoais
            ['name' => 'Pró-labore (retirada do dono)', 'type' => 1],
            ['name' => 'Outros gastos pessoais relacionados ao negócio', 'type' => 1],
        ];

        foreach ($categories as $category) {
            Categories::create($category);
        }
    }
}
