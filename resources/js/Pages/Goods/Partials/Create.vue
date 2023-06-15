<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const form = useForm({
    _method: "POST",
    serial: null,
    price: null,
    weight: null,
    mobis: null,
    korea: null,
});

const createGood = () => {
    form.post(route("goods.store"), {
        errorBag: "createGood",
        preserveScroll: true,
        onSuccess: () => clearInput(),
    });
};
const clearInput = () => {
    serial.value.value = null;
    price.value.value = null;
    weight.value.value = null;
    mobis.value.value = null;
    korea.value.value = null;
};
</script>

<template>
    <AppLayout title="Goods">
        <div>
            <div class="rtl max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <FormSection @submitted="createGood">
                    <template #title> ثبت جنس جدید در سیستم </template>

                    <template #description>
                        برای ثبت یک جنس جدید در سیستم فورم ذیل را به صورت دقیق خانه پری نمایید.
                    </template>

                    <template #form>
                        <!-- Serial Number -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="serial" value="شماره فنی" />
                            <TextInput required id="serial" v-model="form.serial" type="text" class="mt-1 block w-full"
                                autocomplete="serial" />
                            <InputError :message="form.errors.serial" class="mt-2" />
                        </div>

                        <!-- Price -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="price" value="قیمت جنس" />
                            <TextInput required id="price" v-model="form.price" type="text" class="mt-1 block w-full"
                                autocomplete="username" />
                            <InputError :message="form.errors.price" class="mt-2" />
                        </div>
                        <!-- Weight -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="weight" value="وزن جنس" />
                            <TextInput required id="weight" v-model="form.weight" type="text" class="mt-1 block w-full"
                                autocomplete="username" />
                            <InputError :message="form.errors.weight" class="mt-2" />
                        </div>
                        <!-- Mobis -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="mobis" value="موبیز" />
                            <TextInput required id="mobis" v-model="form.mobis" type="text" class="mt-1 block w-full"
                                autocomplete="username" />
                            <InputError :message="form.errors.mobis" class="mt-2" />
                        </div>
                        <!-- Korea section -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="korea" value="کوریا" />
                            <TextInput required id="korea" v-model="form.korea" type="text" class="mt-1 block w-full"
                                autocomplete="username" />
                            <InputError :message="form.errors.korea" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                            ذخیره سازی موفقانه صورت گرفت.
                        </ActionMessage>

                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            <i class="px-2 material-icons hover:cursor-pointer">save</i>
                            ذخیره سازی
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
