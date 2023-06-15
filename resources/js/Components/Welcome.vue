<script setup>
import axios from "axios";

defineProps({
    rates: Object,
});

let result = null;

const search = (val, rates) => {
    let pattern = val;
    let superMode = 0;
    const resultBox = document.getElementById("results");

    if (document.getElementById("mode").checked) {
        superMode = 1;
    }

    if (
        (pattern.length > 4 && superMode == 1) ||
        (pattern.length > 6 && superMode == 0)
    ) {
        pattern = pattern.replace(/\s/g, "");
        pattern = pattern.replace(/-/g, "");
        pattern = pattern.replace(/_/g, "");

        resultBox.innerHTML = `<tr class=''>
                <td colspan='14' class='py-10 text-center'> 
                    <img class=' block w-10 mx-auto h-auto' src='/img/loading.png' alt='google'>
                    </td>
            </tr>`;
        axios
            .post("/search", {
                pattern,
                superMode,
            })
            .then(function (response) {
                resultBox.innerHTML = print(response.data, rates);
            })
            .catch(function (error) {
                console.log(error);
            });
    } else {
        resultBox.innerHTML = "";
    }
};

const print = (data, rates) => {
    let template = "";
    if (data[0].length > 0) {
        for (let item of data[0]) {
            const partNumber = item.partnumber;
            let price = item.price;
            let avgPrice = Math.round((price * 110) / 243.5);
            const weight = Number(item.weight).toFixed(1);
            const mobis = item.mobis;
            const korea = item.korea;
            let status;

            if (mobis == "0.00") {
                status = "NO-Price";
            } else if (mobis == "-") {
                status = "NO-Mobis";
            } else if (mobis == null) {
                status = "Request";
            } else {
                status = "YES-Mobis";
            }

            template += `<tr class="transition duration-300 ease-in-out hover:bg-neutral-200">
                <td class='whitespace-nowrap bg-blue-900'>
                <div class='flex gap-1 text-white font-bold'>`;
            if (status == "Request") {
                template +=
                    ` <a class='link-s ml-4 Request' target='_blank' href='mobis/` +
                    partNumber +
                    `'>?</a>`;
            } else if (status == "NO-Price") {
                template +=
                    ` <a class='link-s ml-4 NO-Price' target='_blank' href='mobis/` +
                    partNumber +
                    `'>!</a>`;
            } else if (status == `NO-Mobis`) {
                template +=
                    ` <a class='link-s ml-4 NO-Mobis' target='_blank' href='mobis/` +
                    partNumber +
                    `'>x</a>`;
            } else {
                template += `<span class='ml-11'></span>`;
            }

            template +=
                partNumber +
                `</div></td>
                <td class='whitespace-nowrap text-center px-3 py-3'>` +
                Math.round(avgPrice * 1.1) +
                `</td>
                <td class='orange whitespace-nowrap text-center px-3 py-3 border-black border-r-2' >` +
                Math.round(avgPrice * 1.2) +
                `</td>`;

            template += normalRate(avgPrice, rates);

            template +=
                `
                <td class='whitespace-nowrap w-24'>
                    <div class='flex justify-center gap-1 items-center px-2'>
                    <a target='_blank' href='https://www.google.com/search?tbm=isch&q=` +
                partNumber +
                `'>
                        <img class='w-5 h-auto' src='/img/google.png' alt='google'>
                    </a>
                    <a msg='` +
                partNumber +
                `'>
                        <img class='w-5 h-auto' src='/img/tel.png' alt='part'>
                    </a>
                    <a target='_blank' href='https://partsouq.com/en/search/all?q=` +
                partNumber +
                `'>
                        <img class='w-5 h-auto' src='/img/part.png' alt='part'>
                    </a>
                </div>
                </td>
                <td class='whitespace-nowrap px-3 py-3 kg'>
                    <div class='weight'>` +
                weight +
                ` KG</div>
                </td>
            </tr> `;

            if (status == "YES-Mobis") {
                price = mobis;
                price = price.replace(",", "");
                avgPrice = Math.round((price * 110) / 243.5);
                template +=
                    `<tr class='mobis transition duration-400 ease-in-out hover:bg-neutral-500'>
                        <td class='text-white font-bold pl-12'> ` +
                    partNumber +
                    `-M</td>
                        <td class='font-bold whitespace-nowrap text-center px-3 py-3'>` +
                    Math.round(avgPrice) +
                    `</td>
                        <td class='whitespace-nowrap px-3 py-3 text-center border-black border-r-2'>` +
                    Math.round(avgPrice * 1.1) +
                    `</td>`;
                template += mobisRate(avgPrice, rates);
                template += `<td></td><td></td></tr>`;
            }
            if(korea) {
                price = korea;
                price = price.replace(",", "");
                avgPrice = Math.round((price * 110) / 243.5);
                template +=
                    `<tr class='mobis transition duration-400 ease-in-out bg-amber-600'>
                        <td class='text-white font-bold pl-12'> ` +
                    partNumber +
                    `-K</td>
                        <td class='font-bold whitespace-nowrap text-center px-3 py-3'>` +
                    Math.round(avgPrice) +
                    `</td>
                        <td class='whitespace-nowrap px-3 py-3 text-center border-black border-r-2'>` +
                    Math.round(avgPrice * 1.1) +
                    `</td>`;
                template += mobisRate(avgPrice, rates);
                template += `<td></td><td></td></tr>`;
            }
        }
        return template;
    } else {
        return `<tr v-else class="transition duration-300 ease-in-out bg-neutral-200">
                    <td colspan="14" class="whitespace-nowrap px-3 py-3 text-center font-bold text-orange-500">
                        <i class="material-icons text-red-500">mood_bad</i>
                        <br>
                                !قطعه ای برای  کد فنی وارد در سیستم پیدا نشد
                    </td>
                </tr>`;
    }
};

