{# TAVPblocks Tabs Component #}
{# Usage: {% include 'components/tabs.volt' with {'tabs': tabs, 'active': 'tab1'} %} #}

{% set active = active|default(tabs|first|default(''))|keys|first %}

<div x-data="{ activeTab: '{{ active }}' }">
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8">
            {% for key, tab in tabs %}
                <button
                    @click="activeTab = '{{ key }}'"
                    :class="activeTab === '{{ key }}' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                >
                    {{ tab.label|default(key) }}
                </button>
            {% endfor %}
        </nav>
    </div>

    <div class="mt-4">
        {% for key, tab in tabs %}
            <div x-show="activeTab === '{{ key }}'" x-cloak>
                {{ tab.content|default('') }}
            </div>
        {% endfor %}
    </div>
</div>
