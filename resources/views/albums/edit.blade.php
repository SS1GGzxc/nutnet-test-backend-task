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
                @foreach($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d.m.Y H:i') }}</td>
                    <td>{{ $log->user->name ?? '—' }}</td>
                    <td>
                        @if($log->action === 'created') Создание
                        @elseif($log->action === 'updated') Редактирование
                        @elseif($log->action === 'deleted') Удаление
                        @endif
                    </td>
                    <td>
                        @if($log->action === 'updated' && $log->old_values && $log->new_values)
                            @foreach($log->new_values as $field => $newValue)
                                @if(isset($log->old_values[$field]) && $log->old_values[$field] !== $newValue)
                                    <div class="log-change">
                                        <strong>{{ $field }}:</strong>
                                        <span class="old">{{ $log->old_values[$field] ?? '—' }}</span>
                                        →
                                        <span class="new">{{ $newValue }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @elseif($log->action === 'created')
                            <em>Новая запись</em>
                        @elseif($log->action === 'deleted')
                            <em>Запись удалена</em>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('lastfm-search');
    const searchBtn = document.getElementById('search-btn');
    const resultsContainer = document.getElementById('search-results');
    const nameInput = document.getElementById('name');
    const authorInput = document.getElementById('author');
    const imgInput = document.getElementById('img');
    const descriptionInput = document.getElementById('description');
    const imgPreview = document.getElementById('img-preview');

    function fillFromLastFm(artist, album) {
        fetch(`{{ route('albums.fetch-librefm') }}?artist=${encodeURIComponent(artist)}&album=${encodeURIComponent(album)}`)
            .then(response => response.json())
            .then(data => {
                if (data.name) nameInput.value = data.name;
                if (data.author) authorInput.value = data.author;
                if (data.description) descriptionInput.value = data.description.replace(/<[^>]*>/g, '');
                if (data.img) {
                    imgInput.value = data.img;
                    imgPreview.innerHTML = `<img src="${data.img}" style="max-width: 200px; margin-top: 10px;">`;
                }
            })
            .catch(err => console.error(err));
    }

    searchBtn.addEventListener('click', function() {
        const query = searchInput.value;
        if (query.length < 2) return;

        resultsContainer.innerHTML = '<p>Загрузка...</p>';

        fetch(`{{ route('albums.search') }}?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    resultsContainer.innerHTML = '<p>Ничего не найдено.</p>';
                    return;
                }

                resultsContainer.innerHTML = '<div class="results-grid">' +
                    data.map(album => `
                        <div class="result-item" data-artist="${album.artist}" data-album="${album.name}">
                            ${album.image && album.image[2]['#text'] ? `<img src="${album.image[2]['#text']}" alt="">` : ''}
                            <div>
                                <strong>${album.name}</strong>
                                <span>${album.artist}</span>
                            </div>
                        </div>
                    `).join('') +
                    '</div>';

                document.querySelectorAll('.result-item').forEach(item => {
                    item.addEventListener('click', function() {
                        fillFromLastFm(this.dataset.artist, this.dataset.album);
                    });
                });
            })
            .catch(err => {
                resultsContainer.innerHTML = '<p>Ошибка поиска.</p>';
            });
    });
});
</script>
@endpush
@endsection
