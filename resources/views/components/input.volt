{# TAVPblocks Input Component #}
{# Usage: {% include 'components/input.volt' with {'name': 'email', 'type': 'email', 'label': 'Email'} %} #}

{% set type = type|default('text') %}
{% set size = size|default('md') %}
{% set disabled = disabled|default(false) %}
{% set required = required|default(false) %}
{% set error = error|default(null) %}

{% set baseClasses = 'block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500' %}

{% set sizeClasses = {
    'sm': 'px-3 py-1.5 text-sm',
    'md': 'px-4 py-2 text-base',
    'lg': 'px-5 py-3 text-lg'
} %}

{% set errorClasses = error ? 'border-red-300 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500' : 'border-gray-300' %}

<div class="mb-4">
    {% if label is defined %}
        <label for="{{ name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            {% if required %}<span class="text-red-500">*</span>{% endif %}
        </label>
    {% endif %}

    <input
        type="{{ type }}"
        name="{{ name }}"
        id="{{ name }}"
        value="{{ value|default('') }}"
        placeholder="{{ placeholder|default('') }}"
        class="{{ baseClasses }} {{ sizeClasses[size] }} {{ errorClasses }} {{ disabled ? 'bg-gray-50 cursor-not-allowed' : '' }}"
        {{ disabled ? 'disabled' : '' }}
        {{ required ? 'required' : '' }}
        {{ attributes|default('') }}
    >

    {% if error %}
        <p class="mt-1 text-sm text-red-600">{{ error }}</p>
    {% endif %}

    {% if help is defined %}
        <p class="mt-1 text-sm text-gray-500">{{ help }}</p>
    {% endif %}
</div>
