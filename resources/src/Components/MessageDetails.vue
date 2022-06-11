<template>
  <v-container>
    <div class="text-h4 my-5 font-weight-medium">
      {{ subject }}
    </div>
    <div class="d-sm-flex justify-space-between align-center">
      <div class="d-flex align-top">
        <v-avatar size="60">
          <v-img :src="senderAvatar" />
        </v-avatar>
        <div class="ml-3">
          <div class="font-weight-bold">
            {{ senderName }}
          </div>
          <div class="recepients d-flex align-start">
            <span class="mr-2"> To: </span>
            <span class="text--secondary">
              <span
                v-for="(receipent, index) in receipents"
                :key="receipent.id"
              >
                {{ receipent.name }}
                <span v-if="index != receipents.length - 1" class="separator">
                  ;
                </span>
              </span>
            </span>
          </div>
        </div>
      </div>
      <div class="text--secondary mt-5 mt-sm-0 ml-5">
        {{ date | formatDate }}
      </div>
    </div>
    <p class="mt-10 text-justify mx-auto" style="max-width: 800px">
      {{ body }}
    </p>
  </v-container>
</template>

<script>
export default {
  props: {
    subject: {
      type: String,
      required: true,
    },
    senderAvatar: {
      type: String,
      required: true,
    },
    senderName: {
      type: String,
      required: true,
    },
    receipents: {
      type: Array,
      required: true,
    },
    date: {
      type: String,
      required: true,
    },
    body: {
      type: String,
      required: true,
    },
  },
  filters: {
    formatDate(date) {
      return moment(date).format("MMM D, YYYY, h:mm A");
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
