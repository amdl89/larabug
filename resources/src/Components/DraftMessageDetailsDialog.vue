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
        <v-btn icon dark @click="closeDialog">
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title>Message Details</v-toolbar-title>
        <v-spacer></v-spacer>
        <v-toolbar-items>
          <v-btn
            v-if="!editing && !msgBeingSent"
            icon
            dark
            @click="editing = true"
          >
            Edit
          </v-btn>
          <v-btn
            v-if="!editing && msgBeingSent"
            icon
            dark
            @click="cancelSending"
          >
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-btn
            v-if="!editing && !msgBeingSent"
            dark
            text
            @click="sendMessage"
            :loading="msgBeingSent"
            :disabled="msgBeingSent"
          >
            Send
          </v-btn>
        </v-toolbar-items>
      </v-toolbar>
      <v-container v-if="editing">
        <message-form
          :message="message"
          :errors="errors"
          @data-reset="resetData"
          :allRecepients="allRecepients"
        >
          <template slot="saveActionButtons" slot-scope="{ data, reset }">
            <div class="d-flex justify-end">
              <v-btn
                @click="reset"
                color="secondary"
                class="mr-3"
                :disabled="currMessageState != messageStates.None"
              >
                Cancel
              </v-btn>
              <v-btn
                @click="updateDraft(data)"
                color="primary"
                class="mr-3"
                :loading="currMessageState == messageStates.Drafting"
                :disabled="currMessageState != messageStates.None"
              >
                <span class="white--text"> Update Draft </span>
                <v-icon color="white" right>mdi-pencil-plus</v-icon>
              </v-btn>
            </div>
          </template>
        </message-form>
      </v-container>
      <message-details
        v-else
        :subject="message.subject"
        :senderAvatar="senderAvatar"
        :senderName="senderName"
        :receipents="msgReceipents"
        :date="message.updatedAt"
        :body="message.body"
        class="mx-auto"
      />
    </v-card>
  </v-dialog>
</template>

<script>
import MessageDetails from "@/Components/MessageDetails.vue";
import MessageForm from "@/Components/MessageForm.vue";

export default {
  components: { MessageDetails, MessageForm },
  props: {
    message: {
      type: Object,
      required: true,
    },
    allRecepients: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      dialogShown: false,
      msgBeingSent: false,
      editing: false,
      errors: {},
      currMessageState: null,
      abortController: null,
    };
  },
  computed: {
    messageStates() {
      return {
        None: 0,
        Sending: 1,
        Drafting: 2,
      };
    },
    authUser() {
      return this.$page.props.auth.user?.data;
    },
    senderAvatar() {
      const possibleAvatars = this.authUser.profile?.avatar;

      return possibleAvatars
        ? possibleAvatars?.thumbnail ||
            possibleAvatars?.original ||
            "./assets/anonymousUser.jpg"
        : "./assets/anonymousUser.jpg";
    },
    senderName() {
      return this.authUser.profile?.name;
    },
    msgReceipents() {
      return this.message.receipents?.map((u) => ({
        id: u.id.toString(),
        name: u.profile.name,
      }));
    },
  },
  created() {
    this.currMessageState = this.messageStates.None;
  },
  methods: {
    closeDialog() {
      this.editing = false;
      this.dialogShown = false;
    },
    resetData() {
      this.errors = {};
      this.editing = false;
    },
    async updateDraft(data) {
      this.currMessageState = this.messageStates.Drafting;

      try {
        const res = await axios.put(
          route("users.draftMessages.update", {
            user: this.authUser,
            message: this.message,
          }),
          data
        );

        this.$toasted.show("Draft Updated Successfully", {
          type: "success",
          duration: 4000,
          icon: "check-bold",
        });

        this.editing = false;
        this.$emit("draftUpdated", { message: res.data.data });
      } catch (error) {
        if (error.response && error.response.status == 422) {
          this.errors = error.response.data.errors;
        } else {
          this.$toasted.show("Failed To Update Draft", {
            type: "error",
            duration: 4000,
            icon: "close",
          });
        }
      } finally {
        this.currMessageState = this.messageStates.None;
      }
    },
    async sendMessage() {
      try {
        this.msgBeingSent = true;
        this.abortController = new AbortController();

        await axios.post(
          route("users.draftMessages.send", { user: this.authUser }),
          { message: this.message.id },
          { signal: this.abortController.signal }
        );

        this.msgBeingSent = false;
        this.dialogShown = false;
        this.$emit("msgSent", { id: this.message.id });
      } catch (error) {
        this.msgBeingSent = false;
      }
    },
    cancelSending() {
      this.abortController?.abort();
      this.abortController = null;
      this.msgBeingSent = false;
    },
  },
};
</script>

<style lang="scss">
</style>
