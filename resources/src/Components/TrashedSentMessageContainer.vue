<template>
  <v-container class="my-5 mx-auto">
    <v-row class="my-2">
      <v-col cols="12" sm="10" class="d-flex">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-bind="attrs"
              v-on="on"
              icon
              fab
              color="primary"
              :disabled="currUiState == uiStates.loadingMessages"
              @click="refreshMessages"
            >
              <v-icon x-large>mdi-restart</v-icon>
            </v-btn>
          </template>
          <span>Refresh</span>
        </v-tooltip>
        <v-text-field
          class="flex-grow-1"
          v-model="searchQuery"
          label="Search"
          clearable
        ></v-text-field>
      </v-col>
      <v-col cols="3" sm="1" class="mx-auto">
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-bind="attrs"
              v-on="on"
              color="secondary"
              fab
              dark
              @click="restoreAll"
              :loading="currBulkActionState == bulkActionStates.RestoringAll"
            >
              <v-icon> mdi-restore-alert </v-icon>
            </v-btn>
          </template>
          <span>Restore All</span>
        </v-tooltip>
      </v-col>
      <v-col cols="3" sm="1" class="mx-auto">
        <bulk-delete-dialog
          title="Delete All Trashed Sent Messages"
          @bulkDelete="deleteAll"
        >
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-bind="attrs"
                v-on="on"
                color="error"
                fab
                dark
                :loading="currBulkActionState == bulkActionStates.DeleteingAll"
              >
                <v-icon> mdi-delete-alert </v-icon>
              </v-btn>
            </template>
            <span>Delete All</span>
          </v-tooltip>
        </bulk-delete-dialog>
      </v-col>
    </v-row>
    <div class="mb-5 msg-list">
      <trashed-sent-message-details-dialog
        :message="message"
        v-for="message in messages"
        :key="message.id"
      >
        <trashed-sent-message
          :message="message"
          class="msg"
          @deleted="deleteMsg"
          @restored="deleteMsg"
        />
      </trashed-sent-message-details-dialog>
    </div>
    <div
      v-if="currUiState == uiStates.loadingMessages"
      class="text-center mt-8"
    >
      <v-progress-circular indeterminate color="primary"></v-progress-circular>
    </div>
    <div v-if="currUiState == uiStates.messagesLoaded" class="text-center mt-8">
      <v-btn @click="loadMessages">Load More</v-btn>
    </div>
    <div
      v-if="currUiState == uiStates.failedToLoadMessages"
      class="text-center mt-8"
    >
      <span class="error--text">Failed To Load</span>
      <v-icon @click="loadMessages" class="cur-point">mdi-restart</v-icon>
    </div>
    <div
      v-if="currUiState == uiStates.allMessagesLoaded"
      class="text-center mt-8 text--secondary"
    >
      No More To Show
    </div>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import MessagesLayout from "@/Layouts/Messages";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import TrashedReceivedMessage from "@/Components/TrashedReceivedMessage.vue";
import TrashedSentMessageDetailsDialog from "@/Components/TrashedSentMessageDetailsDialog.vue";
import TrashedSentMessage from "@/Components/TrashedSentMessage.vue";
import BulkDeleteDialog from "@/Components/BulkDeleteDialog.vue";

