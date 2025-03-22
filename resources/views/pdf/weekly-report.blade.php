<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Rapport hebdomadaire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .section {
            margin-bottom: 20px;
        }

        .section-title {
            background-color: #f3f4f6;
            padding: 10px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f3f4f6;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .stat-item {
            background-color: #f3f4f6;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Rapport hebdomadaire</h1>
        <p>Généré le {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h2 class="section-title">Statistiques générales</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <strong>Total des fournitures :</strong> {{ $stats['total_supplies'] }}
            </div>
            <div class="stat-item">
                <strong>Commandes en attente :</strong> {{ $stats['pending_orders'] }}
            </div>
            <div class="stat-item">
                <strong>Livraisons prévues :</strong> {{ $stats['planned_deliveries'] }}
            </div>
            <div class="stat-item">
                <strong>Alertes non traitées :</strong> {{ $stats['unhandled_alerts'] }}
            </div>
            <div class="stat-item">
                <strong>Demandes en attente :</strong> {{ $stats['pending_requests'] }}
            </div>
        </div>
    </div>

    <div class="section">
        <h2 class="section-title">Alertes de stock</h2>
        <table>
            <thead>
                <tr>
                    <th>Fourniture</th>
                    <th>Référence</th>
                    <th>Emplacement</th>
                    <th>Quantité estimée</th>
                    <th>Seuil d'alerte</th>
                </tr>
            </thead>
            <tbody>
                @foreach($alertStocks as $stock)
                <tr>
                    <td>{{ $stock->supply->name }}</td>
                    <td>{{ $stock->supply->reference }}</td>
                    <td>{{ $stock->location->name }} ({{ $stock->location->site->name }})</td>
                    <td>{{ $stock->estimated_quantity }}</td>
                    <td>{{ $stock->local_alert_threshold }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Top 5 des fournitures les plus commandées</h2>
        <table>
            <thead>
                <tr>
                    <th>Fourniture</th>
                    <th>Référence</th>
                    <th>Nombre de commandes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topSupplies as $supply)
                <tr>
                    <td>{{ $supply->name }}</td>
                    <td>{{ $supply->reference }}</td>
                    <td>{{ $supply->orders_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Livraisons prévues pour la semaine</h2>
        <table>
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Date prévue</th>
                    <th>Statut</th>
                    <th>Emplacement</th>
                </tr>
            </thead>
            <tbody>
                @foreach($weekDeliveries as $delivery)
                <tr>
                    <td>{{ $delivery->order->reference }}</td>
                    <td>{{ $delivery->expected_date->format('d/m/Y') }}</td>
                    <td>{{ $delivery->status }}</td>
                    <td>{{ $delivery->items->first()->location->name }} ({{ $delivery->items->first()->location->site->name }})</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2 class="section-title">Commandes par mois</h2>
        <table>
            <thead>
                <tr>
                    <th>Mois</th>
                    <th>Nombre de commandes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ordersByMonth as $order)
                <tr>
                    <td>{{ Carbon\Carbon::parse($order->month)->format('F Y') }}</td>
                    <td>{{ $order->total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>