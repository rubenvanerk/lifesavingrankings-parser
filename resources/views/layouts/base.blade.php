<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Competition Parser</title>
    <link rel="stylesheet" href="https://fonts.xz.style/serve/inter.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@exampledev/new.css@1/new.min.css">
    <link rel="stylesheet" href="{{url('public/css/main.css')}}">

</head>
<body data-theme="light">
<header>
    <h1>Competition Parser</h1>
    <nav>
        <a href="{{ route('upload') }}">
            Import
        </a>
        /
        <a href="{{ route('browse') }}">
            Browse
        </a>
    </nav>
</header>
@yield('content')
</body>
</html>
