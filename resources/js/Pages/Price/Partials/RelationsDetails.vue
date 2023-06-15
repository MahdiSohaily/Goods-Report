<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    rates: Array,
    relation: Array,
});

const stockRecords = ref(null);
const copied_icon = ref();
const copied = ref(false);

const seekExist = (e) => {
    const element = e.target;
    if (element.hasAttribute("data-key")) {
        const partNumber = element.getAttribute('data-key');
        const brand = element.getAttribute('data-brand');

        const target = document.getElementById(partNumber + '-' + brand)
        target.style.display = 'block';
    }
}

const closeSeekExist = (e) => {
    const element = e.target;
    if (element.hasAttribute("data-key")) {
        const partNumber = element.getAttribute('data-key');
        const brand = element.getAttribute('data-brand');

        const target = document.getElementById(partNumber + '-' + brand)
        target.style.display = 'none';
    }

}

const calculateMobies = (price, rate) => {
    return Math.round(
        Math.round((price * 110) / 243.5) *
        rate *
        1.25 *
        1.3
    )
}

const calculateRegular = (price, rate) => {

    return Math.round(
        Math.round((price * 110) / 243.5) *
        rate *
        1.2 *
        1.2 *
        1.3
    )
}

function myFunction(e) {
    const copyText = e.target;
    navigator.clipboard.writeText(copyText.getAttribute('data-copy'));
    copied.value = true;
    setTimeout(() => {
        copied.value = false;
    }, 500)
}


onMounted(() => {
    stockRecords.value = props.relation.stockInfo;
})
</script>

<template>
    <div class="bg-white rounded-lg col-span-5 overflow-auto">
        <div id="search_result" class="p-3">
            <table class="min-w-full text-left text-sm font-light custom-table">
                <thead class="font-medium bg-green-600">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            شماره فنی
                        </th>
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            مقدار موجودی
                        </th>
                        <th scope="col" class="px-3 py-3 text-white text-center">
                            قیمت به اساس نرخ ارز
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="relative" v-for="element, key of props.relation.sorted">
                        <td class=" px-1">
                            <p class="text-center bold bg-gray-600 text-white px-2 py-3">
                                {{ props.relation.goods[key].partnumber }}
                            </p>

                        </td>
                        <td class="px-1 pt-2">
                            <table class="min-w-full text-sm font-light p-2">
                                <thead class="font-medium">
                                    <tr>
                                        <th v-for="goodAmount, index in  props.relation.existing[key]" scope="col"
                                            :class="index == 'GEN' || index == 'MOB' ? index : 'brand-default'"
                                            class="text-white text-center py-2 relative hover:cursor-pointer"
                                            :data-key="key" :data-brand="index" @mouseover="seekExist"
                                            @mouseleave="closeSeekExist">
                                            {{ index }}
                                            <div class="custome-tooltip" :id="key + '-' + index">
                                                <div v-for="item, iterator in props.relation.stockInfo[key][index]">
                                                    <p v-if="item > 0"> {{ iterator }} : {{ item }}</p>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="py-3">
                                        <td :class="index == 'GEN' || index == 'MOB' ? index : 'brand-default'"
                                            class="whitespace-nowrap text-white px-3 py-2 text-center"
                                            v-for="goodAmount, index in  props.relation.existing[key]">
                                            {{ goodAmount }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td class="px-1 pt-2">
                            <table class="min-w-full text-left text-sm font-light">
                                <thead class="font-medium">
                                    <tr>
                                        <th v-for="rate in rates" scope="col" class="text-gray-800 text-center py-2"
                                            :class="rate.status !== 'N' ? rate.status : 'bg-green-700'">
                                            {{ rate.amount }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="py-3">
                                        <td class="text-bold whitespace-nowrap px-3 py-2 text-center hover:cursor-pointer"
                                            :class="rate.status !== 'N' ? rate.status : 'bg-gray-100'" v-for="rate in rates"
                                            @click="$emit('setPrice', calculateRegular(props.relation.goods[key].price, rate.amount))">

                                            {{
                                                calculateRegular(props.relation.goods[key].price, rate.amount)
                                            }}
                                        </td>
                                    </tr>
                                    <tr class="bg-neutral-400"
                                        v-if="props.relation.goods[key].mobis > 0 && props.relation.goods[key].mobis !== '-'">
                                        <td class="text-bold whitespace-nowrap px-3 text-center py-2 hover:cursor-pointer"
                                            v-for="rate in rates"
                                            @click="$emit('setPrice', calculateMobies(props.relation.goods[key].price, rate.amount))">
                                            {{
                                                calculateMobies(props.relation.goods[key].price, rate.amount)
                                            }}
                                        </td>
                                    </tr>

                                    <tr class="bg-amber-600" v-if="props.relation.goods[key].korea > 0">
                                        <td class="text-bold whitespace-nowrap px-3 text-center py-2 hover:cursor-pointer"
                                            v-for="rate in rates"
                                            @click="$emit('setPrice', calculateMobies(props.relation.goods[key].price, rate.amount))">
                                            {{
                                                calculateMobies(props.relation.goods[key].price, rate.amount)

                                            }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
.custome-tooltip {
    position: absolute;
    display: none;
    bottom: 100%;
    padding: 10px;
    border-radius: 5px;
    background-color: seagreen;
    width: 200px;
    z-index: 100000;
}

.custom-table td {
    vertical-align: super;
}
</style>
