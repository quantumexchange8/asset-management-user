<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    file: null,
});

const handleFileChange = (event) => {
    form.file = event.target.files[0];
};

const uploadKYC = () => {
    form.post(route('kyc.upload'), {
        onSuccess: () => {
            alert('KYC submitted successfully!');
            form.reset();
        },
        onError: () => {
            alert('Failed to submit KYC. Please try again.');
        },
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                KYC Verification
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Upload your Identification Card (IC) for verification.
            </p>
        </header>

        <form @submit.prevent="uploadKYC" class="mt-6 space-y-6">
            <div>
                <InputLabel for="file" value="Upload IC" />
                <input
                    id="file"
                    type="file"
                    @change="handleFileChange"
                    class="mt-1 block w-full border rounded-lg px-3 py-2"
                />
                <InputError :message="form.errors.file" class="mt-2" />
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Submit</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >
                        Submitted.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<style scoped>
/* Add custom styles if needed */
</style>
