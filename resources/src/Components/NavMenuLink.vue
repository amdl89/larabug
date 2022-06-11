<template>
  <div
    class="hover-class cur-point mb-3"
    style="user-select: none"
    v-if="navLink.show"
  >
    <Link
      :href="route(navLink.route.name, navLink.route.params || {})"
      preserve-state
      :class="{
        'link-active': active,
      }"
      class="py-3 px-4 white--text"
      as="div"
    >
      <div class="d-flex align-center">
        <v-icon class="mr-6" v-if="navLink.icon">{{ navLink.icon }} </v-icon>
        <div v-else class="mr-12"></div>
        <div class="h4 ma-0">{{ navLink.text }}</div>
      </div>
    </Link>
  </div>
</template>

<script>
export default {
  name: "NavMenuLink",
  props: {
    navLink: {
      type: Object,
      required: true,
    },
    currRoute: {
      type: String,
      required: true,
    },
  },
  computed: {
    active() {
      return this.navLink.route.name == this.currRoute;
    },
  },
  created() {
    if (this.active) {
      this.$emit("openDropDown");
    }
  },
};
</script>

<style lang="scss">
.link-active {
  background-color: #355070 !important;
  border-radius: 15px;
}
</style>
