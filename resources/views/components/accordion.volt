{# TAVPblocks Accordion Component #}
{# Usage: {% include 'components/accordion.volt' with {'items': accordionItems} %} #}

<div class="space-y-2" x-data="{ open: null }">
    {% for item in items %}
        <div class="border border-gray-200 rounded-lg">
            <button
                @click="open = open === {{ loop.index }} ? null : {{ loop.index }}"
                class="w-full flex justify-between items-center px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50"
            >
                {{ item.title|default('Section ' ~ loop.index) }}
                <svg
                    :class="open === {{ loop.index }} ? 'rotate-180' : ''"
                    class="h-5 w-5 text-gray-500 transition-transform"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div
                x-show="open === {{ loop.index }}"
                x-collapse
                class="px-4 pb-3 text-gray-600"
            >
                {{ item.content|default('') }}
            </div>
        </div>
    {% endfor %}
</div>
