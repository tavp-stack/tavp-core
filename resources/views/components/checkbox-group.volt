{# TAVPblocks Checkbox Group Component #}
{# Usage: {% include 'components/checkbox-group.volt' with {'name': 'tags', 'options': tags} %} #}

{% set selected = selected|default([]) %}

<div class="space-y-2">
    {% for option in options %}
        <label class="flex items-center">
            <input
                type="checkbox"
                name="{{ name|default('checkbox') }}[]"
                value="{{ option.value|default(option) }}"
                {{ option.value|default(option) in selected ? 'checked' : '' }}
                class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <span class="ml-2 text-sm text-gray-700">{{ option.label|default(option) }}</span>
        </label>
    {% endfor %}
</div>
