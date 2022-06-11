<template>
  <v-app>
    <div class="con">
      <slot />
    </div>
  </v-app>
</template>

<script>
export default {
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

<style lang="scss" scoped>
.con {
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
    url("/assets/authBackground.jpg");
  background-repeat: no-repeat;
  background-size: cover;
}
</style>
