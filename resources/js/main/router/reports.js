// import Admin from '../../common/layouts/Admin.vue';
import Payments from '../views/reports/payments/index.vue';
import StockAlert from '../views/reports/stock-alert/index.vue';
import Users from '../views/reports/users/index.vue';
import CashBank from '../views/reports/cash-bank/index.vue';
import SalesSummrary from '../views/reports/sales-summary/index.vue';
import StockSummrary from '../views/reports/stock-summary/index.vue';
import RateList from '../views/reports/rate-list/index.vue';
import ProductSalesSummary from '../views/reports/product-sales-summary/index.vue';
import Expenses from '../views/reports/expenses/index.vue';
import ProfitLoss from '../views/reports/profit-loss/index.vue';

export default [
    {
        path: '/admin/reports/',
        component: () => import('../../common/layouts/Admin.vue'),
        children: [
            {
                path: 'payments',
                component: Payments,
                name: 'admin.reports.payments.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "payments",
                }
            },
            {
                path: 'stock-alert',
                component: StockAlert,
                name: 'admin.reports.stock.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "stock_alert",
                }
            },
            {
                path: 'users',
                component: Users,
                name: 'admin.reports.users.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "users_reports",
                }
            },
            {
                path: 'sales-summary',
                component: SalesSummrary,
                name: 'admin.reports.sales_summary.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "sales_summary",
                    permission: "users_view"
                }
            },
            {
                path: 'stock-summary',
                component: StockSummrary,
                name: 'admin.reports.stock_summary.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "stock_summary",
                    permission: "products_view"
                }
            },
            {
                path: 'rate-list',
                component: RateList,
                name: 'admin.reports.rate_list.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "rate_list",
                    permission: "products_view"
                }
            },
            {
                path: 'product-sales-summary',
                component: ProductSalesSummary,
                name: 'admin.reports.product_sales_summary.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "product_sales_summary",
                    permission: "products_view"
                }
            },
            {
                path: 'cash-bank',
                component: CashBank,
                name: 'admin.reports.cash_bank.index',
                meta: {
                    requireAuth: true,
                    menuParent: "cash_bank",
                    menuKey: "cash_bank",
                }
            },
            {
                path: 'expenses',
                component: Expenses,
                name: 'admin.reports.expenses.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "expense_reports",
                }
            },
            {
                path: 'profit-loss',
                component: ProfitLoss,
                name: 'admin.reports.profit_loss.index',
                meta: {
                    requireAuth: true,
                    menuParent: "reports",
                    menuKey: "profit_loss",
                }
            },

        ]

    }
]
