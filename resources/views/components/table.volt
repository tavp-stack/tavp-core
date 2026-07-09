{# TAVPblocks Table Component #}
{# Usage: {% include 'components/table.volt' with {'columns': columns, 'rows': data} %} #}

<div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                {% if selectable is defined and selectable %}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600">
                    </th>
                {% endif %}
                {% for col in columns %}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        {{ col.label|default(col.field|default(col)) }}
                    </th>
                {% endfor %}
                {% if actions is defined %}
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                {% endif %}
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            {% for row in rows %}
                <tr class="hover:bg-gray-50">
                    {% if selectable is defined and selectable %}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600">
                        </td>
                    {% endif %}
                    {% for col in columns %}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ row[col.field|default(col)]|default('') }}
                        </td>
                    {% endfor %}
                    {% if actions is defined %}
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ actions }}
                        </td>
                    {% endif %}
                </tr>
            {% endfor %}
        </tbody>
    </table>
</div>
