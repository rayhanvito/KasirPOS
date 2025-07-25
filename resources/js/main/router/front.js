import Front from "../views/front/layouts/Front.vue";
import Home from '../views/front/Home.vue';
import Categories from '../views/front/Categories.vue';
import Dashboard from '../views/front/dashboard/Dashboard.vue';
import Profile from '../views/front/dashboard/Profile.vue';
import Orders from '../views/front/dashboard/Orders.vue';
import Checkout from '../views/front/dashboard/Checkout.vue';
import CheckoutSuccess from '../views/front/dashboard/CheckoutSuccess.vue';

export default [
    {
        path: '/',
        component: Front,
        children: [
            {
                path: '/store/:warehouse',
                component: Home,
                name: 'front.homepage',
                meta: {
                    isFrontStore: true,
                    requireUnauth: true,
                    menuKey: "homepage",
                }
            },
            {
                path: '/store/:warehouse/categories/:slug*',
                component: Categories,
                name: 'front.categories',
                meta: {
                    isFrontStore: true,
                    requireUnauth: true,
                    menuKey: "categories",
                }
            },
            {
                path: '/store/:warehouse/dashboard',
                component: Dashboard,
                name: 'front.dashboard',
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "dashboard",
                }
            },
            {
                path: '/store/:warehouse/profile',
                component: Profile,
                name: 'front.profile',
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "profile",
                }
            },
            {
                path: '/store/:warehouse/orders',
                component: Orders,
                name: 'front.orders',
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "orders",
                }
            },
            {
                path: '/store/:warehouse/checkout',
                component: Checkout,
                name: 'front.checkout',
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "orders",
                }
            },
            {
                path: '/store/:warehouse/checkout-success/:uniqueId',
                component: CheckoutSuccess,
                name: 'front.checkout.success',
                meta: {
                    isFrontStore: true,
                    requireAuth: true,
                    menuKey: "orders",
                }
            }

        ]
    }
]
