<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import SectionBorder from "@/Components/SectionBorder.vue";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormRelation from "@/Components/FormRelation.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
defineProps({
    status: Object,
    cars: Object,
});

$(document).ready(() => {
    $(document).on("click", ".add_element", add);
    $(document).on("click", ".load_element", load);
});

const form = useForm({
    _method: "POST",
    name: null,
    serial: null,
    price: null,
    status_id: [],
    pattern_id: null,
    car_id: null,
    values: [],
});

let result = null;
let url = "relations.store";

/***  This function fires on every keyup event on a specific input field and at a specific length
 *    sends an ajax request using the axios library.
 */
const search = (val) => {
    let pattern = val;
    const resultBox = document.getElementById("search_result");

    if (pattern.length > 6) {
        pattern = pattern.replace(/\s/g, "");
        pattern = pattern.replace(/-/g, "");
        pattern = pattern.replace(/_/g, "");

        form.serial = pattern;

        resultBox.innerHTML = `<tr class=''>
                <div class='w-full h-96 flex justify-center items-center'>
                    <img class=' block w-10 mx-auto h-auto' src='/img/loading.png' alt='google'>
                    </div>
            </tr>`;
        axios
            .post(route("relations.search"), {
                pattern,
            })
            .then(function (response) {
                resultBox.innerHTML = prepareData(response.data);
                result = response.data;
            })
            .catch(function (error) {
                console.log(error);
            });
    } else {
        resultBox.innerHTML = "";
    }
};

/**
 * This function prepares data to be attached and returns it.
 */
const prepareData = (data) => {
    let template = "";
    if (data.length > 0) {
        for (let item of data) {
            if (item.pattern) {
                template +=
                    `<div class='w-full flex justify-between items-center shadow-md hover:shadow-lg rounded-md px-4 py-3 mb-2 border-1 border-gray-300' id='search-` +
                    item.id +
                    `'>
                    <p class='text-sm font-semibold text-gray-600'>` +
                    item.partNumber +
                    `</p>
                    <i 
                        data-id='` +
                    item.id +
                    `'
                        data-pattern='` +
                    item.pattern +
                    `'
                        data-partNumber='` +
                    item.partNumber +
                    `'
                        class='load_element material-icons add text-blue-600 cursor-pointer rounded-circle hover:bg-gray-200'>cloud_download
                    </i>
                </div>`;
            } else {
                template +=
                    `<div class='w-full flex justify-between items-center shadow-md hover:shadow-lg rounded-md px-4 py-3 mb-2 border-1 border-gray-300' id='search-` +
                    item.id +
                    `'>
                    <p class='text-sm font-semibold text-gray-600'>` +
                    item.partNumber +
                    `</p>
                    <i
                        data-id='` +
                    item.id +
                    `'
                        data-partNumber='` +
                    item.partNumber +
                    `'
                        class='add_element material-icons add text-green-600 cursor-pointer rounded-circle hover:bg-gray-200'>add_circle_outline
                    </i>
                </div>`;
            }
        }
    } else {
        template += `<div class='w-full text-center shadow-md hover:shadow-lg rounded-md px-4 py-3 mb-2 border-1 border-gray-300''>
                <i class='material-icons text-red-500'>error</i>
                <br/>
                <p class='text-sm font-semibold text-gray-600 text-red-500'> Nothing to show (<span class="text-sm">Check the part number</span>)</p>
            </div>`;
    }

    return template;
};

// A function to add a good to the relation box
const add = (e) => {
    const id = e.target.getAttribute("data-id");
    const partNumber = e.target.getAttribute("data-partnumber");
    form.values.push({ id: id, partNumber: partNumber });
    remove(id);
};

const remove_selected = (e) => {
    const id = e.target.getAttribute("data-id");
    form.values = form.values.filter((item) => {
        return item.id != id;
    });
};

const clearAll = () => {
    form._method = "POST";
    form.pattern_id = null;
    form.name = null;
    form.price = null;
    form.status_id = null;
    form.car_id = [];
    form.values = [];
};

// A function to load data a good to the relation box
function load(e) {
    const pattern = e.target.getAttribute("data-pattern");
    if (!form.pattern_id) {
        form.pattern_id = pattern;
        axios
            .post(route("relations.load"), {
                pattern,
            })
            .then(function (response) {
                push_data(response.data);
                form._method = "PUT";
            })
            .catch(function (error) {
                console.log(error);
            });
        load_pattern_ifo(form.pattern_id);
    } else {
        alert(
            "شما همزمان نمی توانید ۲ رابطه را بارگذاری نمایید.(شما میتوانید با حذف همه رابطه جدید را وارد نمایید)"
        );
    }
}

const push_data = (data) => {
    for (const item of data) {
        remove(item.id);
        form.values.push({ id: item.id, partNumber: item.partNumber });
    }
};

