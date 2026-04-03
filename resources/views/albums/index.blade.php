@extends('layouts.base')

@section('title', 'Альбомы')

@section('content')
<div class="container">
    <h1>Альбомы</h1>
    
    @auth
        <div class="actions">
            <a href="{{ route('albums.create') }}" class="btn btn-primary">Добавить альбом</a>
        </div>
    @endauth

    <div class="per-page-selector">
        <form method="GET" action="{{ route('albums.index') }}">
            <label>Показывать по:
                <select name="per_page" onchange="this.form.submit()">
                    <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12</option>
                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                </select>
            </label>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($albums->isEmpty())
        <p>Альбомы пока не добавлены.</p>
    @else
        <div class="albums-grid">
            @foreach($albums as $album)
                <div class="album-card">
                    @if($album->img)
                        <img src="{{ $album->img }}" alt="{{ $album->name }}">
                    @endif
                    <h3>{{ $album->name }}</h3>
                    <p class="author">{{ $album->author }}</p>
                    @if($album->description)
                        <p class="description">{{ Str::limit($album->description, 100) }}</p>
                    @endif
                    @auth
                        <div class="album-actions">
                            <a href="{{ route('albums.edit', $album) }}" class="btn btn-sm">Редактировать</a>
                            <form action="{{ route('albums.destroy', $album) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить альбом?')">Удалить</button>
                            </form>
                        </div>
                    @endauth
                </div>
            @endforeach
        </div>

        <div class="pagination">
            {{ $albums->links() }}
        </div>
    @endif
</div>
@endsection