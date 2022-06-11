<template>
  <v-container class="my-5 mx-auto">
    <v-sheet
      elevation="6"
      rounded="xl"
      min-height="70vh"
      class="pa-6"
      max-height="100vh"
      style="overflow-y: auto"
    >
      <h3 class="text-center mb-0 mt-2">RECEIVED FROM OTHERS</h3>
      <trashed-received-message-container :initialMessages="receivedMessages" />
    </v-sheet>
    <v-sheet
      elevation="6"
      rounded="xl"
      min-height="70vh"
      class="pa-6 mt-6"
      max-height="100vh"
      style="overflow-y: auto"
    >
      <h3 class="text-center mb-0 mt-2">SENT BY YOU</h3>
      <trashed-sent-message-container :initialMessages="sentMessages" />
    </v-sheet>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import MessagesLayout from "@/Layouts/Messages";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import TrashedReceivedMessageContainer from "@/Components/TrashedReceivedMessageContainer.vue";
import TrashedSentMessageContainer from "@/Components/TrashedSentMessageContainer.vue";

export default {
  components: { TrashedReceivedMessageContainer, TrashedSentMessageContainer },
  layout: [MainLayout, MessagesLayout],
  mixins: [ScrollToTopOnCreate],
  props: {
    receivedMessages: {
      type: Object,
      required: true,
    },
    sentMessages: {
      type: Object,
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
