<template>
  <v-row
    class="px-2 align-top cur-point d-flex align-start align-sm-center"
    :class="{ overlay: highlighted }"
  >
    <v-col cols="1" class="py-0">
      <v-avatar size="40">
        <v-img :src="senderAvatar" />
      </v-avatar>
    </v-col>
    <v-col cols="11" class="py-0">
      <v-row class="d-flex align-top align-sm-center">
        <v-col cols="12" sm="2">
          <span
            :class="{
              'font-weight-bold': highlighted,
            }"
          >
            <div class="truncateText ml-6 ml-sm-0">
              {{ senderName }}
            </div>
          </span>
        </v-col>
        <v-col cols="12" sm="7">
          <span
            :class="{
              'font-weight-bold': highlighted,
            }"
          >
            <div class="truncateText">
              {{ subject }}
              -
              <br class="d-block d-sm-none" />
              <span class="font-weight-light gray--text">
                {{ body | truncate }}
              </span>
            </div>
          </span>
        </v-col>
        <v-col cols="6" sm="2">
          <div
            :class="{
              'font-weight-bold': highlighted,
            }"
            class="d-flex justify-end"
          >
            <div class="truncateText">
              {{ date | formatDate }}
            </div>
          </div>
        </v-col>
        <v-col cols="6" sm="1" class="d-flex justify-end">
          <slot name="action-buttons" />
        </v-col>
      </v-row>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    senderAvatar: {
      type: String,
      required: true,
    },
    senderName: {
      type: String,
      required: true,
    },
    subject: {
      type: String,
      required: true,
    },
    body: {
      type: String,
      required: true,
    },
    date: {
      type: String,
      required: true,
    },
    highlighted: {
      type: Boolean,
      default: false,
    },
  },
  filters: {
    truncate(str) {
      return _.truncate(str, { length: 50, omission: "" });
    },
    formatDate(date) {
      if (moment(date).isSame(moment.now(), "day")) {
        return moment(date).format("h:mm A");
      }
      return moment(date).format("D MMM");
    },
  },
};
</script>

<style lang="scss" scoped>
.truncateText {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.overlay {
  background-color: #f3f5f5;
}
.row {
  margin: 0px !important;
}
.col-1 {
  padding: 7px !important;
}
</style>
