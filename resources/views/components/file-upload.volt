{# TAVPblocks File Upload Component #}
{# Usage: {% include 'components/file-upload.volt' with {'name': 'avatar', 'accept': 'image/*'} %} #}

{% set multiple = multiple|default(false) %}
{% set maxSize = maxSize|default(10) %}
{% set accept = accept|default('*') %}

<div
    x-data="{
        files: [],
        dragging: false,
        addFiles(event) {
            const newFiles = Array.from(event.target.files);
            this.files = [...this.files, ...newFiles];
        },
        removeFile(index) {
            this.files.splice(index, 1);
        },
        formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }
    }"
    class="w-full"
>
    <div
        @dragover.prevent="dragging = true"
        @dragleave.prevent="dragging = false"
        @drop.prevent="dragging = false; addFiles(\$event)"
        :class="dragging ? 'border-blue-500 bg-blue-50' : 'border-gray-300'"
        class="border-2 border-dashed rounded-lg p-6 text-center transition-colors"
    >
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
        </svg>
        <p class="mt-2 text-sm text-gray-600">
            <span class="font-medium text-blue-600 hover:text-blue-500">Click to upload</span>
            or drag and drop
        </p>
        <p class="mt-1 text-xs text-gray-500">Max size: {{ maxSize }}MB</p>
    </div>

    <input
        type="file"
        name="{{ name|default('file') }}"
        {{ multiple ? 'multiple' : '' }}
        accept="{{ accept }}"
        @change="addFiles(\$event)"
        class="hidden"
    >

    <div x-show="files.length > 0" class="mt-4 space-y-2">
        <template x-for="(file, index) in files" :key="index">
            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                <div class="flex items-center space-x-2">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="text-sm text-gray-700" x-text="file.name"></span>
                    <span class="text-xs text-gray-500" x-text="formatSize(file.size)"></span>
                </div>
                <button @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </template>
    </div>
</div>
