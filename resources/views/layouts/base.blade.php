<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <title>@yield("title")</title>
</head>
<body>
  <header>
    <nav>
      <ul>
        <li><a href="{{ route('albums.index') }}">Альбомы</a></li>
        @auth
          <li>
            <span>{{ Auth::user()->name }}</span>
          </li>
          <li>
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Выйти</a>
            <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
          </li>
        @endauth
        @guest
        <li><a href="{{ route('auth.login') }}">Авторизация</a></li>
        <li><a href="{{ route('auth.register') }}">Регистрация</a></li>
        @endguest
      </ul>
    </nav>
  </header>

  <main>
    @yield("content")
  </main>

  <footer>
    <div class="container">
      <p class="copyright">&copy; 2026 Название сайта. Все права защищены.</p>
      <ul>
        <li><a href="#">Политика конфиденциальности</a></li>
        <li><a href="#">Условия использования</a></li>
        <li><a href="#">Контакты</a></li>
      </ul>
    </div>
  </footer>
  @stack('scripts')
</body>
</html>
