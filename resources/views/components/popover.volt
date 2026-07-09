{# TAVPblocks Popover Component #}
{# Usage: {% include 'components/popover.volt' with {'title': 'Popover Title'} %} #}

<div class="relative inline-block" x-data="{ open: false }">
    <div @click="open = !open">
        {{ trigger|default('Click me') }}
    </div>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition
        class="absolute z-50 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 p-4"
        style="display: none;"
    >
        {% if title is defined %}
            <h3 class="text-sm font-medium text-gray-900 mb-2">{{ title }}</h3>
        {% endif %}
        {{ content|default('') }}
    </div>
</div>
