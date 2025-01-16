<script setup>
import Button from 'primevue/button';
import Deposit from './Partials/Deposit.vue';

const props = defineProps({
    wallet: Object,
})
</script>

<template>
    <div class="flex flex-col gap-2">
        <template v-if="wallet.type === 'cash_wallet'">
            <div class="flex justify-between w-full gap-2">
                <Deposit :wallet="wallet"/>

                <Button
                    class="w-full flex justify-center gap-1"
                >
                    Withdrawal
                </Button>
            </div>
            <div class="flex items-center justify-center w-full">
                <Button
                    class="w-full flex justify-center gap-1"
                >
                    Internal Transfer
                </Button>
            </div>
        </template>

        <template v-if="wallet.type === 'bonus_wallet'">
            <div class="flex justify-between w-full gap-2">
                <Deposit :wallet="wallet"/>
                <Button
                    class="w-full flex justify-center gap-1"
                >
                    withdrawal
                </Button>
            </div>
        </template>
    </div>
</template>