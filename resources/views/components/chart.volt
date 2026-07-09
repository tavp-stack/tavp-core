{# TAVPblocks Chart Component #}
{# Usage: {% include 'components/chart.volt' with {'type': 'line', 'data': chartData} %} #}

{% set type = type|default('line') %}
{% set id = id|default('chart-' ~ random()) %}

<div class="w-full" style="height: {{ height|default('300px') }};">
    <canvas id="{{ id }}"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ id }}').getContext('2d');

    new Chart(ctx, {
        type: '{{ type }}',
        data: {{ data|default('{}')|raw }},
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: {{ showLegend is defined and showLegend ? 'true' : 'false' }}
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
