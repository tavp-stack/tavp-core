{# TAVPblocks Breadcrumb Component #}
{# Usage: {% include 'components/breadcrumb.volt' with {'items': breadcrumbItems} %} #}

<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        {% for item in items %}
            <li class="flex items-center">
                {% if not loop.first %}
                    <svg class="h-5 w-5 text-gray-400 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                {% endif %}

                {% if item.href is defined and not loop.last %}
                    <a href="{{ item.href }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                        {{ item.label }}
                    </a>
                {% else %}
                    <span class="text-sm font-medium text-gray-900">{{ item.label }}</span>
                {% endif %}
            </li>
        {% endfor %}
    </ol>
</nav>
