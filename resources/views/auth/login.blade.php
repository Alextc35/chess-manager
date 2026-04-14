@extends('layouts.app')

@section('content')
<div class="container col-md-4 mt-5">
    <h2>Iniciar sesión</h2>

    <form method="POST" action="{{ route('login.post') }}">
        @csrf

        <div class="mb-3">
            <label>Usuario o email</label>
            <input type="text" class="form-control" name="login" value="{{ old('login') }}" required>
            @error('login')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label>Contraseña</label>
            <input type="password" class="form-control" name="password" required>
        </div>

        <button class="btn btn-primary w-100">Entrar</button>
    </form>
</div>
@endsection
