{# TAVPblocks Form Group Component #}
{# Usage: {% include 'components/form-group.volt' with {'label': 'Name', 'error': errors.name} %} #}

{% set required = required|default(false) %}
{% set error = error|default(null) %}
{% set help = help|default(null) %}

<div class="mb-4">
    {% if label is defined %}
        <label for="{{ name|default('') }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ label }}
            {% if required %}<span class="text-red-500">*</span>{% endif %}
        </label>
    {% endif %}

    <div class="mt-1">
        {{ content|default('') }}
    </div>

    {% if error %}
        <p class="mt-1 text-sm text-red-600">{{ error }}</p>
    {% endif %}

    {% if help and not error %}
        <p class="mt-1 text-sm text-gray-500">{{ help }}</p>
    {% endif %}
</div>
