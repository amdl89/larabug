<template>
  <v-dialog v-model="dialogShown" fullscreen>
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn icon>
            <v-icon color="info">mdi-eye</v-icon>
          </v-btn>
        </slot>
      </span>
    </template>
    <v-card>
      <v-toolbar dark color="secondary" flat>
        <v-btn icon dark @click="dialogShown = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>Message Details</v-toolbar-title>
      </v-toolbar>
      <message-details
        :subject="message.subject"
        :senderAvatar="senderAvatar"
        :senderName="senderName"
        :receipents="msgReceipents"
        :date="message.receivedInfo.createdAt"
        :body="message.body"
        class="mx-auto"
      />
    </v-card>
  </v-dialog>
</template>

<script>
import MessageDetails from "@/Components/MessageDetails.vue";

export default {
  components: { MessageDetails },
  props: {
    message: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
    };
  },
  computed: {
    senderAvatar() {
      const possibleAvatars = this.message.sender?.profile?.avatar;

      return possibleAvatars
        ? possibleAvatars?.thumbnail ||
            possibleAvatars?.original ||
            "./assets/anonymousUser.jpg"
        : "./assets/anonymousUser.jpg";
    },
    senderName() {
      return this.message.sender?.profile?.name;
    },
    msgReceipents() {
      return this.message.receipents.map((u) => ({
        id: u.id,
        name: u.profile.name,
      }));
    },
    authUser() {
      return this.$page.props.auth.user?.data;
    },
  },
};
</script>

<style lang="scss">
</style>
