import SuperAdmin from '../layouts/SuperAdmin.vue';
import EmailQueries from '../views/email-queries/index.vue';

export default [
    {
        path: '/',
        component: SuperAdmin,
        children: [
            {
                path: '/superadmin/email-queries',
                component: EmailQueries,
                name: 'superadmin.email-queries.index',
                meta: {
                    requireAuth: true,
                    menuParent: "email_queries",
                    menuKey: route => "email_queries",
                    permission: 'superadmin'
                }
            }
        ]
    }
];
