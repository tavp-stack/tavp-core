{# TAVPblocks Stat Card Component #}
{# Usage: {% include 'components/stat-card.volt' with {'label': 'Revenue', 'value': '$12,345', 'change': '+12.5%'} %} #}

{% set trend = trend|default('up') %}
{% set color = color|default('blue') %}

{% set colorClasses = {
    'blue': 'bg-blue-500',
    'green': 'bg-green-500',
    'red': 'bg-red-500',
    'yellow': 'bg-yellow-500',
    'purple': 'bg-purple-500'
} %}

<div class="bg-white rounded-lg shadow p-6">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500">{{ label|default('Stat') }}</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">{{ value|default('0') }}</p>
        </div>
        {% if change is defined %}
            <div class="flex items-center">
                {% if trend == 'up' %}
                    <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                    </svg>
                {% else %}
                    <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                    </svg>
                {% endif %}
                <span class="text-sm font-medium {{ trend == 'up' ? 'text-green-600' : 'text-red-600' }} ml-1">
                    {{ change }}
                </span>
            </div>
        {% endif %}
    </div>
</div>
