<script setup>
import arrangeTime from "../services/timeline.js";

const props = defineProps({
    estelam: Array,
});

</script>

<template>
    <div class="bg-white rounded-lg shadow-md col-span-2">
        <div class="p-3">
            <table class=" min-w-full text-sm font-light">
                <thead>
                    <tr class="min-w-full bg-green-600">
                        <td class="text-white bold py-2 px-2 w-28">قیمت</td>
                        <td class="text-white bold py-2 px-2 rtl">مشتری</td>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="estelam[0] !== null">
                        <template v-for="price in estelam">
                            <template v-if="price.price">
                                <tr class="min-w-full mb-1 hover:cursor-pointer bg-indigo-200" :data-price='price.price'
                                    @click="$emit('setPrice', price.price)">

                                    <td scope="col" class="text-gray-800 px-2 py-1">
                                        {{ price.price === null ? 'ندارد' : price.price }}
                                    </td>
                                    <td scope="col" class="text-gray-800 px-2 py-1 rtl">
                                        {{ price.name }}
                                    </td>
                                </tr>
                                <tr class="min-w-full mb-1 hover:cursor-pointer border-b-2 bg-indigo-300"
                                    :data-price='price.price'>
                                    <td colspan="3" scope="col" class="text-gray-800 px-2 tiny-text ">
                                        <div class="rtl flex items-center w-full">
                                            <i class="px-1 material-icons tiny-text ">access_time</i>
                                            {{ arrangeTime(price.time) }}
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
        </div>
    </div>
</template>
