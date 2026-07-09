{# TAVPblocks Date Picker Component #}
{# Usage: {% include 'components/date-picker.volt' with {'name': 'due_date'} %} #}

{% set type = type|default('date') %}
{% set required = required|default(false) %}

<div
    x-data="{
        value: '{{ value|default('') }}',
        showCalendar: false,
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        daysInMonth: new Date(this.currentYear, this.currentMonth + 1, 0).getDate(),
        firstDay: new Date(this.currentYear, this.currentMonth, 1).getDay(),
        formatDate(date) {
            return date.toISOString().split('T')[0];
        },
        selectDate(day) {
            const date = new Date(this.currentYear, this.currentMonth, day);
            this.value = this.formatDate(date);
            this.showCalendar = false;
        },
        prevMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },
        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        }
    }"
    class="relative"
>
    <input
        type="text"
        name="{{ name|default('date') }}"
        x-model="value"
        @click="showCalendar = !showCalendar"
        readonly
        placeholder="{{ placeholder|default('Select date') }}"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
        {{ required ? 'required' : '' }}
    >

    <div
        x-show="showCalendar"
        @click.away="showCalendar = false"
        class="absolute z-50 mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4"
        style="display: none;"
    >
        <div class="flex items-center justify-between mb-4">
            <button @click="prevMonth" type="button" class="text-gray-500 hover:text-gray-700">‹</button>
            <span class="text-sm font-medium" x-text="new Date(currentYear, currentMonth).toLocaleDateString('en-US', {month: 'long', year: 'numeric'})"></span>
            <button @click="nextMonth" type="button" class="text-gray-500 hover:text-gray-700">›</button>
        </div>

        <div class="grid grid-cols-7 gap-1 text-center text-xs">
            <div class="font-medium text-gray-500">Su</div>
            <div class="font-medium text-gray-500">Mo</div>
            <div class="font-medium text-gray-500">Tu</div>
            <div class="font-medium text-gray-500">We</div>
            <div class="font-medium text-gray-500">Th</div>
            <div class="font-medium text-gray-500">Fr</div>
            <div class="font-medium text-gray-500">Sa</div>

            <template x-for="i in firstDay" :key="'empty-'+i">
                <div></div>
            </template>

            <template x-for="day in daysInMonth" :key="day">
                <button
                    @click="selectDate(day)"
                    type="button"
                    :class="value === formatDate(new Date(currentYear, currentMonth, day)) ? 'bg-blue-600 text-white' : 'hover:bg-gray-100'"
                    class="w-8 h-8 rounded-full flex items-center justify-center text-sm"
                    x-text="day"
                ></button>
            </template>
        </div>
    </div>
</div>
