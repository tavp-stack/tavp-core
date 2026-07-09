{# TAVPblocks Tooltip Component #}
{# Usage: {% include 'components/tooltip.volt' with {'text': 'Tooltip text'} %} #}

{% set position = position|default('top') %}

{% set positionClasses = {
    'top': 'bottom-full left-1/2 -translate-x-1/2 mb-2',
    'bottom': 'top-full left-1/2 -translate-x-1/2 mt-2',
    'left': 'right-full top-1/2 -translate-y-1/2 mr-2',
    'right': 'left-full top-1/2 -translate-y-1/2 ml-2'
} %}

<div class="relative inline-block" x-data="{ show: false }">
    <div @mouseenter="show = true" @mouseleave="show = false">
        {{ trigger|default('Hover me') }}
    </div>
    <div
        x-show="show"
        x-transition
        class="absolute {{ positionClasses[position] }} z-50 px-3 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg shadow-sm whitespace-nowrap"
        style="display: none;"
    >
        {{ text|default('') }}
        <div class="tooltip-arrow"></div>
    </div>
</div>
