@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Orders</h2>
                    <div>
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">New Order</a>
                        <a href="{{ route('orders.suggest') }}" class="btn btn-info">Suggest Orders</a>
                    </div>
                </div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order Number</th>
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->supplier->name }}</td>
                                <td>{{ $order->order_date->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-{{ $order->status === 'pending' ? 'warning' : ($order->status === 'validated' ? 'success' : 'danger') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info">View</a>
                                    @if($order->status === 'pending')
                                    <a href="{{ route('orders.export', $order) }}" class="btn btn-sm btn-success">Export</a>
                                    <form action="{{ route('orders.validate', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-primary">Validate</button>
                                    </form>
                                    <form action="{{ route('orders.cancel', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection