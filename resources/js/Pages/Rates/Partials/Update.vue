<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    rate: Object,
    rates: Object,
});

const form = useForm({
    _method: "PUT",
    amount: props.rate.amount,
    status: props.rate.status,
});

const updateRate = () => {
    form.post(route("rates.update",props.rate.id), {
        errorBag: "updateRate",
        preserveScroll: true,
    });
};

</script>

<template>
    <AppLayout title="Rates">
        <div class="rtl max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <FormSection @submitted="updateRate">
                <template #title>ویرایش نرخ ارز </template>

                <template #description>
                    برای ویرایش نرخ ارز انتخاب شده اطلاعات ذیل را به دقت ویرایش نمایید.
                </template>

                <template #form>
                    <!-- Amount -->
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="amount" value="نرخ ارز" />
                        <TextInput
                            id="amount"
                            v-model="form.amount"
                            type="text"
                            class="mt-1 block w-full"
                            autocomplete="amount"
                        />
                        <InputError
                            :message="form.errors.amount"
                            class="mt-2"
                        />
                    </div>

                    <!-- status -->
                    <div class="col-span-6 sm:col-span-4">
                        <InputLabel for="status" value="شاخص نرخ ارز" />
                        <select
                            type="status"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            autocomplete="username"
                            v-model="form.status"
                            id="status"
                        >
                            <option value="A" class="A">A</option>
                            <option value="B" class="B">B</option>
                            <option value="C" class="C">C</option>
                            <option value="D" class="D">D</option>
                            <option value="E" class="E">E</option>
                            <option value="F" class="F">F</option>
                            <option value="G" class="G">G</option>
                            <option value="N" class="N">N</option>
                        </select>
                        <InputError
                            :message="form.errors.status"
                            class="mt-2"
                        />
                    </div>
                </template>

                <template #actions>
                    <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                        ویرایش موفقانه صورت گرفت.
                    </ActionMessage>

                    <PrimaryButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                    <i class="px-2 material-icons hover:cursor-pointer">import_export</i>
                        ویرایش
                    </PrimaryButton>
                </template>
            </FormSection>
        </div>
    </AppLayout>
</template>
