{# TAVPblocks Select Component #}
{# Usage: {% include 'components/select.volt' with {'name': 'role', 'options': roles, 'label': 'Role'} %} #}

{% set disabled = disabled|default(false) %}
{% set required = required|default(false) %}
{% set multiple = multiple|default(false) %}
{% set error = error|default(null) %}

{% set baseClasses = 'block w-full rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500' %}
{% set errorClasses = error ? 'border-red-300' : 'border-gray-300' %}

<div class="mb-4">
    {% if label is defined %}
        <label for="{{ name }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            {% if required %}<span class="text-red-500">*</span>{% endif %}
        </label>
    {% endif %}

    <select
        name="{{ name }}"
        id="{{ name }}"
        class="{{ baseClasses }} {{ errorClasses }}"
        {{ multiple ? 'multiple' : '' }}
        {{ disabled ? 'disabled' : '' }}
        {{ required ? 'required' : '' }}
    >
        {% if placeholder is defined %}
            <option value="">{{ placeholder }}</option>
        {% endif %}

        {% for option in options|default([]) %}
            {% if option is iterable %}
                <option value="{{ option.value }}" {{ option.value == value|default('') ? 'selected' : '' }}>
                    {{ option.label }}
                </option>
            {% else %}
                <option value="{{ option }}" {{ option == value|default('') ? 'selected' : '' }}>
                    {{ option }}
                </option>
            {% endif %}
        {% endfor %}
    </select>

    {% if error %}
        <p class="mt-1 text-sm text-red-600">{{ error }}</p>
    {% endif %}
</div>
