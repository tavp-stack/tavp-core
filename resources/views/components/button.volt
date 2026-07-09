{# TAVPblocks Button Component #}
{# Usage: {% include 'components/button.volt' with {'text': 'Click Me', 'variant': 'primary', 'size': 'md'} %} #}

{% set variant = variant|default('primary') %}
{% set size = size|default('md') %}
{% set disabled = disabled|default(false) %}
{% set type = type|default('button') %}

{% set baseClasses = 'inline-flex items-center justify-center font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2' %}

{% set variantClasses = {
    'primary': 'bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500',
    'secondary': 'bg-gray-200 text-gray-900 hover:bg-gray-300 focus:ring-gray-500',
    'danger': 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    'success': 'bg-green-600 text-white hover:bg-green-700 focus:ring-green-500',
    'warning': 'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-500',
    'ghost': 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-500',
    'link': 'bg-transparent text-blue-600 hover:text-blue-800 underline focus:ring-blue-500'
} %}

{% set sizeClasses = {
    'sm': 'px-3 py-1.5 text-xs',
    'md': 'px-4 py-2 text-sm',
    'lg': 'px-6 py-3 text-base',
    'xl': 'px-8 py-4 text-lg'
} %}

<button
    type="{{ type }}"
    class="{{ baseClasses }} {{ variantClasses[variant] }} {{ sizeClasses[size] }} {{ disabled ? 'opacity-50 cursor-not-allowed' : '' }}"
    {{ disabled ? 'disabled' : '' }}
    {{ attributes|default('') }}
>
    {{ text|default('Button') }}
</button>
