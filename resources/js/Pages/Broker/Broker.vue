<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BrokerListing from "@/Pages/Broker/BrokerListing.vue";

defineProps({
    brokerCounts: Number
})
</script>

<template>
    <AuthenticatedLayout title="broker">
        <div class="flex flex-col gap-5 items-center self-stretch w-full">
            <BrokerListing
                :brokerCounts="brokerCounts"
            />
        </div>
    </AuthenticatedLayout>
</template>
