// import Admin from '../../common/layouts/Admin.vue';
import Create from '../views/stock-management/purchases/Create.vue';
import Edit from '../views/stock-management/purchases/Edit.vue';
import Purchases from '../views/stock-management/purchases/index.vue';

import Pos from "../views/stock-management/pos/Pos.vue";
import StockAdjustment from "../views/stock-management/adjustment/index.vue";

import Payment from "../views/stock-management/payments/index.vue";

import OnlineOrders from "../views/stock-management/online-orders/index.vue";

import StockTransfer from "../views/stock-management/stock-transfer/index.vue";

export default [
    {
        path: '/admin/stock/pos',
        component: Pos,
        name: 'admin.pos.index',
        meta: {
            requireAuth: true,
            menuParent: "pos",
            menuKey: "pos",
            permission: "pos_view",
        }
    },
    {
        path: '/admin/stock/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: 'adjustments',
                component: StockAdjustment,
                name: 'admin.stock_adjustments.index',
                meta: {
                    requireAuth: true,
                    menuParent: "stock_adjustment",
                    menuKey: "stock_adjustment",
                    permission: "stock_adjustments_view",
                }
            },
            // Purchases
            {
                path: 'purchases/edit/:id',
                component: Edit,
                name: 'admin.stock.purchases.edit',
                meta: {
                    requireAuth: true,
                    menuParent: "purchases",
                    menuKey: route => 'purchases',
                    permission: route => 'purchases_edit',
                    orderType: "purchases"
                }
            },
            {
                path: 'purchases/create',
                component: Create,
                name: 'admin.stock.purchases.create',
                meta: {
                    requireAuth: true,
                    menuParent: "purchases",
                    menuKey: route => "purchases",
                    permission: route => 'purchases_create',
                    orderType: "purchases"
                }
            },
            {
                path: 'purchases',
                component: Purchases,
                name: 'admin.stock.purchases.index',
                meta: {
                    requireAuth: true,
                    menuParent: "purchases",
                    menuKey: route => "purchases",
                    permission: route => 'purchases_view',
                    orderType: "purchases"
                }
            },

            // Purchase Returns
            {
                path: 'purchase-returns/edit/:id',
                component: Edit,
                name: 'admin.stock.purchase-returns.edit',
                meta: {
                    requireAuth: true,
                    menuParent: "purchases",
                    menuKey: route => 'purchase_returns',
                    permission: route => 'purchase_returns_edit',
                    orderType: "purchase-returns"
                }
            },
            {
                path: 'purchase-returns/create',
                component: Create,
                name: 'admin.stock.purchase-returns.create',
                meta: {
                    requireAuth: true,
                    menuParent: "purchases",
                    menuKey: route => "purchase_returns",
                    permission: route => 'purchase_returns_create',
                    orderType: "purchase-returns"
                }
            },
            {
                path: 'purchase-returns',
                component: Purchases,
                name: 'admin.stock.purchase-returns.index',
                meta: {
                    requireAuth: true,
                    menuParent: "purchases",
                    menuKey: route => "purchase_returns",
                    permission: route => 'purchase_returns_view',
                    orderType: "purchase-returns"
                }
            },

            // Sales
            {
                path: 'sales/edit/:id',
                component: Edit,
                name: 'admin.stock.sales.edit',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => 'sales',
                    permission: route => 'sales_edit',
                    orderType: "sales"
                }
            },
            {
                path: 'sales/create',
                component: Create,
                name: 'admin.stock.sales.create',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => "sales",
                    permission: route => 'sales_create',
                    orderType: "sales"
                }
            },
            {
                path: 'sales',
                component: Purchases,
                name: 'admin.stock.sales.index',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => "sales",
                    permission: route => 'sales_view',
                    orderType: "sales"
                }
            },

            // Sales Returns
            {
                path: 'sales-returns/edit/:id',
                component: Edit,
                name: 'admin.stock.sales-returns.edit',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => 'sales_returns',
                    permission: route => 'sales_returns_edit',
                    orderType: "sales-returns"
                }
            },
            {
                path: 'sales-returns/create',
                component: Create,
                name: 'admin.stock.sales-returns.create',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => "sales_returns",
                    permission: route => 'sales_returns_create',
                    orderType: "sales-returns"
                }
            },
            {
                path: 'sales-returns',
                component: Purchases,
                name: 'admin.stock.sales-returns.index',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => "sales_returns",
                    permission: route => 'sales_returns_view',
                    orderType: "sales-returns"
                }
            },

            // Quotaiton/Estimate
            {
                path: 'quotations/edit/:id',
                component: Edit,
                name: 'admin.stock.quotations.edit',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => 'quotations',
                    permission: route => 'quotations_edit',
                    orderType: "quotations"
                }
            },
            {
                path: 'quotations/create',
                component: Create,
                name: 'admin.stock.quotations.create',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => "quotations",
                    permission: route => 'quotations_create',
                    orderType: "quotations"
                }
            },
            {
                path: 'quotations',
                component: Purchases,
                name: 'admin.stock.quotations.index',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: route => "quotations",
                    permission: route => 'quotations_view',
                    orderType: "quotations"
                }
            },

            // Online Orders
            {
                path: 'online-orders',
                component: OnlineOrders,
                name: 'admin.online_orders.index',
                meta: {
                    requireAuth: true,
                    menuParent: "online_orders",
                    menuKey: "online_orders",
                    orderType: "online-orders"
                }
            },

            // Stock Transfer
            {
                path: 'stock-transfers/edit/:id',
                component: Edit,
                name: 'admin.stock.stock-transfers.edit',
                meta: {
                    requireAuth: true,
                    menuParent: "stock_transfer",
                    menuKey: route => 'stock_transfer',
                    permission: route => 'stock_transfers_edit',
                    orderType: "stock-transfers"
                }
            },
            {
                path: 'stock-transfers/create',
                component: Create,
                name: 'admin.stock.stock-transfers.create',
                meta: {
                    requireAuth: true,
                    menuParent: "stock_transfer",
                    menuKey: route => "stock_transfer",
                    permission: route => 'stock_transfers_create',
                    orderType: "stock-transfers"
                }
            },
            {
                path: 'stock-transfers',
                component: StockTransfer,
                name: 'admin.stock.stock-transfers.index',
                meta: {
                    requireAuth: true,
                    menuParent: "stock_transfer",
                    menuKey: route => "stock_transfer",
                    permission: route => 'stock_transfers_view',
                    orderType: "stock-transfers"
                }
            },
        ]
    },
    {
        path: '/admin/payment/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: 'in',
                component: Payment,
                name: 'admin.payments.in',
                meta: {
                    requireAuth: true,
                    menuParent: "sales",
                    menuKey: "payment_in",
                    permission: "payment_in_view",
                    paymentType: "in",
                }
            },
            {
                path: 'out',
                component: Payment,
                name: 'admin.payments.out',
                meta: {
                    requireAuth: true,
                    menuParent: "purchases",
                    menuKey: "payment_out",
                    permission: "payment_out_view",
                    paymentType: "out",
                }
            },
        ]
    },
]
