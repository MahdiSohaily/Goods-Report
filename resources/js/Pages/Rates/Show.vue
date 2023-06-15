<script setup>
import { ref } from 'vue';
import { router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    rates: Object,
});

const confirmingDeletion = ref(false);
const deletId = ref(null);

const confirmDeletion = (e) => {
    confirmingDeletion.value = true;
    deletId.value = e.target.getAttribute('data-id');
};

const closeModal = () => {
    confirmingDeletion.value = false;
};

const remove = () => {
    router.delete(route("rates.delete", deletId.value));
    confirmingDeletion.value = false;
};

const toggleSelected = (e) => {
    const value = e.target.checked;
    const id = e.target.getAttribute('data-id');
    axios
        .post("/rate/toggle/selected", {
            value,
            id
        })
        .then(function (response) {

        })
        .catch(function (error) {
            console.log(error);
        });
}
</script>

<template>
    <AppLayout title="Rates">
        <div class="rtl max-w-5xl mx-auto py-20 sm:px-6 lg:px-8">
            <div class="mb-3">
                <a :href="route('rates.create')"
                    class=" flex items-center w-52 bg-indigo-500 hover:bg-indigo-400 rounded-md text-white px-4 py-2">
                    <i class="px-2 material-icons hover:cursor-pointer">add_circle_outline</i>
                    ثبت نرخ ارز جدید</a>
            </div>
            <table class="min-w-full text-left text-sm font-light">
                <thead class="font-medium dark:border-neutral-500">
                    <tr class="bg-green-700">
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            نرخ ارز
                        </th>
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            شاخص نرخ
                        </th>
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            زنگ شاخص
                        </th>
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            انتخاب به عنوان پیش فرض
                        </th>
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            عملیات
                        </th>
                    </tr>
                </thead>
                <tbody id="results">
                    <tr v-if="rates.length > 0" v-for="item in rates"
                        class="transition duration-300 ease-in-out bg-neutral-200 hover:bg-neutral-100">
                        <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                            {{ item.amount }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                            {{ item.status }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-3 text-center font-bold" :class="item.status">
                            {{}}
                        </td>
                        <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                            <input type="checkbox" name="selected" id="selected" :checked="item.selected" :data-id="item.id"
                                @click="toggleSelected">
                        </td>
                        <td class="whitespace-nowrap w-24">
                            <div class="flex justify-center gap-1 items-center px-2">
                                <a :href="route('rates.edit', item.id)">
                                    <i class="material-icons text-blue-500">create</i>
                                </a>

                                <button @click="confirmDeletion" type="submit" :data-id="item.id">
                                    <i :data-id="item.id" class="material-icons text-red-600">delete_forever</i>
                                </button>

                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <DialogModal :show="confirmingDeletion" @close="closeModal">
            <template #title>
                حذف نرخ ارز مورد نظر
            </template>

            <template #content>
                آیا مطمئن هستید که می خواهید نرخ ارز مورد نظر را حذف کنید؟ پس از حذف نرخ ارز مورد نظر دیگر در دسترس نخواهد بود.
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    لغو
                </SecondaryButton>
                <DangerButton class="ml-3" @click="remove">
                    حذف
                </DangerButton>

            </template>
        </DialogModal>
    </AppLayout>
</template>
