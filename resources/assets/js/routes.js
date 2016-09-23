router.redirect({
    '/':     '/users',
    '/test': '/users'
});

router.map({
    '/users': {
        component: {
            template: '<div><h1>ha ha ha USERS</h1></div>'
        }
    },
    '/test':  {
        component: {
            template: '<div><h1>ha ha ha TEST</h1></div>'
        }
    }
});

var routerApp = Vue.extend();

router.start(routerApp, '#test');