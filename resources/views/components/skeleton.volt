{# TAVPblocks Skeleton Component #}
{# Usage: {% include 'components/skeleton.volt' with {'type': 'text', 'lines': 3} %} #}

{% set type = type|default('text') %}
{% set lines = lines|default(3) %}

{% if type == 'text' %}
    <div class="animate-pulse space-y-2">
        {% for i in 1..lines %}
            <div class="h-4 bg-gray-200 rounded {{ loop.last ? 'w-3/4' : 'w-full' }}"></div>
        {% endfor %}
    </div>
{% elseif type == 'avatar' %}
    <div class="animate-pulse flex space-x-4">
        <div class="rounded-full bg-gray-200 h-10 w-10"></div>
        <div class="flex-1 space-y-2 py-1">
            <div class="h-4 bg-gray-200 rounded w-1/4"></div>
            <div class="h-4 bg-gray-200 rounded w-1/2"></div>
        </div>
    </div>
{% elseif type == 'card' %}
    <div class="animate-pulse border border-gray-200 rounded-lg p-6">
        <div class="h-4 bg-gray-200 rounded w-1/3 mb-4"></div>
        <div class="space-y-2">
            <div class="h-4 bg-gray-200 rounded"></div>
            <div class="h-4 bg-gray-200 rounded"></div>
            <div class="h-4 bg-gray-200 rounded w-2/3"></div>
        </div>
    </div>
{% elseif type == 'image' %}
    <div class="animate-pulse bg-gray-200 rounded-lg h-48 w-full"></div>
{% elseif type == 'table' %}
    <div class="animate-pulse">
        <div class="h-10 bg-gray-200 rounded mb-2"></div>
        {% for i in 1..lines %}
            <div class="h-8 bg-gray-100 rounded mb-1"></div>
        {% endfor %}
    </div>
{% endif %}
