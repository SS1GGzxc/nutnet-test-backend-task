<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class LibreFmService
{
    private const BASEURL = "https://api.deezer.com";

    public function searchAlbums(string $query, int $limit = 10): array
    {
        $cacheKey = "deezer_albums_" . md5($query . $limit);

        return Cache::remember($cacheKey, 3600, function () use ($query, $limit) {
            try {
                $response = Http::timeout(5)->get(self::BASEURL . '/search/album', [
                    'q' => $query,
                    'limit' => $limit,
                ]);

                if (!$response->successful()) {
                    return [];
                }

                $data = $response->json();
                $albums = $data['data'] ?? [];

                return array_map(function ($album) {
                    return [
                        'name' => $album['title'] ?? '',
                        'artist' => $album['artist']['name'] ?? 'Unknown',
                        'image' => $album['cover_medium'],
                    ];
                }, is_array($albums) ? $albums : []);
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    public function getAlbumInfo(string $artist, string $album): ?array
    {
        $cacheKey = "deezer_album_info_" . md5($artist . $album);

        return Cache::remember($cacheKey, 86400, function () use ($artist, $album) {
            try {
                $searchResponse = Http::timeout(5)->get(self::BASEURL . '/search/album', [
                    'q' => $artist . ' ' . $album,
                    'limit' => 1,
                ]);

                if (!$searchResponse->successful()) {
                    return null;
                }

                $searchData = $searchResponse->json();
                $firstResult = ($searchData['data'][0] ?? null);

                if (!$firstResult) {
                    return null;
                }

                $albumId = $firstResult['id'];

                $response = Http::timeout(5)->get(self::BASEURL . '/album/' . $albumId);

                if (!$response->successful()) {
                    return null;
                }

                $albumData = $response->json();

                return [
                    'name' => $albumData['title'] ?? '',
                    'author' => $albumData['artist']['name'] ?? 'Unknown',
                    'img' => $albumData['cover_xl'] ?? $albumData['cover_big'] ?? '',
                    'description' => $albumData['label'] ?? '',
                ];
            } catch (\Exception $e) {
                return null;
            }
        });
    }
}
