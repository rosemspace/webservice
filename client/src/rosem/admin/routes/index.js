export default [
    {
        path: '/admin',
        name: '@rosem/admin:admin',
        component: 'layouts/TheAdminLayout',
        children: [
            {
                path: 'board',
                name: '@rosem/admin:board',
                component: 'pages/TheDashboardPage',
            },
            {
                path: 'blog',
                name: '@rosem/admin:blog',
                component: 'pages/TheBlogPage',
            },
        ],
    },
    {
        path: '*',
        component: 'layouts/TheAdminLayout',
        children: [
            {
                path: '',
                name: '@rosem/admin:404',
                component: 'pages/TheNotFoundPage',
            },
        ],
    },
];
