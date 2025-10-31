<!DOCTYPE html>
<!-- tag para deixar o theme light, qualquer coisa para mudar o tema só alterar aq -->
<html data-theme="light" lang="pt-BR">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Analytics -->
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CQ70Q47T0F"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());

        gtag('config', 'G-CQ70Q47T0F');
    </script>

    @inertiaHead
</head>

<body>
    @inertia
</body>

</html>
