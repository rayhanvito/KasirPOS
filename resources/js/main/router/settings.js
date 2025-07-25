// import Admin from '../../common/layouts/Admin.vue';
import CompanyEdit from '../views/settings/company/Edit.vue';
import ProfileEdit from '../views/settings/profile/Edit.vue';
import Langs from '../views/settings/translations/langs/index.vue';
import Warehouse from '../views/settings/warehouses/index.vue';
import Roles from '../views/settings/roles/index.vue';
import Taxes from '../views/settings/taxes/index.vue';
import Currencies from '../views/settings/currency/index.vue';
import Units from '../views/settings/units/index.vue';
import PaymentModes from '../views/settings/payment-modes/index.vue';
import CustomFields from '../views/settings/custom-fields/index.vue';
import CommonAdminSettings from "./common/adminSettings";

export default [
    {
        path: '/admin/settings/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: 'company',
                component: CompanyEdit,
                name: 'admin.settings.company.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "company",
                    permission: "companies_edit"
                }
            },
            {
                path: 'profile',
                component: ProfileEdit,
                name: 'admin.settings.profile.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "profile",
                }
            },
            {
                path: 'langs',
                component: Langs,
                name: 'admin.settings.langs.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "translations",
                    permission: "translations_view"
                }
            },
            {
                path: 'warehouses',
                component: Warehouse,
                name: 'admin.settings.warehouses.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "warehouses",
                    permission: "warehouses_view"
                }
            },
            {
                path: 'roles',
                component: Roles,
                name: 'admin.settings.roles.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "roles",
                    permission: "roles_view"
                }
            },
            {
                path: 'taxes',
                component: Taxes,
                name: 'admin.settings.taxes.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "taxes",
                    permission: "taxes_view"
                }
            },
            {
                path: 'currencies',
                component: Currencies,
                name: 'admin.settings.currencies.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "currencies",
                    permission: "currencies_view"
                }
            },
            {
                path: 'payment-modes',
                component: PaymentModes,
                name: 'admin.settings.payment_modes.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "payment_modes",
                    permission: "payment_modes_view"
                }
            },
            {
                path: 'units',
                component: Units,
                name: 'admin.settings.units.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "units",
                    permission: "units_view"
                }
            },
            {
                path: 'custom-fields',
                component: CustomFields,
                name: 'admin.settings.custom_fields.index',
                meta: {
                    requireAuth: true,
                    menuParent: "settings",
                    menuKey: route => "custom_fields",
                    permission: "custom_fields_view"
                }
            },
            ...CommonAdminSettings,
        ]

    }
]
