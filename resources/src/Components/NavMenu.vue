<template>
  <div style="user-select: none">
    <template v-for="menuItem in menu">
      <nav-menu-link
        :nav-link="menuItem"
        v-if="menuItem.type === 'link'"
        :key="menuItem.id"
        v-on="$listeners"
        :currRoute="currRoute"
      />
      <nav-menu-dropdown
        v-else-if="menuItem.type === 'dropdown'"
        :key="menuItem.id"
        :nav-drop-down="menuItem"
        v-on="$listeners"
        :currRoute="currRoute"
      />
    </template>
  </div>
</template>

<script>
export default {
  name: "NavMenu",
  components: {
    NavMenuLink: () => import("@/components/NavMenuLink.vue"),
    NavMenuDropdown: () => import("@/components/NavMenuDropdown.vue"),
  },
  props: {
    menu: {
      type: Array,
      required: true,
    },
    currRoute: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="scss">
.hover-class:hover {
  opacity: 0.8 !important;
  background: rgba(170, 170, 170, 0.452) !important;
  border-radius: 15px;
}
</style>
