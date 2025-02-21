<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import ReferralOverview from "@/Pages/Referrals/ReferralOverview.vue";
import ReferralView from "@/Pages/Referrals/ReferralView.vue";

defineProps({
    total_directs: Number,
    total_networks: Number,
    total_earnings: Number,
})
</script>

<template>
    <AuthenticatedLayout title="referral_programme">
        <div class="flex flex-col gap-5 items-center self-stretch">
            <ReferralOverview
                :total_directs="total_directs"
                :total_networks="total_networks"
                :total_earnings="total_earnings"
            />

            <ReferralView
            />
        </div>
    </AuthenticatedLayout>
</template>
