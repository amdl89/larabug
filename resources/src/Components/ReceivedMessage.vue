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
            v-if="receivedMessageIsUnread"
            v-bind="attrs"
            v-on="on"
            icon
            @click.stop="markAsRead"
            :disabled="currMessageState != messageStates.NoActionBeingPerformed"
            :loading="currMessageState == messageStates.BeingMarkedAsRead"
          >
            <v-icon color="secondary">mdi-check</v-icon>
          </v-btn>
        </template>
        <span>Mark As Read</span>
      </v-tooltip>
      <v-tooltip bottom>
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            v-bind="attrs"
            v-on="on"
            icon
            @click.stop="moveToTrash"
            :disabled="currMessageState != messageStates.NoActionBeingPerformed"
            :loading="currMessageState == messageStates.BeingMovedToTrash"
          >
            <v-icon color="error">mdi-delete</v-icon>
          </v-btn>
        </template>
        <span>Move To Trash</span>
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
        BeingMarkedAsRead: 1,
        BeingMovedToTrash: 2,
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
        this.message.receivedInfo.receivedStatus == this.receivedStatus.Unread
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
    async markAsRead() {
      try {
        this.currMessageState = this.messageStates.BeingMarkedAsRead;

        await axios.post(
          route("users.readMessageReceptions.store", {
            user: this.authUser,
            messageReception: this.message.receivedInfo,
          })
        );

        this.currMessageState = this.messageStates.NoActionBeingPerformed;

        this.$emit("markedAsRead", { id: this.message.id });
      } catch (error) {
        this.currMessageState = this.messageStates.NoActionBeingPerformed;
      }
    },
    async moveToTrash() {
      try {
        this.currMessageState = this.messageStates.BeingMovedToTrash;

        await axios.post(
          route("users.trashedMessageReceptions.store", {
            user: this.authUser,
            messageReception: this.message.receivedInfo,
          })
        );

        this.currMessageState = this.messageStates.NoActionBeingPerformed;

        this.$emit("movedToTrash", { id: this.message.id });
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
