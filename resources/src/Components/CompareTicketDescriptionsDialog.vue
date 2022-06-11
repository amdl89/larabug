<template>
  <v-dialog v-model="dialogShown" scrollable max-width="1100" max-height="700">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn color="tertiary" rounded text>
            <span class=""> Compare Description Values </span>
          </v-btn>
        </slot>
      </span>
    </template>

    <v-card class="py-6 px-2">
      <div class="text-center text-h5 mx-3 mb-4">
        Compare Ticket Descriptions
      </div>
      <v-row>
        <v-col cols="12" sm="6" class="mb-3 mb-sm-0">
          <h3 class="text-center">Old Value</h3>
          <p class="text-center mt-3">
            {{ oldDescription }}
          </p>
        </v-col>
        <v-col cols="12" sm="6">
          <h3 class="text-center">New Value</h3>
          <p class="text-center mt-3">
            {{ newDescription }}
          </p>
        </v-col>
      </v-row>
      <v-card-actions class="d-flex justify-end">
        <v-btn color="tertiary" text @click="dialogShown = false">
          Close
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  props: {
    oldDescription: {
      type: String,
      required: true,
    },
    newDescription: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
    };
  },
};
</script>

<style lang="scss">
</style>
