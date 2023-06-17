<script setup>

const props = defineProps({
    existing: Array,
});

</script>

<template>
    <div class="bg-white rounded-lg shadow-md col-span-2">
        <div class="p-3">
            <table class="min-w-full text-left text-sm font-light">
                <thead class="font-medium bg-green-600">
                    <tr>
                        <th scope="col" class="px-3 text-gray-800 py-3">
                            Part Number
                        </th>
                        <th scope="col" class="px-3 text-gray-800 py-3">Price</th>
                        <th scope="col" class="px-3 text-gray-800 py-3">client</th>
                        <th scope="col" class="px-3 text-gray-800 py-3">date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-gray-200" v-for="price in item.result.estelam">
                        <td scope="col" class="px-3 text-gray-800 py-3 break-words">
                          f
                        </td>
                        <td scope="col" class="px-3 text-gray-800 py-3 break-words">
                           f
                        </td>
                        <td scope="col" class="px-3 text-gray-800 py-3">
                            f
                        </td>
                        <td scope="col" class="px-3 text-gray-800 py-3">
                           f
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
