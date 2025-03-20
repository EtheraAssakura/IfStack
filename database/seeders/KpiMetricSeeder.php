<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KpiMetric;

class KpiMetricSeeder extends Seeder
{
    public function run()
    {
        $metrics = [
            [
                'name' => 'Taux de service',
                'code' => 'SERVICE_RATE',
                'type' => 'percentage',
                'target_value' => 95,
                'current_value' => 0,
                'unit' => '%'
            ],
            [
                'name' => 'Stock moyen',
                'code' => 'AVG_STOCK',
                'type' => 'quantity',
                'target_value' => 1000,
                'current_value' => 0,
                'unit' => 'unités'
            ],
            [
                'name' => 'Taux de rotation des stocks',
                'code' => 'STOCK_TURNOVER',
                'type' => 'ratio',
                'target_value' => 4,
                'current_value' => 0,
                'unit' => 'fois/an'
            ],
            [
                'name' => 'Délai moyen de livraison',
                'code' => 'AVG_DELIVERY_TIME',
                'type' => 'duration',
                'target_value' => 3,
                'current_value' => 0,
                'unit' => 'jours'
            ],
            [
                'name' => 'Taux de commandes en retard',
                'code' => 'LATE_ORDERS',
                'type' => 'percentage',
                'target_value' => 5,
                'current_value' => 0,
                'unit' => '%'
            ]
        ];

        foreach ($metrics as $metric) {
            KpiMetric::create($metric);
        }
    }
}
