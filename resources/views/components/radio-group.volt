{# TAVPblocks Radio Group Component #}
{# Usage: {% include 'components/radio-group.volt' with {'name': 'plan', 'options': plans, 'selected': 'basic'} %} #}

{% set selected = selected|default(null) %}

<div class="space-y-2">
    {% for option in options %}
        <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 {{ option.value|default(option) == selected ? 'border-blue-500 bg-blue-50' : 'border-gray-200' }}">
            <input
                type="radio"
                name="{{ name|default('radio') }}"
                value="{{ option.value|default(option) }}"
                {{ option.value|default(option) == selected ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500"
            >
            <div class="ml-3">
                <span class="text-sm font-medium text-gray-900">{{ option.label|default(option) }}</span>
                {% if option.description is defined %}
                    <p class="text-sm text-gray-500">{{ option.description }}</p>
                {% endif %}
            </div>
        </label>
    {% endfor %}
</div>
