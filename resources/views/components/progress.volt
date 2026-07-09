{# TAVPblocks Progress Component #}
{# Usage: {% include 'components/progress.volt' with {'value': 75, 'max': 100} %} #}

{% set value = value|default(0) %}
{% set max = max|default(100) %}
{% set color = color|default('blue') %}
{% set size = size|default('md') %}

{% set percentage = (value / max * 100)|round %}
{% set colorClasses = {
    'blue': 'bg-blue-600',
    'green': 'bg-green-600',
    'red': 'bg-red-600',
    'yellow': 'bg-yellow-500',
    'gray': 'bg-gray-600'
} %}
{% set sizeClasses = {
    'sm': 'h-1',
    'md': 'h-2',
    'lg': 'h-4'
} %}

<div class="w-full">
    {% if showLabel is defined and showLabel %}
        <div class="flex justify-between mb-1">
            <span class="text-sm font-medium text-gray-700">{{ label|default('Progress') }}</span>
            <span class="text-sm font-medium text-gray-700">{{ percentage }}%</span>
        </div>
    {% endif %}
    <div class="w-full bg-gray-200 rounded-full {{ sizeClasses[size] }}">
        <div
            class="{{ colorClasses[color] }} rounded-full {{ sizeClasses[size] }} transition-all duration-300"
            style="width: {{ percentage }}%"
        ></div>
    </div>
</div>
