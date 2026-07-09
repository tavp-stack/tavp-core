{# TAVPblocks Spinner/Loading Component #}
{# Usage: {% include 'components/spinner.volt' with {'size': 'md'} %} #}

{% set size = size|default('md') %}
{% set color = color|default('blue') %}

{% set sizeClasses = {
    'sm': 'h-4 w-4',
    'md': 'h-8 w-8',
    'lg': 'h-12 w-12',
    'xl': 'h-16 w-16'
} %}

{% set colorClasses = {
    'blue': 'border-blue-600',
    'gray': 'border-gray-600',
    'green': 'border-green-600',
    'red': 'border-red-600',
    'white': 'border-white'
} %}

<div class="inline-block animate-spin rounded-full {{ sizeClasses[size] }} border-4 border-solid {{ colorClasses[color] }} border-r-transparent" role="status">
    <span class="sr-only">Loading...</span>
</div>
