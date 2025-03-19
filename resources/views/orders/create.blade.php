@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Create New Order</h2>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                </div>

                <div class="card-body">
                    <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select name="supplier_id" id="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                    <option value="">Select a supplier</option>
                                    @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ old('supplier_id', request('supplier_id')) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="order_date" class="form-label">Order Date</label>
                                <input type="date" name="order_date" id="order_date" class="form-control @error('order_date') is-invalid @enderror"
                                    value="{{ old('order_date', date('Y-m-d')) }}" required>
                                @error('order_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                                <input type="date" name="expected_delivery_date" id="expected_delivery_date"
                                    class="form-control @error('expected_delivery_date') is-invalid @enderror"
                                    value="{{ old('expected_delivery_date') }}">
                                @error('expected_delivery_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <h3 class="card-title">Order Items</h3>
                            </div>
                            <div class="card-body">
                                <div id="items-container">
                                    <div class="row mb-3 item-row">
                                        <div class="col-md-4">
                                            <label class="form-label">Supply</label>
                                            <select name="items[0][supply_id]" class="form-select supply-select" required>
                                                <option value="">Select a supply</option>
                                                @foreach($supplies as $supply)
                                                <option value="{{ $supply->id }}"
                                                    data-price="{{ $supply->suppliers->first()->pivot->unit_price ?? 0 }}"
                                                    {{ old('items.0.supply_id', request('supply_id')) == $supply->id ? 'selected' : '' }}>
                                                    {{ $supply->reference }} - {{ $supply->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Quantity</label>
                                            <input type="number" name="items[0][quantity]" class="form-control quantity-input"
                                                value="{{ old('items.0.quantity', request('quantity', 1)) }}" min="1" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Unit Price</label>
                                            <input type="number" name="items[0][unit_price]" class="form-control price-input"
                                                step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Total</label>
                                            <input type="text" class="form-control total-input" readonly>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">&nbsp;</label>
                                            <button type="button" class="btn btn-danger d-block remove-item">Remove</button>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-success" id="add-item">Add Item</button>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Create Order</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('items-container');
        const addButton = document.getElementById('add-item');
        let itemCount = 1;

        // Function to update total
        function updateTotal(row) {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            row.querySelector('.total-input').value = (quantity * price).toFixed(2);
        }

        // Function to update price when supply is selected
        function updatePrice(select) {
            const row = select.closest('.item-row');
            const price = select.options[select.selectedIndex].dataset.price;
            row.querySelector('.price-input').value = price;
            updateTotal(row);
        }

        // Add new item row
        addButton.addEventListener('click', function() {
            const template = container.querySelector('.item-row').cloneNode(true);

            // Update names and clear values
            template.querySelectorAll('select, input').forEach(input => {
                input.name = input.name.replace('[0]', `[${itemCount}]`);
                if (input.type !== 'hidden') input.value = '';
            });

            // Add event listeners
            template.querySelector('.supply-select').addEventListener('change', function() {
                updatePrice(this);
            });

            template.querySelector('.quantity-input').addEventListener('input', function() {
                updateTotal(this.closest('.item-row'));
            });

            template.querySelector('.price-input').addEventListener('input', function() {
                updateTotal(this.closest('.item-row'));
            });

            template.querySelector('.remove-item').addEventListener('click', function() {
                this.closest('.item-row').remove();
            });

            container.appendChild(template);
            itemCount++;
        });

        // Add event listeners to initial row
        const initialRow = container.querySelector('.item-row');
        initialRow.querySelector('.supply-select').addEventListener('change', function() {
            updatePrice(this);
        });

        initialRow.querySelector('.quantity-input').addEventListener('input', function() {
            updateTotal(this.closest('.item-row'));
        });

        initialRow.querySelector('.price-input').addEventListener('input', function() {
            updateTotal(this.closest('.item-row'));
        });

        initialRow.querySelector('.remove-item').addEventListener('click', function() {
            if (container.querySelectorAll('.item-row').length > 1) {
                this.closest('.item-row').remove();
            }
        });

        // Set initial price if supply is pre-selected
        const initialSelect = initialRow.querySelector('.supply-select');
        if (initialSelect.value) {
            updatePrice(initialSelect);
        }
    });
</script>
@endpush
@endsection