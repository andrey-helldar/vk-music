const routes = [
    {
        name:      'index',
        path:      '/',
        component: setComponent('index')
    },
    {
        name:      'my',
        path:      '/my',
        component: setComponent('my')
    },
    {
        name:      'search',
        path:      '/search',
        component: setComponent('search')
    },
    {
        name:      'friends',
        path:      '/friends',
        component: setComponent('friends')
    },
    {
        name:      'groups',
        path:      '/groups',
        component: setComponent('groups')
    },
    {
        name:      'recommendations',
        path:      '/recommendations',
        component: setComponent('recommendations')
    },
    {
        name:      'popular',
        path:      '/popular',
        component: setComponent('popular')
    },
    /**
     * Authenticate
     */
    {
        name:      'auth',
        path:      '/auth',
        component: setComponent('vk-auth')
    },
    {
        name:      'verify',
        path:      '/verify',
        component: setComponent('vk-verify')
    },
    /**
     * Redirect to Main page.
     */
    {
        path:     '/*',
        redirect: '/'
    }
];