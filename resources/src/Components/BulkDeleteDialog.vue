<template>
  <v-dialog v-model="dialogShown" max-width="600">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn icon>
            <v-icon color="error">mdi-delete</v-icon>
          </v-btn>
        </slot>
      </span>
    </template>

    <v-card class="py-6 px-2">
      <div class="text-center text-h6 mx-3">
        {{ title }}
      </div>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="secondary" text @click="dialogShown = false">
          Cancel
        </v-btn>
        <v-btn color="error" @click="bulkDelete"> Delete </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      default: "Delete All",
    },
  },
  data() {
    return {
      dialogShown: false,
    };
  },
  methods: {
    bulkDelete() {
      this.$emit("bulkDelete");
      this.dialogShown = false;
    },
  },
};
</script>

<style lang="scss">
</style>
