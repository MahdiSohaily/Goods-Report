<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Container from './Container.vue';

const props = defineProps({
  explodedCodes: Array,
  not_exist: Array,
  existing: Array,
  customer: String,
  completeCode: String,
  notification: String,
  rates: Array,
});

</script>

<template>
  <AppLayout title="Rates">
    <div class="accordion mt-12">
      <!--  Panel 1  -->
      <div v-for="code, code_index of explodedCodes">

        <template v-if="existing[code] && Object.keys(existing[code]).length">
          <input type="checkbox" checked="true" name="panel" :id="code_index" class="hidden">
          <label :for="code_index"
            class="relative flex items-center bg-gray-700 text-white p-4 shadow border-b border-grey hover:cursor-pointer">
            {{ code }}
          </label>
          <div class="accordion__content overflow-hidden bg-grey-lighter">
            <div v-for="item, index of existing[code]" class="grid grid-cols-1 md:grid-cols-10 gap-6 lg:gap-2 lg:p-2">
              <Container :code="code" :index="index" :existing="existing"
                :information="existing[code][index]['information']" :partNumber="index" :rates="props.rates"
                :relation="existing[code][index]['relation']" :exist="existing[code][index]['exist']"
                :notification="notification" :givenPrice="existing[code][index]['givenPrice']" :customer="customer"
                :completeCode="completeCode" />
            </div>
          </div>
        </template>
        <template v-if="not_exist.indexOf(code) > -1">
          <input type="checkbox" checked="true" name="panel" :id="code_index" class="hidden">
          <label :for="code_index"
            class="relative flex items-center bg-gray-700 text-white p-4 shadow border-b border-grey hover:cursor-pointer">
            {{ code }}
          </label>
          <div class="accordion__content overflow-hidden bg-grey-lighter">

            <div class="bg-white rounded-lg px-3 py-10">
              <p class="text-center">
                کد وارد شده در سیستم موجود نیست
              </p>
            </div>
          </div>
        </template>


      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
label:after {
  content: '+';
  position: absolute;
  right: 1em;
  color: #fff;
}

input:checked+label:after {
  content: '-';
  line-height: .8em;
}

.accordion__content {
  max-height: 0em;
  transition: all 0.4s cubic-bezier(0.865, 0.14, 0.095, 0.87);
}

input[name='panel']:checked~.accordion__content {
  /* Get this as close to what height you expect */
  max-height: 150000em;
}
</style>
