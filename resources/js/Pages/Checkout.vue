<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import PickListInput from '@/Components/PickListInput.vue';
import { watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3';

const paymentMethods = [
  'OnDelivery',
  'Card',
  'Paypal'
]

const form = useForm({
  'cart-id': 1,// hardcoded! Should be dynamic
  'full-name': null,
  tel: null,
  email: null,
  'payment-method': paymentMethods[0],
  'card-number': null,
  cvv: null,
  'card-expiration-date': null
})

watch(() => form['payment-method'], (newValue, oldValue) => {
  if (newValue !== 'Card') {
    form['card-number'] = null;
    form.cvv = null;
    form['card-expiration-date'] = null
  }
});

</script>

<template>
  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Checkout</h2>
    </template>
    <div class="flex flex-col justify-center items-center">
      <form @submit.prevent="form.post('/api/checkout')" class="w-1/3">
        <!-- full name -->
        <div class="p-2">
          <InputLabel for="full-name" value="Full Name" />
          <TextInput id="full-name" type="text" class="mt-1 block w-full" v-model="form['full-name']" required autofocus
            autocomplete="name" />
          <InputError class="mt-2" :message="form.errors['full-name']" />
        </div>
        <!-- email -->
        <div class="p-2">
          <InputLabel for="email" value="Email" />
          <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
            autocomplete="email" />
          <InputError class="mt-2" :message="form.errors.email" />
        </div>
        <!-- full name -->
        <div class="p-2">
          <InputLabel for="tell" value="Tel." />
          <TextInput id="tel" type="text" class="mt-1 block w-full" v-model="form.tel" required 
            autocomplete="tel" />
          <InputError class="mt-2" :message="form.errors.tel" />
        </div>
        <!-- payment method -->
        <div class="p-2">
          <InputLabel for="payment-method" value="Payment Methods" />
          <PickListInput
            id="payment-method"
            :allowed-values="paymentMethods"
            v-model="form['payment-method']" />
        </div>
        <!-- card related data -->
        <div v-if="form['payment-method'] === 'Card'">
          <!-- card number -->
          <div class="p-2">
            <InputLabel for="card-number" value="Bank Card Number" />
            <TextInput id="card-number" type="text" class="mt-1 block w-full" v-model="form['card-number']" required 
              autocomplete="Card Number"
              maxlength="19" />
            <InputError class="mt-2" :message="form.errors['card-number']" />
          </div>
          <!-- CVV -->
          <div class="p-2">
            <InputLabel for="cvv" value="CVV" />
            <TextInput id="cvv" type="number" class="mt-1 block w-full" v-model="form.cvv" required 
              autocomplete="CVV"
              />
            <InputError class="mt-2" :message="form.errors.cvv" />
          </div>
          <!-- Card Expiration Date -->
          <div class="p-2">
            <InputLabel for="card-expiration-date" value="Card Expiration Date" />
            <TextInput id="card-expiration-date" 
              type="text" 
              class="mt-1 block w-full" 
              v-model="form['card-expiration-date']" 
              required
              />
            <InputError class="mt-2" :message="form.errors['card-expiration-date']" />
          </div>
        </div>
        <!-- submit -->
        <PrimaryButton class="ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          Submit
        </PrimaryButton>
      </form>
    </div>
  </AuthenticatedLayout>
</template>