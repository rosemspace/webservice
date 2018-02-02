const loadView = (path, importCallback) => typeof path === 'string' ? () => importCallback(path) : path;

const prepareViewsInRoutes = (routes, importCallback) => {
    for (const route of routes) {
        if (route.component) {
            const path = route.component;
            route.component = loadView(path, importCallback);
        } else if (route.components) {
            for (const [name, path] of Object.entries(route.components)) {
                route.components[name] = loadView(path, importCallback);
            }
        }

        if (route.children) {
            prepareViewsInRoutes(route.children, importCallback);
        }
    }
    return routes;
};

export default prepareViewsInRoutes;
