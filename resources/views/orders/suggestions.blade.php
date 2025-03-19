@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Suggested Orders</h2>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                </div>

                <div class="card-body">
                    @if($suggestions->isEmpty())
                    <div class="alert alert-info">
                        No supplies need to be ordered at the moment.
                    </div>
                    @else
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Current Stock</th>
                                    <th>Threshold</th>
                                    <th>Needed</th>
                                    <th>Supplier</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suggestions as $suggestion)
                                <tr>
                                    <td>{{ $suggestion['supply']->reference }}</td>
                                    <td>{{ $suggestion['supply']->name }}</td>
                                    <td>{{ $suggestion['stock']->location->name }}</td>
                                    <td>{{ $suggestion['stock']->estimated_quantity }}</td>
                                    <td>{{ COALESCE($suggestion['stock']->local_alert_threshold, $suggestion['supply']->alert_threshold) }}</td>
                                    <td>{{ $suggestion['needed_quantity'] }}</td>
                                    <td>{{ $suggestion['supplier'] ? $suggestion['supplier']->name : 'No supplier' }}</td>
                                    <td>
                                        @if($suggestion['supplier'])
                                        <a href="{{ route('orders.create', ['supplier_id' => $suggestion['supplier']->id, 'supply_id' => $suggestion['supply']->id, 'quantity' => $suggestion['needed_quantity']]) }}"
                                            class="btn btn-sm btn-primary">
                                            Create Order
                                        </a>
                                        @else
                                        <span class="text-muted">No supplier assigned</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection