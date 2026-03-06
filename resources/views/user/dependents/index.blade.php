@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
        <h1 class="page-title fw-semibold fs-18 mb-0">Senarai Tanggungan</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card custom-card">
        <div class="card-body">
            <a href="{{ route('user.dependents.create') }}" class="btn btn-primary btn-sm mb-3">
                Add Tanggungan
            </a>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>No. KP</th>
                            <th>Pasangan</th>
                            <th>Pertalian</th>
                            <th>No. Tel</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dependents as $dependent)
                            <tr>
                                <td>{{ $dependent->name }}</td>
                                <td>{{ $dependent->no_kp }}</td>
                                <td>{{ ucfirst($dependent->pasangan) }}</td>
                                <td>{{ ucfirst($dependent->pertalian) }}</td>
                                <td>{{ $dependent->no_tel }}</td>
                                <td>
                                    <a href="{{ route('user.dependents.show', $dependent->id) }}" class="btn btn-info btn-sm">View</a>
                                    <a href="{{ route('user.dependents.edit', $dependent->id) }}" class="btn btn-warning btn-sm">Edit</a>

                                    <form action="{{ route('user.dependents.destroy', $dependent->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Adakah anda pasti mahu padam data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">Tiada tanggungan direkodkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection