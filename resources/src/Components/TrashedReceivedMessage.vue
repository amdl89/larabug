<template>
  <message
    :senderAvatar="senderAvatar"
    :senderName="senderName"
    :subject="message.subject"
    :body="message.body"
    :date="message.receivedInfo.createdAt"
    :highlighted="receivedMessageIsUnread"
  >
    <template slot="action-buttons">
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            v-bind="attrs"
            v-on="on"
            icon
            @click.stop="restore"
            :disabled="currMessageState != messageStates.NoActionBeingPerformed"
            :loading="currMessageState == messageStates.BeingRestored"
          >
            <v-icon color="secondary">mdi-file-restore</v-icon>
          </v-btn>
        </template>
        <span>Restore</span>
      </v-tooltip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            v-bind="attrs"
            v-on="on"
            icon
            @click.stop="deleteMsg"
            :disabled="currMessageState != messageStates.NoActionBeingPerformed"
            :loading="currMessageState == messageStates.BeingDeleted"
          >
            <v-icon color="error">mdi-delete-circle</v-icon>
          </v-btn>
        </template>
        <span>Delete</span>
      </v-tooltip>
    </template>
  </message>
</template>

<script>
import constants from "@/constants.js";
import Message from "@/Components/Message.vue";

export default {
  components: { Message },
  props: {
    message: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      currMessageState: null,
    };
  },
  created() {
    this.currMessageState = this.messageStates.NoActionBeingPerformed;
  },
  computed: {
    messageStates() {
      return {
        NoActionBeingPerformed: 0,
        BeingRestored: 1,
        BeingDeleted: 2,
      };
    },
    authUser() {
      return this.$page.props.auth.user?.data;
    },
    receivedStatus() {
      return constants.ReceivedMessageStatus;
    },
    receivedMessageIsUnread() {
      return (
        this.message.receivedInfo.receivedStatus ==
        this.receivedStatus.UnreadTrashed
      );
    },
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
  },
  methods: {
    async restore() {
      try {
        this.currMessageState = this.messageStates.BeingRestored;

        await axios.post(
          route("users.notInTrashMessageReceptions.store", {
            user: this.authUser,
            messageReception: this.message.receivedInfo,
          })
        );

        this.currMessageState = this.messageStates.NoActionBeingPerformed;

        this.$emit("restored", { id: this.message.id });
      } catch (error) {
        this.currMessageState = this.messageStates.NoActionBeingPerformed;
      }
    },
    async deleteMsg() {
      try {
        this.currMessageState = this.messageStates.BeingDeleted;

        await axios.delete(
          route("users.trashedMessageReceptions.destroy", {
            user: this.authUser,
            messageReception: this.message.receivedInfo,
          })
        );

        this.currMessageState = this.messageStates.NoActionBeingPerformed;

        this.$emit("deleted", { id: this.message.id });
      } catch (error) {
        this.currMessageState = this.messageStates.NoActionBeingPerformed;
      }
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
