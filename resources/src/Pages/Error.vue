<template>
  <div class="con">
    <div>
      <H1>{{ title }}</H1>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    status: Number,
  },
  computed: {
    title() {
      return {
        503: "503: Service Unavailable",
        500: "500: Server Error",
        404: "404: Page Not Found",
        403: "403: Forbidden",
      }[this.status];
    },
  },
};
</script>

<style lang="scss" scoped>
.con {
  display: flex;
  align-items: center;
  justify-content: center;
  height: 100%;
}
</style>
