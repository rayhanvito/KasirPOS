// import Admin from '../../common/layouts/Admin.vue';
import SetupAdminApp from "../views/SetupAdminApp.vue";

export default [
    {
        path: '/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: '/admin/setup',
                component: SetupAdminApp,
                name: 'admin.setup_app.index',
                meta: {
                    requireAuth: true,
                    menuParent: "",
                    menuKey: "setup_company",
                }
            }
        ]
    }
]