const normalRate = (avg, rates) => {
    let result = "";

    for (let rate of rates) {
        result +=
            `
            <td class='whitespace-nowrap px-3 py-3 text-center ` +
            rate.status +
            `' > ` +
            Math.round(avg * rate.amount * 1.2 * 1.2 * 1.3) +
            `</td>`;
    }

    return result;
};

const mobisRate = (avg, rates) => {
    let result = "";

    for (let rate of rates) {
        result +=
            `
        <td class='whitespace-nowrap px-3 py-3 text-center b-` +
            rate.status +
            `' > ` +
            Math.round(avg * rate.amount * 1.25 * 1.3) +
            `</td>`;
    }

    return result;
};
</script>

<template>
    <div>
        <div class="p-6 lg:p-8 flex justify-center">
            <input type="text" name="serial" id="serial" class="rounded-md py-3 text-center w-96 border-2 bg-gray-100"
                min="0" max="30" @keyup="search($event.target.value, rates)" placeholder="... کد فنی قطعه را وارد کنید" />
        </div>
        <div class="flex justify-center items-center pb-6">
            <label for="mode" class="px-2">جستجوی پیشرفته</label>
            <input type="checkbox" name="super" id="mode" class="rounded-md " />
        </div>
        <div class="bg-gray-100 bg-opacity-25">
            <div class="max-w-7xl overflow-x-auto mx-auto">
                <table class="min-w-full text-left text-sm font-light">
                    <thead class="font-medium dark:border-neutral-500">
                        <tr class="bg-green-700">
                            <th scope="col" class="px-3 py-3 bg-black text-white w-52 text-center">
                                شماره فنی
                            </th>
                            <th scope="col" class="px-3 py-3 text-white w-20">
                                دلار پایه
                            </th>
                            <th scope="col" class="px-3 py-3 text-white border-black border-r-2">
                                +10%
                            </th>

                            <!-- START TO Loop over the existing rates at database -->
                            <th v-for="item in rates" :class="item.status" scope="col"
                                class="px-3 py-3 text-white text-center">
                                {{ item.amount }}
                            </th>
                            <!-- END the loop -->

                            <th scope="col" class="px-3 py-3 text-white w-32 text-center">
                                عملیات
                            </th>
                            <th scope="col" class="px-3 py-3 text-white">
                                وزن
                            </th>
                        </tr>
                    </thead>
                    <tbody id="results">
                        {{
                            result ? result : ""
                        }}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
