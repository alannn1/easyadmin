<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="">
    <h1 class="text-3xl font-bold ">Halo Gaes</h1>
    <button class="btn btn-primary px-4 py-2 rounded">Click</button>
    <img src="{{ asset('assets/img_harumo.png') }}" class="z-10">
</body>
</html>