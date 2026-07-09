{# TAVPblocks Divider Component #}
{# Usage: {% include 'components/divider.volt' %} #}

<div class="relative my-6">
    <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-gray-300"></div>
    </div>
    {% if label is defined %}
        <div class="relative flex justify-center">
            <span class="bg-white px-4 text-sm text-gray-500">{{ label }}</span>
        </div>
    {% endif %}
</div>
