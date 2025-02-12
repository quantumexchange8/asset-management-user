<script setup>
import Card from "primevue/card";
import Button from "primevue/button";
</script>

<template>
    <Card>
        <template #title>
            {{ $t('public.transfer') }}
        </template>
        <template #content>
            <div class="flex flex-col gap-1 items-center">
                <Button
                    severity="success"
                    size="small"
                    :label="$t('public.deposit')"
                    class="w-full"
                />
                <Button
                    severity="danger"
                    size="small"
                    :label="$t('public.withdraw')"
                    class="w-full"
                />
            </div>
        </template>
    </Card>
</template>
