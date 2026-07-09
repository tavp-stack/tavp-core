{# TAVPblocks Toast Component #}
{# Usage: {% include 'components/toast.volt' with {'type': 'success', 'message': 'Saved!'} %} #}

{% set type = type|default('success') %}
{% set duration = duration|default(3000) %}

{% set typeClasses = {
    'success': 'bg-green-500',
    'error': 'bg-red-500',
    'warning': 'bg-yellow-500',
    'info': 'bg-blue-500'
} %}

<div
    id="toast-{{ random() }}"
    x-data="{ show: true }"
    x-show="show"
    x-init="setTimeout(() => show = false, {{ duration }})"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="fixed bottom-4 right-4 z-50 flex items-center px-4 py-3 rounded-lg text-white shadow-lg {{ typeClasses[type] }}"
    style="display: none;"
>
    <span class="text-sm font-medium">{{ message|default('') }}</span>
    <button @click="show = false" class="ml-4 text-white hover:text-white/80">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    </button>
</div>

<script>
function showToast(type, message, duration = 3000) {
    const id = 'toast-' + Math.random().toString(36).substr(2, 9);
    const classes = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };

    const toast = document.createElement('div');
    toast.id = id;
    toast.className = 'fixed bottom-4 right-4 z-50 flex items-center px-4 py-3 rounded-lg text-white shadow-lg ' + classes[type];
    toast.innerHTML = `
        <span class="text-sm font-medium">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-4 text-white hover:text-white/80">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    `;

    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), duration);
}
</script>
