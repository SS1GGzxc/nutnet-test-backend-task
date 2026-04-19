@extends('layouts.base')

@section('title', 'Редактировать альбом')

@section('content')
<div class="container">
    <h1>Редактировать альбом</h1>

    <div class="lastfm-search">
        <h3>Поиск в Deezer</h3>
        <div class="search-form">
            <input type="text" id="lastfm-search" placeholder="Введите название альбома...">
            <button type="button" id="search-btn">Найти</button>
        </div>
        <div id="search-results" class="search-results"></div>
    </div>

    <form method="POST" action="{{ route('albums.update', $album) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Название</label>
            <input type="text" name="name" id="name" value="{{ old('name', $album->name) }}" required>
        </div>

        <div class="form-group">
            <label for="author">Исполнитель</label>
            <input type="text" name="author" id="author" value="{{ old('author', $album->author) }}" required>
        </div>

        <div class="form-group">
            <label for="img">Обложка (URL)</label>
            <input type="text" name="img" id="img" value="{{ old('img', $album->img) }}">
            @if($album->img)
                <div id="img-preview"><img src="{{ $album->img }}" style="max-width: 200px; margin-top: 10px;"></div>
            @endif
        </div>

        <div class="form-group">
            <label for="description">Описание</label>
            <textarea name="description" id="description">{{ old('description', $album->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="{{ route('albums.index') }}" class="btn">Отмена</a>
    </form>

    @if($logs->isNotEmpty())
    <div class="logs-section">
        <h3>История изменений</h3>
        <table class="logs-table">
            <thead>
                <tr>
                    <th>Дата</th>
                    <th>Пользователь</th>
                    <th>Действие</th>
                    <th>Изменения</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $actionLabels = [
                        'created' => 'Создание',
                        'updated' => 'Редактирование',
                        'deleted' => 'Удаление',
                    ];
                @endphp
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $log->user->name ?? '—' }}</td>
                    <td>{{ $actionLabels[$log->action] ?? $log->action }}</td>
                    <td>
                        <x-album-log-changes :log="$log" />
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@push('scripts')
@vite(['resources/ts/albums.ts'])
@endpush
@endsection
