import { createStore } from 'vuex';
import auth from './auth';
import front from './front';

export default createStore({
    modules: {
        auth,
        front
    }
})
