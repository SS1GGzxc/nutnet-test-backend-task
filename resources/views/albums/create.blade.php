@extends('layouts.base')

@section('title', 'Добавить альбом')

@section('content')
<div class="container">
    <h1>Добавить альбом</h1>

    <div class="lastfm-search">
        <h3>Поиск в Deezer</h3>
        <div class="search-form">
            <input type="text" id="lastfm-search" placeholder="Введите название альбома...">
            <button type="button" id="search-btn">Найти</button>
        </div>
        <div id="search-results" class="search-results"></div>
    </div>

    <form method="POST" action="{{ route('albums.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Название</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label for="author">Исполнитель</label>
            <input type="text" name="author" id="author" value="{{ old('author') }}" required>
        </div>

        <div class="form-group">
            <label for="img">Обложка (URL)</label>
            <input type="text" name="img" id="img" value="{{ old('img') }}">
            <div id="img-preview"></div>
        </div>

        <div class="form-group">
            <label for="description">Описание</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Создать альбом</button>
        <a href="{{ route('albums.index') }}" class="btn">Отмена</a>
    </form>
</div>

@push('scripts')
@vite(['resources/ts/albums.ts'])
@endpush
@endsection
