<template>
  <div class="move-container rounded-lg">
    <h3
      v-if="containerTitle"
      class="text-center py-2 tertiary white--text rounded-t-lg"
    >
      {{ containerTitle }}
    </h3>
    <slot
      name="moveAll:activator"
      :bind="{ disabled: items.length === 0 }"
      :on="{ click: moveAll }"
    >
      <v-btn
        block
        color="secondary"
        depressed
        class="white--text rounded-0"
        @click="moveAll"
        :disabled="items.length === 0"
      >
        Move All
        <v-icon right>mdi-chevron-double-right</v-icon>
      </v-btn>
    </slot>
    <div class="items-container py-3 cur-point">
      <div
        class="item pa-3"
        v-for="item in items"
        :key="item.value"
        @click="move(item)"
      >
        <slot name="item" :item="item">
          {{ item.text }}
        </slot>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      required: true,
    },
    containerTitle: {
      type: String,
      default: null,
    },
  },
  methods: {
    moveAll() {
      this.$emit("allMoved", { items: this.items });
    },
    move(item) {
      this.$emit("itemMoved", { item });
    },
  },
};
</script>

<style lang="scss" scoped>
.move-container {
  background: #fff;
}

.items-container {
  overflow-y: auto;
  max-height: 250px;
}

.item {
  border-radius: 5px;
}

.item:hover {
  background-color: #f7edf0;
}
</style>
