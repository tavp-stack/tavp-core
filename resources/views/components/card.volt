{# TAVPblocks Card Component #}
{# Usage: {% include 'components/card.volt' with {'title': 'Card Title'} %} #}

{% set padded = padded|default(true) %}
{% set border = border|default(true) %}
{% set shadow = shadow|default('sm') %}

{% set shadowClasses = {
    'none': '',
    'sm': 'shadow-sm',
    'md': 'shadow',
    'lg': 'shadow-lg'
} %}

<div class="bg-white {{ border ? 'border border-gray-200' : '' }} {{ shadowClasses[shadow] }} rounded-lg {{ padded ? 'p-6' : '' }}">
    {% if title is defined or subtitle is defined or actions is defined %}
        <div class="flex items-center justify-between mb-4">
            <div>
                {% if title is defined %}
                    <h3 class="text-lg font-semibold text-gray-900">{{ title }}</h3>
                {% endif %}
                {% if subtitle is defined %}
                    <p class="text-sm text-gray-500 mt-1">{{ subtitle }}</p>
                {% endif %}
            </div>
            {% if actions is defined %}
                <div class="flex gap-2">
                    {{ actions }}
                </div>
            {% endif %}
        </div>
    {% endif %}

    {{ content|default('') }}

    {% if footer is defined %}
        <div class="mt-4 pt-4 border-t border-gray-200">
            {{ footer }}
        </div>
    {% endif %}
</div>
