import moment from 'moment';
import router from '../router';
const AUTH_USER = 'auth_user';
const AUTH_TOKEN = 'auth_token';
const EXIPRES_KEY = 'expire_key';
const APP_SETTINGS_KEY = 'app_settings';
const GLOBAL_SETTINGS_KEY = 'global_settings';
const PER_PAGE_ITEM = 'per_page_item';
const ALL_LANGS = 'all_langs';
const SELECTED_LANG = 'selected_lang';
const PAGE_TITLE = 'page_title';
const DARK_THEME = 'dark_theme';
const ACTIVE_MODULES = 'active_modules';
const CSS_SETTINGS = 'css_settings';
const SELECTED_WAREHOUSE = 'selected_warehouse';
const ALL_WAREHOUSES = 'all_warehouses';
const ALL_FRONT_WAREHOUSES = 'all_front_warehouses';
const ADD_MENUS = 'add_menus';
const ALL_EXPORT_DATA = 'all_export_data';
const EMAIL_SETTING_VERIFIED = 'email_setting_verified';
const VISIBLE_SUBSCRIPTION_MODULES = 'visible_subscription_modules';

moment.suppressDeprecationWarnings = true;

const getJSONFromLocalStorage = (key) => {
    const value = window.localStorage.getItem(key);

    if (value === 'undefined' || value === 'null' || value === undefined || value === null) {
        return null;
    }
    else {
        return JSON.parse(value);
    }
};

const getDefaultLangKey = () => {
    var defaultLangKey = window.config.defaultLangKey;

    return defaultLangKey ? defaultLangKey : "en";
}

