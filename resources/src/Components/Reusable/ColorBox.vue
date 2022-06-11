<template>
  <div class="color-box" :style="{ background: color }"></div>
</template>

<script>
export default {
  props: {
    color: {
      type: String,
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.color-box {
  display: inline-block;
  height: 20px;
  width: 20px;
  border: 0.6px solid grey;
}
</style>
