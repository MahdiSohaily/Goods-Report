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
    good: Object,
});

const form = useForm({
    _method: "PUT",
    serial: props.good.partnumber,
    price: props.good.price,
    weight: props.good.weight,
    mobis: props.good.mobis,
    korea: props.good.korea,
});

const updateGood = () => {
    form.post(route("goods.update", props.good.id), {
        errorBag: "updateGood",
        preserveScroll: true,
        // onSuccess: () => clearInput(),
    });
};
</script>

<template>
    <AppLayout title="Goods">
        <div>
            <div class="rtl max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
                <FormSection @submitted="updateGood">
                    <template #title> ویرایش جنس </template>

                    <template #description>
                        برای ویرایش جنس انخاب شده مشخصات ذیل را به دقت کامل ویرایش نمایید.
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
                            <TextInput id="weight" v-model="form.weight" type="text" class="mt-1 block w-full"
                                autocomplete="username" />
                            <InputError :message="form.errors.weight" class="mt-2" />
                        </div>
                        <!-- Mobis -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="mobis" value="موبیز" />
                            <TextInput id="mobis" v-model="form.mobis" type="text" class="mt-1 block w-full"
                                autocomplete="username" />
                            <InputError :message="form.errors.mobis" class="mt-2" />
                        </div>
                        <!-- korea -->
                        <div class="col-span-6 sm:col-span-4">
                            <InputLabel for="korea" value="کوریا" />
                            <TextInput id="korea" v-model="form.korea" type="text" class="mt-1 block w-full"
                                autocomplete="username" />
                            <InputError :message="form.errors.korea" class="mt-2" />
                        </div>
                    </template>

                    <template #actions>
                        <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                            عملیات ویرایش موفقانه صورت گرفت.
                        </ActionMessage>

                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            <i class="px-2 material-icons hover:cursor-pointer">swap_vertical_circle</i>
                            ویرایش
                        </PrimaryButton>
                    </template>
                </FormSection>
            </div>
        </div>
    </AppLayout>
</template>
