/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
require('./helper/filters');
require('./helper/progressBar');
require('./helper/sweetAlert');

window.Vue = require('vue');

//Vue Form
import { Form, HasError, AlertError } from 'vform';

window.Form = Form;
Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);


//Vue Router
import VueRouter from 'vue-router'

Vue.use(VueRouter);

let routes = [
    { path: '/dashboard', component: require('./components/Dashboard').default },
    { path: '/developer', component: require('./components/Developer').default },
    { path: '/profile', component: require('./components/Profile').default },
    { path: '/users', component: require('./components/Users').default },
    { path: '*', component: require('./components/404').default },
]

const router = new VueRouter({
    mode: 'history',
    routes
});

window.Fire = new Vue();

//User Type verification gate
import Gate from './helper/gate';
Vue.prototype.$gate = new Gate(window.user);


//Moment JS and its filters
import moment from 'moment';
Vue.filter('filterDate', function(value){
    return moment(value).format('MMMM Do YYYY');
})
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue').default
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue').default
);

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue').default
);


//404 Vue Page
Vue.component(
    'not-found',
    require('./components/404.vue').default
);

//Pagination
Vue.component('pagination', require('laravel-vue-pagination'));


const app = new Vue({
    el: '#app',
    router,
    data:{
        search: "",
    },

    methods:{
        searchStart(){
            Fire.$emit('searching');
        }
    }
});
