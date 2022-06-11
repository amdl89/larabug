<template>
  <th @click="changeSortOrder" class="hover">
    <div class="fill-height d-flex align-top">
      <div>
        {{ title }}
      </div>
      <div v-if="sortOrder === sortOrders.None" class="ml-1">
        <v-icon class="text--disabled">mdi-chevron-up</v-icon>
      </div>
      <div else class="ml-1">
        <span v-if="sortOrder === sortOrders.Asc">
          <v-icon color="black">mdi-arrow-up</v-icon>
        </span>
        <span v-else-if="sortOrder === sortOrders.Desc">
          <v-icon color="black">mdi-arrow-down</v-icon>
        </span>
      </div>
    </div>
  </th>
</template>

<script>
import constants from "@/constants";
export default {
  props: {
    title: {
      type: String,
      required: true,
    },
    initialSortOrder: {
      type: String,
      default: constants.SortOrder.None,
    },
  },
  data() {
    return {
      sortOrder: this.initialSortOrder,
    };
  },
  computed: {
    sortOrders() {
      return constants.SortOrder;
    },
  },
  methods: {
    changeSortOrder() {
      switch (this.sortOrder) {
        case this.sortOrders.None:
          this.sortOrder = this.sortOrders.Asc;
          break;
        case this.sortOrders.Asc:
          this.sortOrder = this.sortOrders.Desc;
          break;
        case this.sortOrders.Desc:
          this.sortOrder = this.sortOrders.None;
          break;
      }
      this.$emit("sortOrderChanged", { sortOrder: this.sortOrder });
    },
  },
};
</script>

<style lang="scss" scoped>
.hover:hover {
  background-color: rgba(245, 245, 245, 0.822) !important;
}
</style>
