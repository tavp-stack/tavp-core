{# TAVPblocks Stack Component #}
{# Usage: {% include 'components/stack.volt' with {'direction': 'vertical', 'spacing': 4} %} #}

{% set direction = direction|default('vertical') %}
{% set spacing = spacing|default(4) %}

{% set directionClasses = {
    'horizontal': 'flex flex-row items-center',
    'vertical': 'flex flex-col'
} %}

{% set spacingClasses = {
    '0': '',
    '1': 'space-y-1',
    '2': 'space-y-2',
    '3': 'space-y-3',
    '4': 'space-y-4',
    '5': 'space-y-5',
    '6': 'space-y-6',
    '8': 'space-y-8'
} %}

{% set spacingHClasses = {
    '0': '',
    '1': 'space-x-1',
    '2': 'space-x-2',
    '3': 'space-x-3',
    '4': 'space-x-4',
    '5': 'space-x-5',
    '6': 'space-x-6',
    '8': 'space-x-8'
} %}

<div class="{{ directionClasses[direction] }} {{ direction == 'vertical' ? spacingClasses[spacing|default('4')] : spacingHClasses[spacing|default('4')] }}">
    {{ content|default('') }}
</div>
