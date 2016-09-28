var VueRouter = require('vue-router');
Vue.use(VueRouter);

// The router needs a root component to render.
// For demo purposes, we will just use an empty one
// because we are using the HTML as the app template.
var routesApp = Vue.extend({});

// Create a router instance.
// You can pass in additional options here, but let's
// keep it simple for now.
var router = new VueRouter({});

// Define some routes.
// Each route should map to a component. The "component" can
// either be an actual component constructor created via
// Vue.extend(), or just a component options object.
// We'll talk about nested routes later.
router.map({
    '/':        {
        component: {
            //template: Vue.component('index')
            template: '<h1 class="loader-screen-hide">I SEE YOU!</h1>'
        }
    },
    '/my':      {
        component: Vue.component('audio')
    },
    '/friends': {
        component: Vue.component('friends')
    },
    '/groups':  {
        component: Vue.component('groups')
    },
    '/search':  {
        component: Vue.component('search')
    }
});


router.start(app, 'main');