export default {
  props: {
    initialMessages: {
      type: Object,
      default: () => ({
        data: [],
        meta: {
          next_cursor: null,
        },
      }),
    },
  },
  components: {
    TrashedReceivedMessage,
    TrashedSentMessageDetailsDialog,
    TrashedSentMessage,
    BulkDeleteDialog,
  },
  layout: [MainLayout, MessagesLayout],
  mixins: [ScrollToTopOnCreate],
  data() {
    return {
      currUiState: null,
      currBulkActionState: null,

      searchQuery: null,

      messages: this.initialMessages.data,
      nextCursor: this.initialMessages.meta.next_cursor,

      abortController: null,
    };
  },
  computed: {
    authUser() {
      return this.$page.props.auth.user?.data;
    },
    uiStates() {
      return {
        loadingMessages: 0,
        messagesLoaded: 1,
        failedToLoadMessages: 2,
        allMessagesLoaded: 3,
      };
    },
    bulkActionStates() {
      return {
        None: 0,
        RestoringAll: 1,
        DeleteingAll: 2,
      };
    },
  },
  created() {
    this.currBulkActionState = this.bulkActionStates.None;
    if (this.nextCursor) {
      this.currUiState = this.uiStates.messagesLoaded;
    } else {
      this.currUiState = this.uiStates.allMessagesLoaded;
    }
  },
  methods: {
    scrollToElementTop() {
      window.scrollTo(0, this.$el.offsetTop);
    },
    async refreshMessages() {
      this.messages = [];
      this.nextCursor = null;
      await this.loadMessages();
      this.scrollToElementTop();
    },
    async loadMessages() {
      this.cancelOngoingRequests();

      try {
        this.currUiState = this.uiStates.loadingMessages;

        this.abortController = new AbortController();

        const res = await axios.get(
          route("users.trashedSentMessages.index", {
            user: this.authUser,
            cursor: this.nextCursor,
            searchQuery: this.searchQuery,
          }),
          { signal: this.abortController.signal }
        );

        this.messages = [...this.messages, ...res.data.data];
        this.nextCursor = res.data.meta.next_cursor;

        if (this.nextCursor) {
          this.currUiState = this.uiStates.messagesLoaded;
        } else {
          this.currUiState = this.uiStates.allMessagesLoaded;
        }
      } catch (error) {
        this.currUiState = this.uiStates.failedToLoadMessages;
      }
    },
    async restoreAll() {
      try {
        this.currBulkActionState = this.bulkActionStates.RestoringAll;

        await axios.post(
          route("users.notInTrashSentMessages.storeAll", {
            user: this.authUser,
          })
        );

        this.messages = [];
        this.currBulkActionState = this.bulkActionStates.None;

        this.$toasted.show("Restored All Trashed Sent Messages", {
          type: "success",
          duration: 3000,
          icon: "check-bold",
        });
      } catch (error) {
        this.currBulkActionState = this.bulkActionStates.None;

        this.$toasted.show("Failed To Restore Sent Messages", {
          type: "error",
          duration: 3000,
          icon: "close",
        });
      }
    },
    async deleteAll() {
      try {
        this.currBulkActionState = this.bulkActionStates.DeleteingAll;

        await axios.delete(
          route("users.trashedSentMessages.destroyAll", {
            user: this.authUser,
          })
        );

        this.messages = [];
        this.currBulkActionState = this.bulkActionStates.None;

        this.$toasted.show("Deleted All Trashed Sent Messages", {
          type: "success",
          duration: 3000,
          icon: "check-bold",
        });
      } catch (error) {
        this.currBulkActionState = this.bulkActionStates.None;

        this.$toasted.show("Failed To Delete Sent Messages", {
          type: "error",
          duration: 3000,
          icon: "close",
        });
      }
    },
    cancelOngoingRequests() {
      if (this.abortController) {
        this.abortController.abort();
        this.abortController = null;
      }
    },
    deleteMsg({ id }) {
      this.messages = this.messages.filter((m) => m.id != id);
    },
  },
  watch: {
    searchQuery: _.debounce(function (newVal) {
      if (!newVal || newVal.length >= 3) {
        this.refreshMessages();
      }
    }, 500),
    initialMessages(newVal) {
      (this.messages = newVal.data),
        (this.nextCursor = newVal.meta.next_cursor);
    },
  },
};
</script>

<style lang="scss" scoped>
.msg {
  border-bottom: 0.3px solid lightgrey;
}

.msg-list {
  border-top: 0.3px solid lightgrey;
}
</style>
