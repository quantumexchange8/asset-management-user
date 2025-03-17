<script setup>
import Card from "primevue/card";
import {usePage} from "@inertiajs/vue3";
import {generalFormat} from "@/Composables/format.js";
import Tag from "primevue/tag";
import Button from "primevue/button";
import dayjs from "dayjs";

defineProps({
    accumulatedPersonalShare: {
        type: [String, Number]
    },
})

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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 w-full">
        <Card class="w-full">
            <template #content>
                <div class="flex flex-col gap-3 w-full items-center self-stretch">
                    <div class="flex flex-col items-center rounded p-5 bg-surface-100 dark:bg-surface-800 w-full">
                        <div class="text-3xl font-medium">
                            {{ formatAmount(accumulatedPersonalShare, 4) }}
                        </div>
                        <span class="text-surface-500 text-sm">{{ $t('public.personal_share') }}</span>
                    </div>
                    <div class="flex text-sm text-surface-500 items-center justify-between gap-1 w-full">
                        <Tag
                            severity="info"
                            :value="$t('public.accumulating')"
                        />
                        <Button
                            type="button"
                            severity="secondary"
                            outlined
                            as="a"
                            :href="route('report.profit_sharing')"
                            :label="$t('public.view_report')"
                            size="small"
                            class="!py-0.5"
                        />
                    </div>
                </div>
            </template>
        </Card>
        <Card class="w-full">
            <template #content>
                <div class="flex flex-col gap-3 w-full items-center self-stretch">
                    <div class="flex flex-col items-center rounded p-5 bg-surface-100 dark:bg-surface-800 w-full">
                        <div class="text-3xl font-medium">
                            {{ formatAmount(user.accumulated_amount, 4) }}
                        </div>
                        <span class="text-surface-500 text-sm">{{ $t('public.bonus') }}</span>
                    </div>
                    <div class="flex text-sm text-surface-500 items-center justify-between gap-1 w-full">
                        <Tag
                            severity="info"
                            :value="$t('public.accumulating')"
                        />
                        <div class="flex items-center justify-end gap-2">
                            {{ $t('public.receive_on_saturday') }}:
                            <span class="text-base text-surface-950 dark:text-white">{{ nextSaturday }}</span>
                        </div>
                    </div>
                </div>
            </template>
        </Card>
    </div>
</template>
