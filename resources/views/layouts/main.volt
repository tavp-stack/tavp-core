<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}TAVP App{% endblock %}</title>
    <link rel="stylesheet" href="{{ asset('build/app.css') }}">
    <script defer src="{{ asset('build/app.js') }}"></script>
</head>
<body class="bg-gray-50 text-gray-900">
    <header class="border-b">
        <nav class="max-w-5xl mx-auto p-4 flex gap-4">
            <a href="/" class="font-bold">TAVP</a>
            <a href="/about">About</a>
            <a href="/contact">Contact</a>
        </nav>
    </header>

    <main class="max-w-5xl mx-auto p-4">
        {% block content %}{% endblock %}
    </main>

    <footer class="border-t mt-8 p-4 text-center text-sm text-gray-500">
        Built with TAVP — Tailwind + Alpine + Volt + Phalcon.
    </footer>
</body>
</html>
