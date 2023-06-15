<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
defineProps({ rates: Object, result: Array });
</script>

<template>
    <AppLayout title="Mobis">
        <div class="py-20">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div>
                    <div class="bg-gray-100 bg-opacity-25">
                        <div class="max-w-7xl overflow-x-auto mx-auto">
                            <table class="min-w-full text-left text-sm font-light">
                                <thead class="font-medium dark:border-neutral-500">
                                    <tr class="bg-green-700">
                                        <th scope="col" class="px-3 py-3 bg-black text-white w-52 text-center">
                                            شماره فنی
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-white">
                                            دلار پایین
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-white">
                                            دلار میانگین
                                        </th>
                                        <th scope="col" class="px-3 py-3 text-white border-black border-r-2">
                                            دلار بالا
                                        </th>

                                        <!-- START TO Loop over the existing rates at database -->
                                        <th v-for="item in rates" :class="item.status" scope="col"
                                            class="px-3 py-3 text-white text-center">
                                            {{ item.amount }}
                                        </th>
                                        <!-- END the loop -->

                                        <th scope="col" class="px-3 py-3 text-white w-52 text-center">
                                            عملیات
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="result.length > 0" class="transition duration-300 ease-in-out bg-neutral-300">
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            {{ result["partNumber"] }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            {{
                                                Math.round(
                                                    result["avgPrice"] / 1.1
                                                )
                                            }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center font-bold">
                                            {{ Math.round(result["avgPrice"]) }}
                                        </td>
                                        <td class="whitespace-nowrap px-3 py-3 text-center">
                                            {{
                                                Math.round(
                                                    result["avgPrice"] * 1.1
                                                )
                                            }}
                                        </td>
                                        <td v-for="item in rates" class="whitespace-nowrap px-3 py-3">
                                            {{
                                                Math.round(
                                                    result["avgPrice"] *
                                                    item.amount *
                                                    1.25 *
                                                    1.3
                                                )
                                            }}
                                        </td>
                                        <td class="whitespace-nowrap w-24">
                                            <div class="flex justify-center gap-1 items-center px-2">
                                                <a target="_blank" :href="
                                                    'https://www.google.com/search?tbm=isch&q=' +
                                                    result['partNumber']
                                                ">
                                                    <img class="w-5 h-auto" src="/img/google.png" alt="google" />
                                                </a>
                                                <a msg="partNumber">
                                                    <img class="w-5 h-auto" src="/img/tel.png" alt="part" />
                                                </a>
                                                <a target="_blank" :href="
                                                    'https://www.google.com/search?tbm=isch&q=' +
                                                    result['partNumber']
                                                ">
                                                    <img class="w-5 h-auto" src="/img/part.png" alt="part" />
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-else class="transition duration-300 ease-in-out bg-neutral-200">
                                        <td colspan="14"
                                            class="whitespace-nowrap px-3 py-3 text-center text-red-500 font-bold">
                                            <i class="material-icons text-red-500">mood_bad</i>
                                            <br />
                                            !این قطعه فاقد موبیز می باشد
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
