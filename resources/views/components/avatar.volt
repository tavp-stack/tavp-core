{# TAVPblocks Avatar Component #}
{# Usage: {% include 'components/avatar.volt' with {'name': 'John Doe', 'size': 'md'} %} #}

{% set size = size|default('md') %}
{% set src = src|default(null) %}
{% set alt = alt|default(name|default('User')) %}

{% set sizeClasses = {
    'sm': 'h-8 w-8 text-xs',
    'md': 'h-10 w-10 text-sm',
    'lg': 'h-12 w-12 text-base',
    'xl': 'h-16 w-16 text-lg'
} %}

{% set colors = ['bg-blue-500', 'bg-green-500', 'bg-yellow-500', 'bg-red-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'] %}

{% if src %}
    <img src="{{ src }}" alt="{{ alt }}" class="{{ sizeClasses[size] }} rounded-full object-cover">
{% else %}
    {% set initials = name|default('U')|split(' ')|map(p => p|first)|join('')|upper|slice(0, 2) %}
    {% set colorIndex = name|default('U')|length % colors|length %}
    <div class="{{ sizeClasses[size] }} rounded-full {{ colors[colorIndex] }} flex items-center justify-center text-white font-medium">
        {{ initials }}
    </div>
{% endif %}
