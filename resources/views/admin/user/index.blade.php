@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Daftar User</h2>
    <a href="{{ route('admin.user.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus me-2"></i> Tambah User
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table align-middle table-hover">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $index => $user)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge bg-{{ $user->role == 'admin' ? 'success' : 'info' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.user.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    @if($users->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada user.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
