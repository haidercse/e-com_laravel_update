import { createRouter, createWebHistory } from 'vue-router';
import MainLayout from '../layouts/MainLayout.vue';
import HomeView from '../views/HomeView.vue';
import CategoriesView from '../views/CategoriesView.vue';
import CategoryProductsView from '../views/CategoryProductsView.vue';
import ProductDetailView from '../views/ProductDetailView.vue';
import AccountView from '../views/AccountView.vue';
import SearchView from '../views/SearchView.vue';
import CheckoutView from '../views/CheckoutView.vue';
import ChatView from '../views/ChatView.vue';
import DeliveryView from '../views/DeliveryView.vue';
import ShopView from '../views/ShopView.vue';
import PolicyView from '../views/PolicyView.vue';
import CatalogListView from '../views/CatalogListView.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: MainLayout,
      children: [
        { path: '', name: 'home', component: HomeView },
        { path: 'categories', name: 'categories', component: CategoriesView },
        {
          path: 'categories/:id',
          name: 'category',
          component: CategoryProductsView,
        },
        { path: 'products/:id', name: 'product', component: ProductDetailView },
        { path: 'search', name: 'search', component: SearchView },
        { path: 'account', name: 'account', component: AccountView },
        { path: 'checkout', name: 'checkout', component: CheckoutView },
        { path: 'messages', name: 'messages', component: ChatView },
        { path: 'delivery', name: 'delivery', component: DeliveryView },
        { path: 'shops', name: 'shops', component: ShopView },
        { path: 'shops/:id', name: 'shop', component: ShopView },
        { path: 'policies/:type', name: 'policy', component: PolicyView },
        { path: 'brands/:id', name: 'brand-products', component: CatalogListView },
        { path: 'deals', name: 'today-deals', component: CatalogListView },
        { path: 'featured', name: 'featured-products', component: CatalogListView },
        { path: 'best-sellers', name: 'best-sellers', component: CatalogListView },
      ],
    },
    { path: '/:pathMatch(.*)*', redirect: '/' },
  ],
  scrollBehavior: () => ({ top: 0 }),
});

export default router;
