<template>
  <div class="mb-3 cur-point" style="user-select: none" v-if="navDropDown.show">
    <div
      :class="{ dropDownOpen: open }"
      class="d-flex align-center py-3 px-4 white--text hover-class"
      @click="toggleDropDown"
    >
      <v-icon class="mr-6" v-if="navDropDown.icon"
        >{{ navDropDown.icon }}
      </v-icon>
      <div v-else class="mr-12"></div>
      <div class="h4 ma-0">{{ navDropDown.text }}</div>
      <div class="ml-auto">
        <v-icon :class="{ rotated: open }">mdi-arrow-down</v-icon>
      </div>
    </div>
    <div v-show="open" class="ml-5">
      <nav-menu
        :menu="navDropDown.children"
        v-on="$listeners"
        @openDropDown="openDropDown"
        :currRoute="currRoute"
      />
    </div>
  </div>
</template>

<script>
export default {
  components: {
    NavMenu: () => import("@/components/NavMenu.vue"),
  },
  name: "NavMenuDropdown",
  props: {
    navDropDown: {
      type: Object,
      required: true,
    },
    currRoute: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      open: false,
    };
  },
  methods: {
    toggleDropDown() {
      this.open = !this.open;
    },
    openDropDown() {
      this.open = true;
      this.$emit("openDropDown");
    },
  },
};
</script>

<style lang="scss" scoped>
.dropDownOpen {
  opacity: 0.8 !important;
  background: transparent !important;
}
.rotated {
  transform: rotate(180deg);
  transition: transform 0.3s ease-in;
}
</style>
