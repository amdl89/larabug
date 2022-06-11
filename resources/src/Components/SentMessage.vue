<template>
  <message
    :senderAvatar="senderAvatar"
    :senderName="senderName"
    :subject="message.subject"
    :body="message.body"
    :date="message.createdAt"
    :highlighted="false"
  >
    <template slot="action-buttons">
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
        BeingMovedToTrash: 2,
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
      return this.authUser?.profile?.name;
    },
  },
  methods: {
    async moveToTrash() {
      try {
        this.currMessageState = this.messageStates.BeingMovedToTrash;

        await axios.post(
          route("users.trashedSentMessages.store", {
            user: this.authUser,
            message: this.message,
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
