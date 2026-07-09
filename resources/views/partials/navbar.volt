{# Reusable navbar partial. Usage: {% partial "partials/navbar" with ['active': 'home'] %} #}
<nav class="flex gap-4">
    <a href="/" class="{% if active == 'home' %}font-bold{% endif %}">Home</a>
    <a href="/about" class="{% if active == 'about' %}font-bold{% endif %}">About</a>
</nav>
