<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {ref} from "vue";
import RebateOverview from "@/Pages/Report/Rebate/RebateOverview.vue";
import RebateBonusTable from "@/Pages/Report/Rebate/RebateBonusTable.vue";

const totalVolume = ref(null);
const totalRebate = ref(null);

const handleOverview = (data) => {
    totalVolume.value = Number(data.totalVolume);
    totalRebate.value = Number(data.totalRebate);
}
</script>

<template>
    <AuthenticatedLayout title="rebate_bonus">
        <div class="flex flex-col items-center gap-5">
            <RebateOverview
                :totalVolume="totalVolume"
                :totalRebate="totalRebate"
            />

            <RebateBonusTable
                @update-totals="handleOverview"
            />
        </div>
    </AuthenticatedLayout>
</template>
