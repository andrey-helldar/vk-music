//window._ = require('lodash');
import _ from 'lodash';
window._ = _;

/**
 * We'll load jQuery and the MaterializeCSS jQuery plugin which provides support
 * for JavaScript based MaterializeCSS features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

//window.$ = window.jQuery = require('jquery');
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

require('./materialize.min');
//import 'materialize-css';

/**
 * Vue is a modern JavaScript library for building interactive web interfaces
 * using reactive data binding and reusable components. Vue's API is clean
 * and simple, leaving you to focus on building your next great project.
 */

import Vue from 'vue';
window.Vue = Vue;

import VueResource from "vue-resource";
Vue.use(VueResource);

/**
 * We'll register a HTTP interceptor to attach the "CSRF" header to each of
 * the outgoing requests issued by this application. The CSRF middleware
 * included with Laravel will automatically verify the header's value.
 */

Vue.http.options.root = '/api';
Vue.http.headers.common['X-CSRF-TOKEN'] = Laravel.csrfToken;

Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-TOKEN', Laravel.csrfToken);

    next();
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from "laravel-echo"

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });

/**
 * Other
 */
require('./functions');

/**
 * Other parameters of Vue.js.
 */
Vue.config.async = true;
Vue.config.devtools = appFunc.debug; // DevTools mode is only available in development build. In production set FALSE !
Vue.config.debug = appFunc.debug; // Debug mode is only available in development build. In production set FALSE !
Vue.config.silent = !appFunc.debug; //Suppress all Vue.js logs and warnings.
Vue.config.unsafeDelimiters = ['{!!', '!!}']; // Change the raw HTML interpolation delimiters.
