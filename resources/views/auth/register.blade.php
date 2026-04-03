@extends('layouts.base')

@section('title', 'Регистрация')

@section('content')
<div class="container auth-container">
    <h1>Регистрация</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('auth.register') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name">Имя</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="email">Email <span class="required">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required>
        </div>

        <div class="form-group">
            <label for="password">Пароль <span class="required">*</span></label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit">Зарегистрироваться</button>
    </form>

    <p class="auth-link">Уже есть аккаунт? <a href="{{ route('auth.login') }}">Войти</a></p>
</div>
@endsection
