<script setup>
 import { useRoute } from 'vue-router';
 import PagamentoPixAxios from '@/components/api/checkout/PagamentoPixAxios.vue';

 const route = useRoute();
 const transacaoId = route.params.id;
</script>

<template>
 <PagamentoPixAxios :transacaoId="transacaoId"/>
</template>

