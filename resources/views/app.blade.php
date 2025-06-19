<!DOCTYPE html>
<html lang="{{ str_replace('-', '_', app()->getLocale()) }}" prefix="og: http://ogp.me/ns#" style="background: rgb(17, 24, 39)">
<head>
    <!-- Metadata -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="author" content="coalaura">
    <meta name="description" content="OP-Framework - Admin Panel">

    <!-- Open Graph Protocol -->
    <meta property="og:title" content="OP-FW - Admin Panel">
    <meta property="og:type" content="admin.fivem">
    <meta property="og:image" content="{{ asset('favicon.jpg') }}">

    <!-- Page title -->
    <title>OP-FW - Admin @yield('title')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.jpg') }}?v=1624011750066">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,500;1,600;1,700;1,800;1,900&family=Ubuntu+Mono&display=swap" rel="stylesheet">

    @vite(['resources/css/app.pcss', 'resources/js/app.js'])

    <!-- Screen Reader styles so we don't have weird flickering -->
    <style>.sr-only{border:0;clip:rect(0,0,0,0);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px}</style>

    <!-- Extra header -->
    {!! extraHeader() !!}
</head>

<body class="h-full font-sans text-black antialiased">
    @inertia

    <script>
        // meow :)
        (function () {
        	const div = document.createElement("div");

        	const height = window.innerHeight - 24;
        	const width = window.innerWidth - 34;

        	div.style.top = (Math.random() * height) + "px";
        	div.style.left = (Math.random() * width) + "px";

        	div.style.opacity = "0.03";
        	div.style.color = "white";
        	div.style.position = "absolute";
        	div.style.fontSize = "14px";
        	div.style.fontFamily = "Montserrat, sans-serif";
        	div.style.pointerEvents = "none";

        	div.innerText = "meow";

        	document.body.appendChild(div);
        })();
    </script>

    <script>
        function quit() {
            if (window.self === window.top) {
                return;
            }

            window.parent.postMessage({
                close: true
            }, "*");
        };

        (function () {
            if (window.self === window.top) {
                return;
            }

            window.addEventListener("keyup", e => {
                if (e.key !== "Escape") {
                    return;
                }

                quit();
            });
        })();
    </script>
</body>
