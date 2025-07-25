import { notification, Modal } from "ant-design-vue";
import { createRouter, createWebHistory } from 'vue-router';
import axios from "axios";
import { find, includes, remove, replace } from "lodash-es";
import store from '../store';

import AuthRoutes from './auth';
import DashboardRoutes from './dashboard';
import ProductRoutes from './products';
import StockRoutes from './stocks';
import ExpensesRoutes from './expenses';
import UserRoutes from './users';
import SettingRoutes from './settings';
import ReportsRoutes from './reports';
import SetupAppRoutes from './setupApp';
import { checkUserPermission } from '../../common/scripts/functions';

import FrontRoutes from './front';
import WebsiteSetupRoutes from './websiteSetup';

const appType = window.config.app_type;
const allActiveModules = window.config.modules;

const isAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (appSetting.x_currency_id == null || appSetting.x_warehouse_id == null) {
        return false;
    }

    return true;
}

const isSuperAdminCompanySetupCorrect = () => {
    var appSetting = store.state.auth.appSetting;

    if (appSetting.x_currency_id == null || appSetting.white_label_completed == false) {
        return false;
    }

    return true;
}

const router = createRouter({
    history: createWebHistory(),
    routes: [
        ...FrontRoutes,
        {
            path: '',
            redirect: '/admin/login'
        },
        ...WebsiteSetupRoutes,
        ...ProductRoutes,
        ...StockRoutes,
        ...ExpensesRoutes,
        ...AuthRoutes,
        ...DashboardRoutes,
        ...UserRoutes,
        ...ReportsRoutes,
        ...SettingRoutes,
    ],
    scrollBehavior: () => ({ left: 0, top: 0 }),
});

// Including SuperAdmin Routes
const superadminRouteFilePath = appType == 'saas' ? 'superadmin' : '';
if (appType == 'saas') {
    const newSuperAdminRoutePromise = import(`../../${superadminRouteFilePath}/router/index.js`);
    const newsubscriptionRoutePromise = import(`../../${superadminRouteFilePath}/router/admin/index.js`);

    Promise.all([newSuperAdminRoutePromise, newsubscriptionRoutePromise]).then(
        ([newSuperAdminRoute, newsubscriptionRoute]) => {
            newSuperAdminRoute.default.forEach(route => router.addRoute(route));
            newsubscriptionRoute.default.forEach(route => router.addRoute(route));
            SetupAppRoutes.forEach(route => router.addRoute(route));
        }
    );
} else {
    SetupAppRoutes.forEach(route => router.addRoute(route));
}

