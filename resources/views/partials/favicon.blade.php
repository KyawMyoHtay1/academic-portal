@php
    $faviconPath = public_path('favicon.svg');
    $faviconVersion = file_exists($faviconPath) ? filemtime($faviconPath) : time();
    $faviconUrl = asset('favicon.svg') . '?v=' . $faviconVersion;
@endphp
<meta name="application-name" content="{{ config('app.name', 'University Academic Portal') }}">
<meta name="theme-color" content="#0b1f3a">
<meta name="msapplication-TileColor" content="#0b1f3a">
<link rel="icon" href="{{ $faviconUrl }}" sizes="any" type="image/svg+xml">
<link rel="shortcut icon" href="{{ $faviconUrl }}" type="image/svg+xml">