const load_pattern_ifo = (id) => {
    axios
        .post(route("relations.pattern"), {
            id,
        })
        .then(function (response) {
            form.name = response.data.pattern.name;
            form.price = response.data.pattern.price;
            form.car_id = response.data.cars;
            form.status_id = response.data.pattern.status_id;
        })
        .catch(function (error) {
            console.log(error);
        });
};

// A function to remove added goods from relation box
function remove(id) {
    const item = document.getElementById("search-" + id);
    if (item) {
        item.remove();
    }
}

const createRelation = () => {
    form.post(route(url), {
        preserveScroll: true,
        onSuccess: (response) => {
            console.log(response);
            clearAll();
        },
    });
};
</script>

<template>
    <AppLayout title="Relations">
        <div class="rtl h-70S grid grid-cols-1 my-8 md:grid-cols-3 gap-6 lg:gap-8 p-6 lg:p-8">
            <div class="bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between p-3">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="material-icons font-semibold text-orange-400">search</i>
                        جستجوی اجناس
                    </h2>
                </div>

                <div class="flex justify-center px-3">
                    <input type="text" name="serial" id="serial"
                        class="rounded-md py-3 w-full border-1 text-sm border-gray-300 focus:outline-none text-gray-500"
                        min="0" max="30" @keyup="search($event.target.value, rates)" placeholder="شماره فنی ..." />
                </div>
                <SectionBorder />
                <div id="search_result" class="p-3">
                    <!-- Search Results are going to be appended here -->
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md">
                <div class="flex items-center justify-between p-3">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="material-icons text-green-600">beenhere</i>
                        اجناس انتخاب شده
                    </h2>
                    <button
                        class="flex items-center border-none bg-red-500 hover:bg-red-600 text-white rounded-lg px-4 py-2 text-sm"
                        @click="clearAll">
                        <i class="px-2 material-icons hover:cursor-pointer">delete</i>
                        حذف همه
                    </button>
                </div>
                <p class="px-3 mb-4 text-gray-500 text-sm leading-relaxed">
                    لیست اجناس انتخاب شده برای افزودن به رابطه!
                </p>
                <SectionBorder />

                <div id="selected_box" class="p-3">
                    <!-- selected items are going to be added here -->
                    <div v-if="form.values.length > 0" v-for="item in form.values"
                        class="w-full flex justify-between items-center shadow-md hover:shadow-lg rounded-md px-4 py-3 mb-2 border-1 border-gray-300">
                        <p class="text-sm font-semibold text-gray-600">
                            {{ item.partNumber }}
                        </p>
                        <i :data-id="item.id" :data-partNumber="item.partNumber" @click="remove_selected"
                            class="material-icons add text-red-600 cursor-pointer rounded-circle hover:bg-gray-200">do_not_disturb_on
                        </i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md">
                <div class="p-3">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center gap-2">
                        <i class="material-icons font-semibold text-blue-500">save</i>
                        ثبت رابطه در سیستم
                    </h2>
                </div>

                <p class="px-3 py-1 mb-4 text-gray-500 text-sm leading-relaxed">
                    برای ثبت رایطه در سیستم فورم ذیل را با دقت پر نمایید.
                </p>

                <SectionBorder />

                <div class="p-3">
                    <FormRelation @submitted="createRelation">
                        <template #form>
                            <!-- Name -->
                            <div class="pb-2">
                                <InputLabel for="name" value="اسم رابطه" />
                                <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full ltr"
                                    autocomplete="name" />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>
                            <div class="pb-2">
                                <InputLabel for="price" value="قیمت" />
                                <TextInput id="price" v-model="form.price" type="text" class="mt-1 block w-full ltr"
                                    autocomplete="price" />
                                <InputError :message="form.errors.price" class="mt-2" />
                            </div>
                            <div class="pb-2">
                                <InputLabel for="cars" value="خودرو های مرتبط" />
                                <select type="cars" multiple
                                    class="mt-1 block w-full border-gray-300 ltr focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    autocomplete="username" v-model="form.car_id" id="cars">
                                    <option v-for="item in cars" :value="item.id" class="text-sm">
                                        {{ item.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.car_id" class="mt-2" />
                            </div>
                            <div class="pb-2">
                                <InputLabel for="status" value="وضعیت" />
                                <select type="status"
                                    class="mt-1 block w-full ltr border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    autocomplete="username" v-model="form.status_id" id="status">
                                    <option v-for="item in status" :value="item.id" class="text-sm">
                                        {{ item.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.status_id" class="mt-2" />
                            </div>
                            <!-- <div class="container">
                                        <select multiple data-multi-select-plugin autocomplete="username" v-model="form.car_id"
                                            id="cars">
                                            <option v-for="item in cars" :value="item.id" class="text-sm"
                                                :selected="form.car_id != null && form.car_id.includes(item.id)">
                                                {{ item.name }}
                                            </option>
                                        </select>
                                    </div>
                                     -->
                            <InputError :message="form.errors.values" class="mt-2" />
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
                    </FormRelation>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
