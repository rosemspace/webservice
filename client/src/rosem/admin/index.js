// import "rosem-css";
import VueRouter from "vue-router";
import App, { resolveViews } from "@rosem/kernel";
// import store from '../../store';
import routes from "./routes";

export default new App({
  el: "#app",
  // metaInfo: {
  //     titleTemplate: '%s | Your company'
  // },
  // store,
  router: new VueRouter({
    mode: "history",
    routes: resolveViews(routes, path => import(`./views/${path}`)),
    linkActiveClass: "active",
    scrollBehavior(to, from, savedPosition) {
      return savedPosition || { x: 0, y: 0 };
    }
  }),

  render: h => <router-view />,

  created() {
    this.$root.$on("overlay", $event =>
      document.body.classList[$event ? "add" : "remove"]("overlapped")
    );
  }
});
