<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import BrokerListing from "@/Pages/Broker/BrokerListing.vue";
</script>

<template>
    <AuthenticatedLayout title="broker">
        <div class="flex flex-col gap-5 items-center self-stretch w-full">
            <BrokerListing />
        </div>
    </AuthenticatedLayout>
</template>
