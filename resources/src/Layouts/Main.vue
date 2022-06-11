<template>
  <v-app style="background-color: lavender !important">
    <app-nav-bar id="theNavbar" />
    <v-main>
      <slot />
    </v-main>
    <v-btn
      v-scroll="onScroll"
      v-show="fab"
      fab
      dark
      fixed
      bottom
      right
      color="tertiary"
      @click="toTop"
    >
      <v-icon>mdi-chevron-up</v-icon>
    </v-btn>
  </v-app>
</template>

<script>
import AppNavBar from "@/components/AppNavBar.vue";

export default {
  components: { AppNavBar },
  data() {
    return {
      fab: false,
    };
  },
  methods: {
    onScroll: _.throttle(function (e) {
      if (typeof window === "undefined") {
        return;
      }
      const top = window.pageYOffset || e.target.scrollTop || 0;
      this.fab = top > 20;
    }, 500),
    toTop() {
      this.$vuetify.goTo(0);
    },
  },
  updated() {
    const toast = this.$page.props.toast;
    if (toast) {
      this.$toasted.show(toast.message, {
        type: toast.type,
        duration: 4000,
        icon: "check-bold",
      });
      this.$page.props.toast = null;
    }
  },
};
</script>

<style lang="scss">
#theNavbar header {
  min-height: 64px !important;
}
</style>
