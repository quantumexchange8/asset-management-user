
<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

// PrimeVue Components
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dropdown from 'primevue/dropdown';
import Calendar from 'primevue/calendar';

// Table Data
const rows = ref([
    {
        date: '12/11/2024 05:01:28',
        username: '韩立萍',
        accountNumber: '457639',
        volume: 0.16,
        rebate: '$1.12',
        status: 'Approved',
        type: 'Personal',
    },
    {
        date: '12/11/2024 04:01:34',
        username: '陈金光',
        accountNumber: '457866',
        volume: 0.38,
        rebate: '$2.66',
        status: 'Approved',
        type: 'Affiliate',
    },
    // Add more rows as needed
    {
        date: '14/11/2024 04:01:34',
        username: 'wendy',
        accountNumber: '457867',
        volume: 0.78,
        rebate: '$2.34',
        status: 'Approved',
        type: 'Affiliate',
    },
    // Add more rows as needed
]);

// State Variables
const searchQuery = ref('');
const filterType = ref('');
const filterDateRange = ref([]);
const showFilterDropdown = ref(false);

// Dropdown Options for Type
const typeOptions = [
    { label: 'Affiliate', value: 'Affiliate' },
    { label: 'Personal', value: 'Personal' },
];

// Computed Property: Filtered Rows
const filteredRows = computed(() => {
    return rows.value.filter((row) => {
        const matchesSearch =
            !searchQuery.value ||
            row.username.includes(searchQuery.value) ||
            row.accountNumber.toString().includes(searchQuery.value);

        const matchesType =
            !filterType.value || row.type === filterType.value;

        const matchesDateRange =
            !filterDateRange.value.length ||
            (new Date(row.date) >= filterDateRange.value[0] &&
                new Date(row.date) <= filterDateRange.value[1]);

        return matchesSearch && matchesType && matchesDateRange;
    });
});

// Functions
const toggleFilterDropdown = () => {
    showFilterDropdown.value = !showFilterDropdown.value;
};

const clearFilters = () => {
    searchQuery.value = '';
    filterType.value = '';
    filterDateRange.value = [];
};
</script>

<template>
    <AuthenticatedLayout :title="'Dashboard'">
        <div>
            <!-- Search & Filter -->
            <div class="flex items-center space-x-2 mb-4">
                <!-- Search Input with Icon -->
                <div class="flex items-center bg-white px-2 py-1 rounded-lg shadow-sm">
                    <i class="pi pi-search text-gray-400"></i>
                    <InputText
                        v-model="searchQuery"
                        class="border-0 focus:outline-none ml-2"
                        placeholder="Search"
                    />
                </div>

                <!-- Filter Button with Icon -->
                <Button
                   
                    label="Filter"
                    class="p-button-outlined"
                    @click="toggleFilterDropdown"
                />
            </div>

            <!-- Dropdown for Filters -->
            <div v-if="showFilterDropdown" class="absolute bg-white shadow-lg rounded-lg p-4 w-64 z-50">
                <!-- Filter by Type -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm mb-2">Filter by Type</label>
                    <Dropdown
                        v-model="filterType"
                        :options="typeOptions"
                        optionLabel="label"
                        placeholder="Select a type"
                        class="w-full"
                    />
                </div>

                <!-- Filter by Date -->
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Filter Date</label>
                    <Calendar
                        v-model="filterDateRange"
                        selectionMode="range"
                        dateFormat="dd/mm/yy"
                        placeholder="dd/mm/yyyy - dd/mm/yyyy"
                        class="w-full"
                    />
                </div>

                <!-- Clear All Button -->
                <Button
                    label="Clear All"
                    icon="pi pi-times"
                    class="p-button-outlined p-button-secondary w-full mt-4"
                    @click="clearFilters"
                />
            </div>

            <!-- PrimeVue DataTable -->
            <DataTable :value="filteredRows" paginator :rows="5" class="p-datatable-gridlines">
                <Column field="date" header="Date" sortable />
                <Column field="username" header="Username" />
                <Column field="accountNumber" header="Account Number" />
                <Column field="volume" header="Volume" sortable />
                <Column field="rebate" header="Rebate" sortable />
                <!-- Custom Status Column -->
                <Column header="Status">
                    <template #body="slotProps">
                        <span
                            v-if="slotProps.data.status === 'Approved'"
                            class="bg-green-100 text-green-700 font-medium text-sm px-3 py-1 rounded-full"
                        >
                            {{ slotProps.data.status }}
                        </span>
                    </template>
                </Column>
            </DataTable>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
/* No additional styles needed with Tailwind CSS */
</style>
