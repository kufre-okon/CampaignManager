/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue').default;

// register vee validator and rules
import { ValidationProvider, extend, ValidationObserver } from 'vee-validate';
import { required, max, min_value,image } from 'vee-validate/dist/rules';
extend('required', {
    ...required,
    message: 'This field is required'
});
extend('max', {
    ...max,
    message: 'This field must not be more than {length} characters or digits'
});

extend('min_value', {
    ...min_value,
    message: 'This field must not be less than {min} characters or digits'
});
extend('image', {
    ...image,
    message:'Please select only image files'
});



/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))


Vue.component('validation-summary', require('./components/common/FormValidationSummary.vue').default);
Vue.component('banner-file-previewer', require('./components/common/BannerFilePreviewer.vue').default);
Vue.component('creative-previewer-modal', require('./components/common/Modal.vue').default);

import App from './App.vue';
import VueAxios from 'vue-axios';
import VueRouter from 'vue-router';
import axios from 'axios';
import { routes } from './routes';
import Vue from 'vue';


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.use(VueRouter);
Vue.use(VueAxios, axios);

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

const app = new Vue({
    el: '#app',
    router: router,
    render: h => h(App),
});
