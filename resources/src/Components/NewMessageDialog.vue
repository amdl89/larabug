<template>
  <v-dialog v-model="dialogShown" fullscreen>
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn icon>
            <v-icon color="info">mdi-plus</v-icon>
          </v-btn>
        </slot>
      </span>
    </template>
    <v-card>
      <v-toolbar dark color="secondary">
        <v-btn icon @click="dialogShown = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>New Message</v-toolbar-title>
      </v-toolbar>
      <v-container class="my-5">
        <message-form :message="message" :errors="errors">
          <template slot="saveActionButtons" slot-scope="{ data }">
            <div class="d-flex justify-end">
              <v-btn
                @click="draft(data)"
                color="tertiary"
                class="mr-3"
                :loading="currMessageState == messageStates.Drafting"
                :disabled="currMessageState != messageStates.None"
              >
                <span class="white--text"> Draft </span>
                <v-icon color="white" right>mdi-pencil-plus</v-icon>
              </v-btn>
              <v-btn
                @click="send(data)"
                color="primary"
                :loading="currMessageState == messageStates.Sending"
                :disabled="currMessageState != messageStates.None"
              >
                Send
                <v-icon right>mdi-send</v-icon>
              </v-btn>
            </div>
          </template>
        </message-form>
      </v-container>
    </v-card>
  </v-dialog>
</template>

<script>
import MessageForm from "@/Components/MessageForm.vue";
import constants from "@/constants.js";

export default {
  components: { MessageForm },
  data() {
    return {
      dialogShown: false,
      message: {},
      errors: {},
      currMessageState: null,
    };
  },
  computed: {
    authUser() {
      return this.$page.props.auth.user?.data;
    },
    messageStates() {
      return {
        None: 0,
        Sending: 1,
        Drafting: 2,
      };
    },
  },
  created() {
    this.currMessageState = this.messageStates.None;
  },
  methods: {
    draft(data) {
      data.sentStatus = constants.SentMessageStatus.Draft;
      this.saveMessage(data, {
        onStart: () => {
          this.currMessageState = this.messageStates.Drafting;
        },
      });
    },
    send(data) {
      data.sentStatus = constants.SentMessageStatus.Sent;
      this.saveMessage(data, {
        onStart: () => {
          this.currMessageState = this.messageStates.Sending;
        },
      });
    },
    saveMessage(data, options) {
      this.$inertia.post(
        route("users.sentMessages.store", { user: this.authUser }),
        data,
        {
          preserveScroll: false,
          preserveState: false,
          onError: (errors) => {
            this.errors = errors;
          },
          onSuccess: () => {
            this.message = {};
            this.errors = {};
            this.dialogShown = false;
          },
          onFinish: () => {
            this.currMessageState = this.messageStates.None;
          },
          ...options,
        }
      );
    },
  },
};
</script>

<style lang="scss">
</style>
