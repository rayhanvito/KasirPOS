import Brands from '../views/product-manager/brands/index.vue';
import Categories from '../views/product-manager/categories/index.vue';
import Products from '../views/product-manager/products/index.vue';
import Variation from '../views/product-manager/variations/index.vue';

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/brands',
                component: Brands,
                name: 'admin.brands.index',
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: route => "brands",
                    permission: "brands_view",
                }
            },
            {
                path: '/admin/categories',
                component: Categories,
                name: 'admin.categories.index',
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: route => "categories",
                    permission: "categories_view",
                }
            },
            {
                path: '/admin/products',
                component: Products,
                name: 'admin.products.index',
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: route => "products",
                    permission: "products_view",
                }
            },
            {
                path: '/admin/variations',
                component: Variation,
                name: 'admin.variations.index',
                meta: {
                    requireAuth: true,
                    menuParent: "product_manager",
                    menuKey: route => "variations",
                    permission: "products_view",
                }
            },
        ]
    }
];
