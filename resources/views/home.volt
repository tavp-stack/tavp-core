{% extends 'layouts.app' %}

{% block content %}
<div class="px-4 py-6 sm:px-0">
    <div class="border-4 border-dashed border-gray-200 rounded-lg p-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900">Welcome to TAVP</h1>
            <p class="mt-2 text-gray-600">Tailwind + Alpine + Volt + Phalcon</p>
            <div class="mt-6">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
{% endblock %}
