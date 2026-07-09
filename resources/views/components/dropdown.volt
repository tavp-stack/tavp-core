{# TAVPblocks Dropdown Component #}
{# Usage: {% include 'components/dropdown.volt' with {'trigger': 'Options', 'items': menuItems} %} #}

{% set align = align|default('left') %}

{% set alignClasses = {
    'left': 'left-0',
    'right': 'right-0'
} %}

<div class="relative inline-block text-left" x-data="{ open: false }">
    <div @click="open = !open">
        {{ trigger|default('Dropdown') }}
    </div>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute {{ alignClasses[align] }} mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50"
        style="display: none;"
    >
        <div class="py-1" role="menu">
            {% for item in items|default([]) %}
                {% if item.divider is defined and item.divider %}
                    <div class="border-t border-gray-100 my-1"></div>
                {% else %}
                    <a
                        href="{{ item.href|default('#') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                        role="menuitem"
                    >
                        {% if item.icon is defined %}
                            <span class="mr-2">{{ item.icon }}</span>
                        {% endif %}
                        {{ item.label|default(item) }}
                    </a>
                {% endif %}
            {% endfor %}
        </div>
    </div>
</div>
