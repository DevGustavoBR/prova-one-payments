import {createWebHistory, createRouter} from 'vue-router'

import Produtos from '../views/ProdutosPage.vue';
import Transacoes from '../views/TransacoesPage.vue';
import Produto from '../views/ProdutoPage.vue';
import MetodoPagamento from '../views/checkout/MetodoPagamentoPage.vue';
import PixPage from '../views/checkout/pix/PixPage.vue';
import CartaoCreditoPage from '../views/checkout/CartaoCreditoPage.vue';
import TransacaoDetalhesPage from '../views/TransacaoDetalhesPage.vue';

const routes = [
  { path: '/', component: Produtos, name: 'produtos' },
  { path: '/produto/:id', component: Produto, name: 'produto'},
  { path: '/transacoes', component: Transacoes, name: 'transacoes' },
  { path: '/transacao/:id', component: TransacaoDetalhesPage, name: 'detalhes'},
  { path: '/checkout/metodo-pagamento/:id', component: MetodoPagamento, name: 'metodoPagamento'},
  { path: '/checkout/pagamento/pix/:id', component: PixPage, name: 'pixPagamento'},
  { path: '/checkout/pagamento/cartao-credito/:id', component: CartaoCreditoPage, name: 'cartaoCreditoPagamento'}
]



const router = createRouter({
  history: createWebHistory(),
  routes,
})


export default router;