export default {
    namespaced: true,
    state() {
        return {
            user: getJSONFromLocalStorage(AUTH_USER),
            warehouse: getJSONFromLocalStorage(SELECTED_WAREHOUSE),
            all_warehouses: getJSONFromLocalStorage(ALL_WAREHOUSES) || [],
            all_front_warehouses: getJSONFromLocalStorage(ALL_FRONT_WAREHOUSES) || [],
            allLangs: getJSONFromLocalStorage(ALL_LANGS) || [],
            lang: window.localStorage.getItem(SELECTED_LANG) || getDefaultLangKey(),
            token: window.localStorage.getItem(AUTH_TOKEN) || null,
            expires: window.localStorage.getItem(EXIPRES_KEY) || null,
            globalSetting: getJSONFromLocalStorage(GLOBAL_SETTINGS_KEY),
            appSetting: getJSONFromLocalStorage(APP_SETTINGS_KEY),
            addMenus: getJSONFromLocalStorage(ADD_MENUS) || [],
            visibleSubscriptionModules: getJSONFromLocalStorage(VISIBLE_SUBSCRIPTION_MODULES) || [],
            perPage: window.localStorage.getItem(PER_PAGE_ITEM) || window.config.path,
            pageTitle: window.localStorage.getItem(PAGE_TITLE) || "",
            darkTheme: getJSONFromLocalStorage(DARK_THEME),
            activeModules: getJSONFromLocalStorage(ACTIVE_MODULES),
            cssSettings: getJSONFromLocalStorage(CSS_SETTINGS) || {
                leftRightPadding: '50px',
                headerMenuMode: 'vertical'
            },
            appChecking: true,
            emailSettingVerified: window.localStorage.getItem(EMAIL_SETTING_VERIFIED) || false,
            menuCollapsed: window.innerWidth <= 991 ? true : false,
            allExportData: getJSONFromLocalStorage(ALL_EXPORT_DATA) || [],
        }
    },

    mutations: {
        updateUser(state, user) {
            state.user = user;
            window.localStorage.setItem(AUTH_USER, JSON.stringify(user));
        },
        updateWarehouse(state, warehouse) {
            state.warehouse = warehouse;
            window.localStorage.setItem(SELECTED_WAREHOUSE, JSON.stringify(warehouse));
        },
        updateAllWarehouses(state, warehouses) {
            state.all_warehouses = warehouses;
            window.localStorage.setItem(ALL_WAREHOUSES, JSON.stringify(warehouses));
        },
        updateAppChecking(state, appChecking) {
            state.appChecking = appChecking;
        },
        updateToken(state, token) {
            state.token = token;
            window.localStorage.setItem(AUTH_TOKEN, token);

            // Setting up auth bearer token to axios
            axiosAdmin.defaults.headers.common["Authorization"] = `Bearer ${token}`
        },
        updateExpires(state, expires) {
            state.expires = new Date(expires);
            window.localStorage.setItem(EXIPRES_KEY, expires);
        },
        updateApp(state, appSetting) {
            state.appSetting = appSetting;
            window.localStorage.setItem(APP_SETTINGS_KEY, JSON.stringify(appSetting));

            // Changing favicon
            var faviconLink = document.querySelector("link[rel~='icon']");
            faviconLink.href = appSetting.small_light_logo_url;
        },
        updateGlobalSetting(state, globalSetting) {
            state.globalSetting = globalSetting;
            window.localStorage.setItem(GLOBAL_SETTINGS_KEY, JSON.stringify(globalSetting));

            // Changing favicon
            var faviconLink = document.querySelector("link[rel~='icon']");
            faviconLink.href = globalSetting.small_light_logo_url;
        },
        updateAddMenus(state, addMenus) {
            state.addMenus = addMenus;
            window.localStorage.setItem(ADD_MENUS, JSON.stringify(addMenus));
        },
        updatePerPage(state, perPage) {
            state.perPage = perPage;
            window.localStorage.setItem(PER_PAGE_ITEM, perPage);
        },
        updateLang(state, lang) {
            state.lang = lang;
            window.localStorage.setItem(SELECTED_LANG, lang);
        },
        updatePageTitle(state, pageTitle) {
            state.pageTitle = pageTitle;
            window.localStorage.setItem(PAGE_TITLE, pageTitle);

            if (state.appSetting !== null && state.appSetting !== undefined) {
                document.title = `${pageTitle} - ${state.appSetting.name}`;
            }
            else {
                document.title = `${pageTitle} - Stockify`;
            }

        },
        updateDarkTheme(state, isDarkTheme) {
            state.darkTheme = isDarkTheme;
            window.localStorage.setItem(DARK_THEME, isDarkTheme);
        },
        updateActiveModules(state, activeModules) {
            state.activeModules = activeModules;
            window.localStorage.setItem(ACTIVE_MODULES, JSON.stringify(activeModules));
        },
        updateCssSettings(state, cssSettings) {
            state.cssSettings = cssSettings;
            window.localStorage.setItem(CSS_SETTINGS, JSON.stringify(cssSettings));
        },
        updateAllLangs(state, allLangs) {
            state.allLangs = allLangs;
            window.localStorage.setItem(ALL_LANGS, JSON.stringify(allLangs));
        },
        updateMenuCollapsed(state, menuCollapsed) {
            state.menuCollapsed = menuCollapsed;
        },
        updatAllExportData(state, allExportData) {
            state.allExportData = allExportData;
            window.localStorage.setItem(ALL_EXPORT_DATA, JSON.stringify(allExportData));
        },
        updateEmailVerifiedSetting(state, emailSettingVerified) {
            state.emailSettingVerified = emailSettingVerified;
            window.localStorage.setItem(EMAIL_SETTING_VERIFIED, emailSettingVerified);
        },
        updateVisibleSubscriptionModules(state, modules) {
            state.visibleSubscriptionModules = modules;
            window.localStorage.setItem(VISIBLE_SUBSCRIPTION_MODULES, JSON.stringify(modules));
        },
    },

    actions: {
        updateUser(context) {
            axiosAdmin.post(`/user`)
                .then(function (response) {
                    context.commit('updateUser', response.data.user);
                })
                .catch(function (error) {

                });
        },
        updateGlobalSetting(context) {
            axiosAdmin.get('/global-setting')
                .then(function (response) {
                    context.commit('updateGlobalSetting', response.data.global_setting);

                    // Changing favicon
                    var faviconLink = document.querySelector("link[rel~='icon']");
                    faviconLink.href = globalSetting.small_light_logo_url;
                })
                .catch(function (error) {

                });
        },
        updateApp(context) {
            axiosAdmin.get('/app')
                .then(function (response) {
                    context.commit('updateApp', response.data.app);
                    context.commit('updateAddMenus', response.data.shortcut_menus.credentials);
                    context.commit('updateEmailVerifiedSetting', response.data.email_setting_verified);

                    // Changing favicon
                    var faviconLink = document.querySelector("link[rel~='icon']");
                    faviconLink.href = response.data.app.small_light_logo_url;
                })
                .catch(function (error) {

                });
        },
        updateAllLangs(context) {
            axiosAdmin.get('/all-langs')
                .then(function (response) {
                    context.commit('updateAllLangs', response.data.langs);
                })
                .catch(function (error) {

                });
        },
        updateAllWarehouses(context) {
            axiosAdmin.get('/warehouses?limit=10000')
                .then(function (response) {
                    context.commit('updateAllWarehouses', response.data);
                })
                .catch(function (error) {

                });
        },
        refreshToken(context) {
            const token = context.state.token;

            if (token !== "" && token !== "undefined" && token != 'null' && token != null) {
                axiosAdmin
                    .post(`/auth/refresh-token`)
                    .then(function (response) {
                        context.commit("updateUser", response.data.user);
                        context.commit("updateToken", response.data.token);
                        context.commit("updateExpires", response.data.expires_in);
                    })
                    .catch(function (error) { });
            }
        },
        logout(context) {
            axiosAdmin.post('/auth/logout')
                .then(function (response) {
                    window.sessionStorage.clear();

                    context.commit("updateUser", []);
                    context.commit('updateToken', null);
                    context.commit('updateExpires', null);
                    context.commit('updateEmailVerifiedSetting', 0);

                    router.push({
                        name: "admin.login"
                    });
                })
                .catch(function (error) {

                });
        },
        logoutToRootUrl(context) {
            axiosAdmin.post('/auth/logout')
                .then(function (response) {

                    context.commit("updateUser", []);
                    context.commit('updateToken', null);
                    context.commit('updateExpires', null);

                    window.location.href = window.config.path;
                })
                .catch(function (error) {

                });
        },
        updateVisibleSubscriptionModules(context) {
            axiosAdmin.post('/visible-subscription-modules')
                .then(function (response) {
                    context.commit('updateVisibleSubscriptionModules', response.data);
                })
                .catch(function (error) {

                });
        },
    },

    getters: {
        isLoggedIn: (state) => {
            if (state.token === null || state.token === '') {
                return false;
            }
            else {
                return moment(state.expires).isAfter(moment());
            }
        }
    }
}
