<script setup>
defineProps({
    rates: Object,
});

const form = useForm({
    _method: "GET",
    customer: null,
    code: null,
});

const LoadPrice = () => {
    form.post(route("price.load"), {
        errorBag: "LoadPrice",
        preserveScroll: true,
        onSuccess: () => clearInput(),
    });
};

const clearInput = () => { };
</script>

<template>
    <div class="w-96 my-96 mx-auto text-center h-40 bg-white rounded-lg lg:p-2 flex justify-center items-center">
        <p>No result ! {{ item.search }}</p>
    </div>
</template>
