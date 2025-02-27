<script setup>
import Card from "primevue/card";
import {usePage} from "@inertiajs/vue3";
import {generalFormat} from "@/Composables/format.js";
import {computed} from "vue";
import dayjs from "dayjs";

const user = usePage().props.auth.user;
const {formatAmount} = generalFormat();

const getNextSaturday = () => {
    const today = dayjs();
    const dayOfWeek = today.day();
    const daysUntilSaturday = (6 - dayOfWeek + 7) % 7 || 7;
    return today.add(daysUntilSaturday, 'day').format('YYYY-MM-DD');
};

const nextSaturday = getNextSaturday();
</script>

<template>
    <Card class="w-full">
        <template #content>
            <div class="flex flex-col gap-3 w-full items-center self-stretch">
                <div class="flex flex-col items-center p-5 bg-surface-100 dark:bg-surface-800 w-full">
                    <div class="text-3xl font-medium">
                        ${{ formatAmount(user.accumulated_amount, 4) }}
                    </div>
                    <span class="text-surface-500 text-sm">{{ $t('public.accumulate_bonus') }}</span>
                </div>
                <div class="flex text-sm text-surface-500 items-center justify-center gap-1 w-full">
                    {{ $t('public.receive_on_saturday') }} -
                    <span class="text-base text-surface-950 dark:text-white">{{ nextSaturday }}</span>
                </div>
            </div>
        </template>
    </Card>
</template>
