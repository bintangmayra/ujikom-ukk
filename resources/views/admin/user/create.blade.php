@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Tambah User</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.user.store') }}" method="POST">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Nama <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>

        <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary me-2">Simpan</button>
            <a href="{{ route('admin.user.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
</div>
@endsection
