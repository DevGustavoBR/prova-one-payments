<script setup>

const props = defineProps({
  error: {type: String,required: true}
});


</script>


<template>
<div v-if="props.error" class="bg-gray-50 text-gray-800 p-8 w-full rounded-lg font-[sans-serif] max-w-4xl mx-auto py-36">
      <h1 class="text-4xl font-extrabold  text-center">{{ props.error }}</h1>
    </div>
</template>
