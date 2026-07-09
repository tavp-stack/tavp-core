{# TAVPblocks Modal Component #}
{# Usage: {% include 'components/modal.volt' with {'id': 'confirm-modal', 'title': 'Confirm Action'} %} #}

{% set size = size|default('md') %}

{% set sizeClasses = {
    'sm': 'max-w-md',
    'md': 'max-w-lg',
    'lg': 'max-w-2xl',
    'xl': 'max-w-4xl',
    'full': 'max-w-6xl'
} %}

<div
    id="{{ id|default('modal') }}"
    class="fixed inset-0 z-50 hidden overflow-y-auto"
    aria-labelledby="{{ id|default('modal') }}-title"
    role="dialog"
    aria-modal="true"
>
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:align-middle {{ sizeClasses[size] }}">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="{{ id|default('modal') }}-title">
                            {{ title|default('Modal Title') }}
                        </h3>
                        <div class="mt-2">
                            {{ content|default('') }}
                        </div>
                    </div>
                </div>
            </div>
            {% if footer is defined %}
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    {{ footer }}
                </div>
            {% endif %}
        </div>
    </div>
</div>

<script>
function toggleModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.toggle('hidden');
}
</script>
