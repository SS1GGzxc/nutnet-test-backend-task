@extends('layouts.base')

@section('title', 'Авторизация')

@section('content')
<div class="container auth-container">
    <h1>Авторизация</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('auth.login') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль <span class="required">*</span></label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Авторизоваться</button>
    </form>

    <p class="auth-link">Нет аккаунта? <a href="{{ route('auth.register') }}">Зарегистрироваться</a></p>
</div>
@endsection
