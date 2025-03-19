@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Order Details</h2>
                    <div>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                        @if($order->status === 'pending')
                        <a href="{{ route('orders.export', $order) }}" class="btn btn-success">Export Excel</a>
                        <form action="{{ route('orders.validate', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary">Validate</button>
                        </form>
                        <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-danger">Cancel</button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Order Information</h4>
                            <table class="table">
                                <tr>
                                    <th>Order Number:</th>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'validated' ? 'success' : 'danger') }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Order Date:</th>
                                    <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                </tr>
                                <tr>
                                    <th>Expected Delivery Date:</th>
                                    <td>{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('d/m/Y') : 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <th>Created By:</th>
                                    <td>{{ $order->user->name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Supplier Information</h4>
                            <table class="table">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $order->supplier->name }}</td>
                                </tr>
                                <tr>
                                    <th>Catalog URL:</th>
                                    <td>
                                        @if($order->supplier->catalog_url)
                                        <a href="{{ $order->supplier->catalog_url }}" target="_blank">View Catalog</a>
                                        @else
                                        Not available
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h4>Order Items</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Reference</th>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->supply->reference }}</td>
                                            <td>{{ $item->supply->name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->unit_price, 2) }} €</td>
                                            <td>{{ number_format($item->quantity * $item->unit_price, 2) }} €</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end">Total HT:</th>
                                            <th>{{ number_format($order->items->sum(function($item) { return $item->quantity * $item->unit_price; }), 2) }} €</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    @if($order->deliveries->isNotEmpty())
                    <div class="card">
                        <div class="card-header">
                            <h4>Deliveries</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Items</th>
                                            <th>Created By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->deliveries as $delivery)
                                        <tr>
                                            <td>{{ $delivery->delivery_date->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $delivery->status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($delivery->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <ul class="list-unstyled mb-0">
                                                    @foreach($delivery->items as $item)
                                                    <li>{{ $item->supply->name }} ({{ $item->quantity }})</li>
                                                    @endforeach
                                                </ul>
                                            </td>
                                            <td>{{ $delivery->user->name }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection