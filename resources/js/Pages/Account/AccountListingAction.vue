<script setup>
import Button from "primevue/button";
import {ref} from "vue";
import Dialog from "primevue/dialog";

const props = defineProps({
    account: Object,
});

const visible = ref(false);

const openDialog = () => {
    visible.value = true;
}

const closeDialog = () => {
    visible.value = false;
}
</script>

<template>
    <Button
        type="button"
        severity="secondary"
        outlined
        :label="$t('public.view_report')"
        class="w-full"
        size="small"
        @click="openDialog"
    />

    <Dialog
        v-model:visible="visible"
        modal
        :header="$t('public.view_report')"
        class="dialog-xs md:dialog-md"
    >
    </Dialog>
</template>