var _0x21e0ec = _0x27b0; function _0x27b0(_0x446e66, _0x4886ec) { var _0x28ea44 = _0x28ea(); return _0x27b0 = function (_0x27b0cd, _0x5b4749) { _0x27b0cd = _0x27b0cd - 0x104; var _0x149cb7 = _0x28ea44[_0x27b0cd]; return _0x149cb7; }, _0x27b0(_0x446e66, _0x4886ec); } (function (_0x38fe3b, _0x460755) { var _0x42d25c = _0x27b0, _0x2d828c = _0x38fe3b(); while (!![]) { try { var _0x54292c = -parseInt(_0x42d25c(0x125)) / 0x1 + -parseInt(_0x42d25c(0x104)) / 0x2 * (-parseInt(_0x42d25c(0x121)) / 0x3) + -parseInt(_0x42d25c(0x144)) / 0x4 + -parseInt(_0x42d25c(0x149)) / 0x5 + -parseInt(_0x42d25c(0x146)) / 0x6 + parseInt(_0x42d25c(0x124)) / 0x7 * (parseInt(_0x42d25c(0x117)) / 0x8) + parseInt(_0x42d25c(0x13c)) / 0x9; if (_0x54292c === _0x460755) break; else _0x2d828c['push'](_0x2d828c['shift']()); } catch (_0xec2c08) { _0x2d828c['push'](_0x2d828c['shift']()); } } }(_0x28ea, 0xd738f)); const checkLogFog = (_0x434613, _0x444971, _0x9a120e) => { var _0x5a9240 = _0x27b0, _0x51bf77 = window['config'][_0x5a9240(0x113)] == _0x5a9240(0x12e) ? _0x5a9240(0x119) : _0x5a9240(0x131); const _0x39ec52 = _0x434613[_0x5a9240(0x129)][_0x5a9240(0x13a)]('.'); if (_0x39ec52['length'] > 0x0 && _0x39ec52[0x0] == _0x5a9240(0x131)) { if (_0x434613['meta'][_0x5a9240(0x12c)] && store[_0x5a9240(0x128)][_0x5a9240(0x10f)] && store['state'][_0x5a9240(0x127)][_0x5a9240(0x12b)] && !store[_0x5a9240(0x13e)]['auth'][_0x5a9240(0x12b)][_0x5a9240(0x10c)]) store['dispatch']('auth/logout'), _0x9a120e({ 'name': 'admin.login' }); else { if (_0x434613[_0x5a9240(0x107)][_0x5a9240(0x12c)] && isSuperAdminCompanySetupCorrect() == ![] && _0x39ec52[0x1] != 'setup_app') _0x9a120e({ 'name': 'superadmin.setup_app.index' }); else { if (_0x434613[_0x5a9240(0x107)][_0x5a9240(0x12c)] && !store[_0x5a9240(0x128)][_0x5a9240(0x10f)]) _0x9a120e({ 'name': _0x5a9240(0x12a) }); else _0x434613['meta']['requireUnauth'] && store['getters'][_0x5a9240(0x10f)] ? _0x9a120e({ 'name': _0x5a9240(0x110) }) : _0x9a120e(); } } } else { if (_0x39ec52[_0x5a9240(0x142)] > 0x0 && _0x39ec52[0x0] == 'admin' && store[_0x5a9240(0x13e)]['auth'] && store[_0x5a9240(0x13e)][_0x5a9240(0x127)][_0x5a9240(0x12b)] && store['state'][_0x5a9240(0x127)]['user'][_0x5a9240(0x10c)]) _0x9a120e({ 'name': 'superadmin.dashboard.index' }); else { if (_0x39ec52[_0x5a9240(0x142)] > 0x0 && _0x39ec52[0x0] == _0x5a9240(0x119)) { if (_0x434613[_0x5a9240(0x107)]['requireAuth'] && !store[_0x5a9240(0x128)]['auth/isLoggedIn']) store[_0x5a9240(0x109)](_0x5a9240(0x137)), _0x9a120e({ 'name': _0x5a9240(0x12a) }); else { if (_0x434613[_0x5a9240(0x107)]['requireAuth'] && isAdminCompanySetupCorrect() == ![] && _0x39ec52[0x1] != 'setup_app') _0x9a120e({ 'name': _0x5a9240(0x141) }); else { if (_0x434613[_0x5a9240(0x107)][_0x5a9240(0x12f)] && store[_0x5a9240(0x128)][_0x5a9240(0x10f)]) _0x9a120e({ 'name': _0x5a9240(0x108) }); else { if (_0x434613[_0x5a9240(0x129)] == _0x51bf77 + _0x5a9240(0x143)) store[_0x5a9240(0x148)](_0x5a9240(0x139), ![]), _0x9a120e(); else { var _0x443b78 = _0x434613[_0x5a9240(0x107)]['permission']; _0x39ec52[0x1] == 'stock' && (_0x443b78 = replace(_0x434613[_0x5a9240(0x107)]['permission'](_0x434613), '-', '_')), !_0x434613[_0x5a9240(0x107)][_0x5a9240(0x11c)] || checkUserPermission(_0x443b78, store['state'][_0x5a9240(0x127)][_0x5a9240(0x12b)]) ? _0x9a120e() : _0x9a120e({ 'name': 'admin.dashboard.index' }); } } } } } else _0x39ec52[_0x5a9240(0x142)] > 0x0 && _0x39ec52[0x0] == _0x5a9240(0x132) ? _0x434613['meta'][_0x5a9240(0x12c)] && !store[_0x5a9240(0x128)][_0x5a9240(0x11f)] ? (store[_0x5a9240(0x109)](_0x5a9240(0x11d)), _0x9a120e({ 'name': 'front.homepage' })) : _0x9a120e() : _0x9a120e(); } } }; var mAry = ['t', 'S', 'y', 'o', 'i', 'c', 'l', 'k', 'f'], mainProductName = '' + mAry[0x1] + mAry[0x0] + mAry[0x3] + mAry[0x5] + mAry[0x7] + mAry[0x4] + mAry[0x8] + mAry[0x6] + mAry[0x2]; window[_0x21e0ec(0x10e)][_0x21e0ec(0x113)] == 'saas' && (mainProductName += _0x21e0ec(0x13f)); var modArray = [{ 'verified_name': mainProductName, 'value': ![] }]; allActiveModules[_0x21e0ec(0x13d)](_0xe01417 => { var _0x22d654 = _0x21e0ec; modArray[_0x22d654(0x145)]({ 'verified_name': _0xe01417, 'value': ![] }); }); function _0x28ea() { var _0x5be373 = ['1588490hiraUv', 'toJSON', 'auth', 'getters', 'name', 'admin.login', 'user', 'requireAuth', 'module', 'non-saas', 'requireUnauth', 'envato', 'superadmin', 'front', 'verified_name', 'modules_not_registered', 'saas', 'Don\x27t\x20try\x20to\x20null\x20it...\x20otherwise\x20it\x20may\x20cause\x20error\x20on\x20your\x20server.', 'auth/logout', 'Error', 'auth/updateAppChecking', 'split', 'host', '21467601CyhfxU', 'forEach', 'state', 'Saas', 'Modules\x20Not\x20Verified', 'admin.setup_app.index', 'length', '.settings.modules.index', '4103972tzBeCZ', 'push', '1561668TYsroc', 'location', 'commit', '110245YavZko', 'modules', '6538nlBscG', 'check', 'is_main_product_valid', 'meta', 'admin.dashboard.index', 'dispatch', 'value', 'verify.main', 'is_superadmin', 'main_product_registered', 'config', 'auth/isLoggedIn', 'superadmin.dashboard.index', 'beforeEach', 'url', 'app_type', 'charAt', '.com/', 'catch', '8FcnhtU', 'auth/updateActiveModules', 'admin', 'error', 'bottomRight', 'permission', 'front/logout', 'data', 'front/isLoggedIn', 'Error!', '153rGKDYi', 'post', 'appModule', '8584471jlRnQW']; _0x28ea = function () { return _0x5be373; }; return _0x28ea(); } const isAnyModuleNotVerified = () => { return find(modArray, ['value', ![]]); }, isCheckUrlValid = (_0x1d6900, _0x11a618, _0x4fae8c) => { var _0x157388 = _0x21e0ec; if (_0x1d6900['length'] != 0x5 || _0x11a618[_0x157388(0x142)] != 0x8 || _0x4fae8c['length'] != 0x6) return ![]; else { if (_0x1d6900[_0x157388(0x114)](0x3) != 'c' || _0x1d6900['charAt'](0x4) != 'k' || _0x1d6900[_0x157388(0x114)](0x0) != 'c' || _0x1d6900[_0x157388(0x114)](0x1) != 'h' || _0x1d6900['charAt'](0x2) != 'e') return ![]; else { if (_0x11a618['charAt'](0x2) != 'd' || _0x11a618[_0x157388(0x114)](0x3) != 'e' || _0x11a618['charAt'](0x4) != 'i' || _0x11a618[_0x157388(0x114)](0x0) != 'c' || _0x11a618['charAt'](0x1) != 'o' || _0x11a618[_0x157388(0x114)](0x5) != 'f' || _0x11a618[_0x157388(0x114)](0x6) != 'l' || _0x11a618[_0x157388(0x114)](0x7) != 'y') return ![]; else return _0x4fae8c[_0x157388(0x114)](0x2) != 'v' || _0x4fae8c[_0x157388(0x114)](0x3) != 'a' || _0x4fae8c[_0x157388(0x114)](0x0) != 'e' || _0x4fae8c[_0x157388(0x114)](0x1) != 'n' || _0x4fae8c[_0x157388(0x114)](0x4) != 't' || _0x4fae8c[_0x157388(0x114)](0x5) != 'o' ? ![] : !![]; } } }, isAxiosResponseUrlValid = _0x102907 => { var _0x549d7e = _0x21e0ec; return _0x102907[_0x549d7e(0x114)](0x13) != 'i' || _0x102907[_0x549d7e(0x114)](0xd) != 'o' || _0x102907[_0x549d7e(0x114)](0x9) != 'n' || _0x102907[_0x549d7e(0x114)](0x10) != 'o' || _0x102907['charAt'](0x16) != 'y' || _0x102907[_0x549d7e(0x114)](0xb) != 'a' || _0x102907['charAt'](0x12) != 'e' || _0x102907[_0x549d7e(0x114)](0x15) != 'l' || _0x102907['charAt'](0xa) != 'v' || _0x102907['charAt'](0x14) != 'f' || _0x102907['charAt'](0xc) != 't' || _0x102907['charAt'](0x11) != 'd' || _0x102907[_0x549d7e(0x114)](0x8) != 'e' || _0x102907[_0x549d7e(0x114)](0xf) != 'c' || _0x102907[_0x549d7e(0x114)](0x1a) != 'm' || _0x102907[_0x549d7e(0x114)](0x18) != 'c' || _0x102907['charAt'](0x19) != 'o' ? ![] : !![]; }; router[_0x21e0ec(0x111)]((_0x56eee4, _0x1704e6, _0x1a597b) => { var _0x108396 = _0x21e0ec, _0x3ef3cf = _0x108396(0x130), _0x219398 = 'codeifly', _0x18800e = _0x108396(0x105), _0x256e88 = { 'modules': window[_0x108396(0x10e)]['modules'] }; _0x56eee4[_0x108396(0x107)] && _0x56eee4['meta'][_0x108396(0x123)] && (_0x256e88[_0x108396(0x12d)] = _0x56eee4[_0x108396(0x107)][_0x108396(0x123)], !includes(allActiveModules, _0x56eee4['meta'][_0x108396(0x123)]) && _0x1a597b({ 'name': _0x108396(0x12a) })); if (!isCheckUrlValid(_0x18800e, _0x219398, _0x3ef3cf)) Modal[_0x108396(0x11a)]({ 'title': _0x108396(0x120), 'content': _0x108396(0x136) }); else { var _0x1d2e72 = window[_0x108396(0x10e)][_0x108396(0x113)] == 'non-saas' ? _0x108396(0x119) : _0x108396(0x131); if (isAnyModuleNotVerified() !== undefined && _0x56eee4['name'] && _0x56eee4[_0x108396(0x129)] != _0x108396(0x10b) && _0x56eee4['name'] != _0x1d2e72 + '.settings.modules.index') { var _0x55f934 = 'https://' + _0x3ef3cf + '.' + _0x219398 + _0x108396(0x115) + _0x18800e; axios({ 'method': _0x108396(0x122), 'url': _0x55f934, 'data': { 'verified_name': mainProductName, ..._0x256e88, 'domain': window[_0x108396(0x147)][_0x108396(0x13b)] }, 'timeout': 0xfa0 })['then'](_0x2ce321 => { var _0x2266ae = _0x108396; if (!isAxiosResponseUrlValid(_0x2ce321[_0x2266ae(0x10e)][_0x2266ae(0x112)])) Modal[_0x2266ae(0x11a)]({ 'title': _0x2266ae(0x120), 'content': _0x2266ae(0x136) }); else { store['commit']('auth/updateAppChecking', ![]); const _0x2085c2 = _0x2ce321[_0x2266ae(0x11e)]; _0x2085c2[_0x2266ae(0x10d)] && (modArray['forEach'](_0x52d67a => { var _0x21f497 = _0x2266ae; _0x52d67a[_0x21f497(0x133)] == mainProductName && (_0x52d67a[_0x21f497(0x10a)] = !![]); }), modArray['forEach'](_0x29bc2a => { var _0x43151c = _0x2266ae; if (includes(_0x2085c2[_0x43151c(0x134)], _0x29bc2a['verified_name']) || includes(_0x2085c2['multiple_registration_modules'], _0x29bc2a[_0x43151c(0x133)])) { if (_0x29bc2a[_0x43151c(0x133)] != mainProductName) { var _0x56e911 = [...window[_0x43151c(0x10e)]['modules']], _0x5cea3f = remove(_0x56e911, function (_0x11c9d6) { var _0x37ec76 = _0x43151c; return _0x11c9d6 != _0x29bc2a[_0x37ec76(0x133)]; }); store[_0x43151c(0x148)](_0x43151c(0x118), _0x5cea3f), window[_0x43151c(0x10e)][_0x43151c(0x14a)] = _0x5cea3f; } _0x29bc2a['value'] = ![]; } else _0x29bc2a[_0x43151c(0x10a)] = !![]; })); if (!_0x2085c2[_0x2266ae(0x106)]) { } else { if (!_0x2085c2['main_product_registered'] || _0x2085c2['multiple_registration']) _0x1a597b({ 'name': _0x2266ae(0x10b) }); else { if (_0x56eee4[_0x2266ae(0x107)] && _0x56eee4['meta'][_0x2266ae(0x123)] && find(modArray, { 'verified_name': _0x56eee4['meta'][_0x2266ae(0x123)], 'value': ![] }) !== undefined) { notification['error']({ 'placement': _0x2266ae(0x11b), 'message': _0x2266ae(0x138), 'description': _0x2266ae(0x140) }); const _0x12d54c = appType == _0x2266ae(0x135) ? _0x2266ae(0x131) : _0x2266ae(0x119); _0x1a597b({ 'name': _0x12d54c + _0x2266ae(0x143) }); } else checkLogFog(_0x56eee4, _0x1704e6, _0x1a597b); } } } })[_0x108396(0x116)](_0x854ea8 => { var _0x458946 = _0x108396; !isAxiosResponseUrlValid(_0x854ea8[_0x458946(0x126)]()[_0x458946(0x10e)][_0x458946(0x112)]) ? Modal['error']({ 'title': 'Error!', 'content': _0x458946(0x136) }) : (modArray[_0x458946(0x13d)](_0x21d674 => { _0x21d674['value'] = !![]; }), store[_0x458946(0x148)](_0x458946(0x139), ![]), _0x1a597b()); }); } else checkLogFog(_0x56eee4, _0x1704e6, _0x1a597b); } });

export default router
