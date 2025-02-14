<script setup>
import {IconWallet} from "@tabler/icons-vue";
import Card from "primevue/card";

defineProps({
    title: String
})
</script>

<template>
    <Card class="w-full">
        <template #title>
            <div class="flex gap-2 items-center">
                <IconWallet size="28" />
                <span>{{ $t(`public.${title}`) }}</span>
            </div>
        </template>
        <template #content>

        </template>
    </Card>
</template>
