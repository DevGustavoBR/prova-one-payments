

<script setup>
import { useRoute } from 'vue-router';
import MetodoAxios from '@/components/api/checkout/MetodoAxios.vue';


const route = useRoute();
console.log(route.params.id);
const produtoId = route.params.id
</script>


<template>
  <MetodoAxios :produtoId="produtoId" />
</template>
