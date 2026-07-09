{# TAVPblocks Grid Component #}
{# Usage: {% include 'components/grid.volt' with {'cols': 3, 'gap': 4} %} #}

{% set cols = cols|default(3) %}
{% set gap = gap|default(4) %}

{% set colClasses = {
    '1': 'grid-cols-1',
    '2': 'grid-cols-1 sm:grid-cols-2',
    '3': 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3',
    '4': 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4',
    '5': 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5',
    '6': 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6'
} %}

{% set gapClasses = {
    '2': 'gap-2',
    '3': 'gap-3',
    '4': 'gap-4',
    '5': 'gap-5',
    '6': 'gap-6',
    '8': 'gap-8'
} %}

<div class="grid {{ colClasses[cols|default('3')] }} {{ gapClasses[gap|default('4')] }}">
    {{ content|default('') }}
</div>
