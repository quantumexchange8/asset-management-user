<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import StandardOverview from "@/Pages/Report/Standard/StandardOverview.vue";
import StandardBonusTable from "@/Pages/Report/Standard/StandardBonusTable.vue";

const totalBonusAmount = ref(null);
const maxBonusAmount = ref(null);

const handleOverview = (data) => {
    totalBonusAmount.value = Number(data.totalBonusAmount);
    maxBonusAmount.value = data.maxBonusAmount;
}
</script>

<template>
    <AuthenticatedLayout title="ib_group_incentive">
        <div class="flex flex-col items-center gap-5">
            <StandardOverview
                :totalBonusAmount="totalBonusAmount"
                :maxBonusAmount="maxBonusAmount"
            />

            <StandardBonusTable
                @update-totals="handleOverview"
            />
        </div>
    </AuthenticatedLayout>
</template>
