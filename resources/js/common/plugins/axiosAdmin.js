import axios from 'axios';
import { message } from "ant-design-vue";
import store from '../../main/store';
import router from '../../main/router';

var axiosAdmin = axios.create({
    baseURL: window.config.path + '/api/v1'
});

// Axios default headers
axiosAdmin.defaults.headers['Accept'] = 'application/json';
axiosAdmin.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axiosAdmin.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

// Axios jwt token by default
if (localStorage.getItem('auth_token')) {
    axiosAdmin.defaults.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
}
// Axios error listener
axiosAdmin.interceptors.response.use(function (response) {
    return response.data;
}, function (error) {
    const errorCode = error.response.status;

    if (errorCode === 401 || (errorCode === 500 && error.response && error.response.data && error.response.data.message && error.response.data.message == 'UNAUTHORIZED EXCEPTION')) {
        // If error 401 redirect to login
        window.location.href = window.config.path + '/admin/login';
        delete window.axiosAdmin.defaults.headers.common.Authorization;
        localStorage.removeItem('auth_token');
        localStorage.setItem('auth_user', null);
        // throw new Error('Unauthorized');
    } else if (errorCode === 400) {
        var errMessage = error.response.data.error.message;
        message.error(errMessage);
    } else if (errorCode === 403) {
        var errMessage = error.response.data.error.message;
        message.error(errMessage);
    } else if (errorCode === 404) {
        var errMessage = error.response.data.error.message;
        message.error(errMessage);
    } else if (errorCode === 405) {
        store.dispatch("auth/updateApp");

        if (window.config.app_type == 'saas') {
            router.push({
                name: "admin.subscription.current_plan",
            });
        }

    }

    return Promise.reject(error.response);
});

/**
 * Set global so you don't have to import it
 */
window.axiosAdmin = axiosAdmin;
