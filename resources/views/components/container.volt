{# TAVPblocks Container Component #}
{# Usage: {% include 'components/container.volt' with {'size': 'lg'} %} #}

{% set size = size|default('lg') %}
{% set padding = padding|default(true) %}

{% set sizeClasses = {
    'sm': 'max-w-3xl',
    'md': 'max-w-5xl',
    'lg': 'max-w-7xl',
    'xl': 'max-w-screen-xl',
    'full': 'max-w-full'
} %}

<div class="{{ sizeClasses[size] }} mx-auto {{ padding ? 'px-4 sm:px-6 lg:px-8' : '' }}">
    {{ content|default('') }}
</div>
