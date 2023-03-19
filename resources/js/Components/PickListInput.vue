<script setup>
import { defineProps, ref, watch, defineEmits } from 'vue'

defineProps({
    modelValue: {required: true},
    allowedValues: { type: Array, required: true },
})
defineEmits(['update:modelValue']);

const selectedValue = ref(null)

</script>

<template>
    <select 
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        >
            <option v-for="option in allowedValues" :key="option" :value="option">
            {{ option }}
            </option>
    </select>
</template>