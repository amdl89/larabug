import Vue from "vue";
import { createInertiaApp, Link, Head } from "@inertiajs/inertia-vue";
import { InertiaProgress } from "@inertiajs/progress";
import PortalVue from "portal-vue";
import UUID from "vue-uuid";
import Toasted from "vue-toasted";
import Vuelidate from "vuelidate";
import vuetify from "@/Libs/Vuetify";
import VueDataObjectPath from "vue-data-object-path";

require("@/bootstrap");

createInertiaApp({
    resolve: (name) => require(`@/Pages/${name}`),
    setup({ el, App, props, plugin }) {
        Vue.use(plugin)
            .use(PortalVue)
            .use(UUID)
            .use(Toasted, {
                className: "toast",
                iconPack: "mdi",
                theme: "bubble",
            })
            .use(Vuelidate)
            .use(VueDataObjectPath)
            .mixin({ methods: { route: window.route } });

        Vue.component("Link", Link).component("Head", Head);

        new Vue({
            vuetify,
            render: (h) => h(App, props),
        }).$mount(el);
    },
});

InertiaProgress.init({
    color: "#cd4631",
    showSpinner: true,
});
