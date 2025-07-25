// import Admin from '../../common/layouts/Admin.vue';
import ProductCards from '../views/website-setup/product-cards/index.vue';
import FrontSettings from '../views/website-setup/front-settings/Edit.vue';

export default [
    {
        path: '/admin/website-setup/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: 'product-cards',
                component: ProductCards,
                name: 'admin.website-setup.product-cards.index',
                meta: {
                    requireAuth: true,
                    menuParent: "website_setup",
                    menuKey: route => "product-cards",
                    permission: "product_cards_view"
                }
            },
            {
                path: 'front-settings',
                component: FrontSettings,
                name: 'admin.website-setup.front-settings.edit',
                meta: {
                    requireAuth: true,
                    menuParent: "website_setup",
                    menuKey: route => "front-settings",
                    permission: "front_settings_edit"
                }
            },

        ]

    }
]
