{# TAVPblocks Pagination Component #}
{# Usage: {% include 'components/pagination.volt' with {'currentPage': 1, 'lastPage': 10, 'baseUrl': '/posts'} %} #}

{% set currentPage = currentPage|default(1) %}
{% set lastPage = lastPage|default(1) %}
{% set baseUrl = baseUrl|default('#') %}
{% set showFirstLast = showFirstLast|default(true) %}

{% if lastPage > 1 %}
<nav class="flex items-center justify-between" aria-label="Pagination">
    <div class="flex-1 flex justify-between sm:hidden">
        {% if currentPage > 1 %}
            <a href="{{ baseUrl }}?page={{ currentPage - 1 }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Previous
            </a>
        {% endif %}
        {% if currentPage < lastPage %}
            <a href="{{ baseUrl }}?page={{ currentPage + 1 }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Next
            </a>
        {% endif %}
    </div>

    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
                Page <span class="font-medium">{{ currentPage }}</span> of <span class="font-medium">{{ lastPage }}</span>
            </p>
        </div>
        <div>
            <ul class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                {% if showFirstLast and currentPage > 1 %}
                    <li>
                        <a href="{{ baseUrl }}?page=1" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">First</span>
                            «
                        </a>
                    </li>
                {% endif %}

                {% if currentPage > 1 %}
                    <li>
                        <a href="{{ baseUrl }}?page={{ currentPage - 1 }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            ‹
                        </a>
                    </li>
                {% endif %}

                {% for page in range(max(1, currentPage - 2), min(lastPage, currentPage + 2)) %}
                    <li>
                        <a
                            href="{{ baseUrl }}?page={{ page }}"
                            class="relative inline-flex items-center px-4 py-2 border text-sm font-medium {{ page == currentPage ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' }}"
                        >
                            {{ page }}
                        </a>
                    </li>
                {% endfor %}

                {% if currentPage < lastPage %}
                    <li>
                        <a href="{{ baseUrl }}?page={{ currentPage + 1 }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            ›
                        </a>
                    </li>
                {% endif %}

                {% if showFirstLast and currentPage < lastPage %}
                    <li>
                        <a href="{{ baseUrl }}?page={{ lastPage }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Last</span>
                            »
                        </a>
                    </li>
                {% endif %}
            </ul>
        </div>
    </div>
</nav>
{% endif %}
