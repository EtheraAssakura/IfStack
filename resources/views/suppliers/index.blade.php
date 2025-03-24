@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h2>Liste des Fournisseurs</h2>
                    <a href="{{ route('suppliers.create') }}" class="btn btn-primary">Nouveau Fournisseur</a>
                </div>

                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Contact</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($suppliers as $supplier)
                                <tr>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->contact_name }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->phone }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-info btn-sm">Voir</a>
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning btn-sm">Modifier</a>
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce fournisseur ?')">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection