@php
    $manifestPath = public_path('frontend/manifest.json');

    if (!file_exists($manifestPath)) {
        abort(503, 'Vue storefront assets are missing. Run "npm run dev" from the project root.');
    }

    $manifest = json_decode(file_get_contents($manifestPath), true);
    $entry = $manifest['index.html'] ?? null;

    if (!is_array($entry) || empty($entry['file'])) {
        abort(500, 'Vue storefront entry is missing from the Vite manifest.');
    }
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f16722">
    <title>Shop — Everyday finds</title>
    @foreach ($entry['css'] ?? [] as $stylesheet)
        <link rel="stylesheet" href="{{ asset('frontend/' . $stylesheet) }}">
    @endforeach
</head>
<body>
    <div id="app"></div>
    <script type="module" src="{{ asset('frontend/' . $entry['file']) }}"></script>
</body>
</html>
