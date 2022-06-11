import Vue from "vue";
import Vuetify from "vuetify/lib";

Vue.use(Vuetify);

export default new Vuetify({
    theme: {
        themes: {
            light: {
                primary: "#355070",
                secondary: "#343a40",
                tertiary: "#a73060",
                accent: "#607d8b",
                error: "#cd4631",
                warning: "#ffb107",
                info: "#04AEC4",
                success: "#8bc34a",
            },
        },
    },
});
