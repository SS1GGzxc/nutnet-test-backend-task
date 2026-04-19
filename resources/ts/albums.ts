interface LastFmImage {
  '#text': string;
  size: 'small' | 'medium' | 'large' | 'extralarge';
}

interface LastFmAlbumData {
  name?: string;
  author?: string;
  description?: string;
  img?: string;
}

interface SearchResultAlbum {
  artist: string;
  name: string;
  image?: LastFmImage[];
}

document.addEventListener('DOMContentLoaded', function () {
  const searchInput = document.getElementById('lastfm-search') as HTMLInputElement | null;
  const searchBtn = document.getElementById('search-btn') as HTMLButtonElement | null;
  const resultsContainer = document.getElementById('search-results') as HTMLDivElement | null;
  const nameInput = document.getElementById('name') as HTMLInputElement | null;
  const authorInput = document.getElementById('author') as HTMLInputElement | null;
  const imgInput = document.getElementById('img') as HTMLInputElement | null;
  const descriptionInput = document.getElementById('description') as HTMLTextAreaElement | null;
  const imgPreview = document.getElementById('img-preview') as HTMLDivElement | null;

  if (!searchInput || !searchBtn || !resultsContainer || !nameInput || !authorInput || !imgInput || !descriptionInput || !imgPreview) {
    return;
  }

  /**
   * Build a URL with query parameters using the URL/URLSearchParams API
   * instead of manual string concatenation with encodeURIComponent.
   */
  function buildUrl(path: string, params: Record<string, string>): string {
    const url = new URL(path, window.location.origin);
    url.search = new URLSearchParams(params).toString();
    return url.toString();
  }

  function fillFromLastFm(artist: string, album: string): void {
    const url = buildUrl('/albums/fetch-librefm', { artist, album });

    fetch(url)
      .then(response => response.json())
      .then((data: LastFmAlbumData) => {
        if (data.name) nameInput!.value = data.name;
        if (data.author) authorInput!.value = data.author;
        if (data.description) descriptionInput!.value = data.description.replace(/<[^>]*>/g, '');
        if (data.img) {
          imgInput!.value = data.img;
          imgPreview!.innerHTML = '';
          const img = document.createElement('img');
          img.src = data.img;
          img.style.maxWidth = '200px';
          img.style.marginTop = '10px';
          imgPreview!.appendChild(img);
        }
      })
      .catch(err => console.error(err));
  }

  searchBtn.addEventListener('click', function () {
    const query = searchInput.value;
    if (query.length < 2) return;

    resultsContainer.innerHTML = '<p>Загрузка...</p>';

    const url = buildUrl('/albums/search', { q: query });

    fetch(url)
      .then(response => response.json())
      .then((data: SearchResultAlbum[]) => {
        if (data.length === 0) {
          resultsContainer.innerHTML = '<p>Ничего не найдено.</p>';
          return;
        }

        resultsContainer.innerHTML = '';
        const grid = document.createElement('div');
        grid.className = 'results-grid';

        data.forEach((album) => {
          const item = document.createElement('div');
          item.className = 'result-item';
          item.dataset.artist = album.artist;
          item.dataset.album = album.name;

          const imageUrl = album.image?.[2]?.['#text'];
          if (imageUrl) {
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = '';
            item.appendChild(img);
          }

          const info = document.createElement('div');

          const strong = document.createElement('strong');
          strong.textContent = album.name;
          info.appendChild(strong);

          const span = document.createElement('span');
          span.textContent = album.artist;
          info.appendChild(span);

          item.appendChild(info);

          item.addEventListener('click', () => {
            fillFromLastFm(album.artist, album.name);
          });

          grid.appendChild(item);
        });

        resultsContainer.appendChild(grid);
      })
      .catch(() => {
        resultsContainer.innerHTML = '<p>Ошибка поиска.</p>';
      });
  });
});
