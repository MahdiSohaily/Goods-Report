<script setup>
import { onMounted, onUpdated, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormRelation from "@/Components/FormRelation.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import arrangeTime from "../services/timeline.js";
import axios from "axios";

const props = defineProps({
    givenPrice: Array,
    information: Array,
    price: String,
    customer: String,
    completeCode: String,
    partNumber: String,
    notification: String
});

const customer_name = ref(null);
const asked = ref(false);

const form = useForm({
    _method: "POST",
    customer: props.customer,
    partnumber: props.partNumber,
    completeCode: props.completeCode,
    notification: props.notification,
    price: null,
});

const savePrice = () => {
    form.post(route("price.store"), {
        errorBag: "savePrice",
        preserveScroll: true,
        onSuccess: () => {
            form.price = null;
        },
    });
};

const getCustomerName = (id) => {
    axios
        .post("/price/customer/name", {
            id,
        })
        .then(function (response) {
            customer_name.value = (response.data);
        })
        .catch(function (error) {
            console.log(error);
        });
}

const askPrice = () => {
    axios
        .post("/price/ask", {
            partNumber: props.partNumber,
            customer: props.customer
        })
        .then(function (response) {
            asked.value = true;
            setTimeout(() => { asked.value = false; }, 500)
        })
        .catch(function (error) {
            console.log(error);
        });
}

onMounted(() => {
    form.price = props.price;
    getCustomerName(props.customer);
})

onUpdated(() => {
    form.price = props.price;
})

</script>

<template>
    <div class="bg-white rounded-lg shadow-md col-span-2 overflow-auto">
        <div id="search_result" class="p-3">
            <table class=" min-w-full text-sm font-light">
                <thead>
                    <tr class="min-w-full bg-green-600">
                        <td class="text-white bold py-2 px-2 w-28">قیمت</td>
                        <td class="text-white bold py-2 px-2 rtl">مشتری</td>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="givenPrice[0] !== null">
                        <template v-for="price in givenPrice">
                            <template v-if="price.price">
                                <tr class="min-w-full mb-1 hover:cursor-pointer"
                                    :class="price.ordered ? 'bg-red-400' : 'bg-indigo-200'" :data-price='price.price'
                                    @click="price.ordered && $emit('setPrice', price.price)">

                                    <td scope="col" class="text-gray-800 px-2 py-1" :class="price.ordered && 'text-white'">
                                        {{ price.price === null ? 'ندارد' : price.price }}
                                    </td>
                                    <td scope="col" class="text-gray-800 px-2 py-1 rtl" :class="price.ordered && 'text-white'">
                                        {{ price.ordered ? 'قیمت دستوری' : price.name }}
                                    </td>
                                </tr>
                                <tr class="min-w-full mb-1 hover:cursor-pointer border-b-2"
                                    :class="price.ordered ? 'bg-red-500' : 'bg-indigo-300'" :data-price='price.price'>
                                    <td :class="price.ordered && 'text-white'" colspan="3" scope="col"
                                        class="text-gray-800 px-2 tiny-text ">
                                        <div class="rtl flex items-center w-full">
                                            <i class="px-1 material-icons tiny-text "
                                                :class="price.ordered ? 'text-white' : 'text-gray-800'">access_time</i>
                                            {{ arrangeTime(price.created_at) }}
                                        </div>
                                    </td>
                                </tr>
                            </template>

                        </template>
                    </template>
                    <template v-else>
                        <tr class="min-w-full mb-4 border-b-2 border-white">
                            <td colspan="3" scope="col" class="text-gray-800 py-2 text-center bg-indigo-300">
                                موردی برای نمایش وجود ندارد!!
                            </td>
                        </tr>
                    </template>

                </tbody>
            </table>
            <br>
            <FormRelation class="rtl" @submitted="savePrice()">
                <template #form>
                    <div class="pb-2">
                        <InputLabel for="price" value="قیمت" />
                        <TextInput id="price" v-model="form.price" type="text" class="mt-1 block w-full ltr"
                            autocomplete="price" />
                        <InputError :message="form.errors.price" class="mt-2" />
                    </div>

                </template>

                <template #actions>
                    <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        ثبت
                    </PrimaryButton>
                    <PrimaryButton @click="$emit('setPrice', 'نداریم')" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        نداریم !!!
                    </PrimaryButton>
                    <a @click="askPrice"
                        class="hover:cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        ارسال به نیایش
                    </a>
                    <ActionMessage :on="form.recentlySuccessful" class="mr-3">
                        عملیات موفقانه صورت گرفت.
                    </ActionMessage>
                    <ActionMessage :on="asked" class="mr-3">
                        عملیات موفقانه صورت گرفت.
                    </ActionMessage>
                </template>
            </FormRelation>
        </div>
    </div>
</template>
