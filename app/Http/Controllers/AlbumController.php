<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\AlbumLog;
use App\Services\LibreFmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlbumController extends Controller
{
    protected LibreFmService $libreFm;

    public function __construct(LibreFmService $libreFm)
    {
        $this->libreFm = $libreFm;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);
        $albums = Album::latest()->paginate($perPage)->appends($request->query());

        return view('albums.index', compact('albums'));
    }

    public function create()
    {
        return view('albums.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'author' => 'required|max:255',
            'description' => 'nullable',
            'img' => 'nullable|max:255',
        ]);

        $album = Album::create($validated);

        AlbumLog::create([
            'album_id' => $album->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'old_values' => null,
            'new_values' => $validated,
        ]);

        return redirect()->route('albums.index')->with('success', 'Альбом успешно создан.');
    }

    public function edit(Album $album)
    {
        $logs = $album->logs()->with('user')->latest()->get();

        return view('albums.edit', compact('album', 'logs'));
    }

    public function update(Request $request, Album $album)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'author' => 'required|max:255',
            'description' => 'nullable',
            'img' => 'nullable|max:255',
        ]);

        $oldValues = $album->only(['name', 'author', 'description', 'img']);

        $album->update($validated);

        AlbumLog::create([
            'album_id' => $album->id,
            'user_id' => Auth::id(),
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $validated,
        ]);

        return redirect()->route('albums.index')->with('success', 'Альбом успешно обновлён.');
    }

    public function destroy(Album $album)
    {
        $oldValues = $album->only(['name', 'author', 'description', 'img']);

        AlbumLog::create([
            'album_id' => $album->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'old_values' => $oldValues,
            'new_values' => null,
        ]);

        $album->delete();

        return redirect()->route('albums.index')->with('success', 'Альбом удалён.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $results = $this->libreFm->searchAlbums($query, 10);

        return response()->json($results);
    }

    public function fetchFromLastFm(Request $request)
    {
        $artist = $request->get('artist', '');
        $albumName = $request->get('album', '');

        if (empty($artist) || empty($albumName)) {
            return response()->json(['error' => 'Artist and album name required'], 422);
        }

        $info = $this->libreFm->getAlbumInfo($artist, $albumName);

        if (!$info) {
            return response()->json(['error' => 'Album not found'], 404);
        }

        return response()->json([
            'name' => $info['name'] ?? '',
            'author' => $info['author'] ?? '',
            'description' => $info['description'] ?? '',
            'img' => $info['img'] ?? '',
        ]);
    }
}
