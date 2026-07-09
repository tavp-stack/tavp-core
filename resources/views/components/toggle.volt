{# TAVPblocks Toggle/Switch Component #}
{# Usage: {% include 'components/toggle.volt' with {'name': 'active', 'checked': true} %} #}

{% set checked = checked|default(false) %}
{% set disabled = disabled|default(false) %}

<label class="relative inline-flex items-center cursor-pointer">
    <input
        type="checkbox"
        name="{{ name|default('toggle') }}"
        value="1"
        class="sr-only peer"
        {{ checked ? 'checked' : '' }}
        {{ disabled ? 'disabled' : '' }}
    >
    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600 {{ disabled ? 'opacity-50 cursor-not-allowed' : '' }}"></div>
    {% if label is defined %}
        <span class="ml-3 text-sm font-medium text-gray-900">{{ label }}</span>
    {% endif %}
</label>
