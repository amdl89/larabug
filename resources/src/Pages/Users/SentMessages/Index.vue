<template>
  <v-container class="my-5 mx-auto">
    <v-sheet elevation="6" rounded="xl" min-height="80vh" class="pa-6">
      <v-row class="my-2">
        <v-col cols="12">
          <v-text-field
            v-model="searchQuery"
            label="Search"
            clearable
          ></v-text-field>
        </v-col>
        <v-col cols="12" sm="1" class="d-flex justify-center align-top">
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
        </v-col>
        <v-col cols="12" sm="4">
          <v-select
            v-model="filters.createdRange"
            :items="allDateRanges"
            :menu-props="{ maxHeight: '200' }"
            label="Date"
            outlined
          ></v-select>
        </v-col>
        <v-col cols="12" sm="5">
          <v-select
            v-model="filters.receipents"
            :items="allUsers"
            multiple
            :menu-props="{ maxHeight: '200' }"
            label="Users"
            outlined
          ></v-select>
        </v-col>
        <v-col
          cols="12"
          sm="2"
          class="mb-5 mb-sm-0 d-flex justify-space-around"
        >
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn
                v-bind="attrs"
                v-on="on"
                color="error"
                fab
                dark
                @click="moveAllToTrash"
                :loading="movingAllToTrash"
              >
                <v-icon> mdi-delete-variant </v-icon>
              </v-btn>
            </template>
            <span>Move All To Trash</span>
          </v-tooltip>
        </v-col>
      </v-row>
      <div class="mb-5 msg-list">
        <sent-message-details-dialog
          :message="message"
          v-for="message in messages"
          :key="message.id"
        >
          <sent-message
            :message="message"
            class="msg"
            @movedToTrash="moveToTrash"
          />
        </sent-message-details-dialog>
      </div>
      <div
        v-if="currUiState == uiStates.loadingMessages"
        class="text-center mt-8"
      >
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>
      <div
        v-if="currUiState == uiStates.messagesLoaded"
        class="text-center mt-8"
      >
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
    </v-sheet>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import MessagesLayout from "@/Layouts/Messages";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import constants from "@/constants.js";
import SentMessageDetailsDialog from "@/Components/SentMessageDetailsDialog.vue";
import SentMessage from "@/Components/SentMessage.vue";

export default {
  layout: [MainLayout, MessagesLayout],
  mixins: [ScrollToTopOnCreate],
  components: { SentMessageDetailsDialog, SentMessage },
  props: {
    initialMessages: {
      type: Object,
      required: true,
    },
    users: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      currUiState: null,
      movingAllToTrash: false,

      searchQuery: null,
      filters: {
        createdRange: constants.DateRange.All,
        receipents: [],
      },

      messages: this.initialMessages.data,
      nextCursor: this.initialMessages.meta.next_cursor,

      abortController: null,
    };
  },
  computed: {
    allDateRanges() {
      return Object.values(constants.DateRange);
    },
    allUsers() {
      return this.users.data.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
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
  },
  created() {
    this.messages = this.initialMessages.data;
    this.nextCursor = this.initialMessages.meta.next_cursor;

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
          route("users.notInTrashSentMessages.index", {
            user: this.authUser,
            ..._.pickBy({
              searchQuery: this.searchQuery,
              filters: _.pickBy(this.filters),
              cursor: this.nextCursor,
            }),
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
        if (axios.isCancel(error)) {
          return;
        }
        this.currUiState = this.uiStates.failedToLoadMessages;
      }
    },
    moveAllToTrash() {
      this.$inertia.post(
        route("users.trashedSentMessages.storeAll", {
          user: this.authUser,
        }),
        {},
        {
          preserveState: false,
          preserveScroll: false,
          onStart: () => {
            this.movingAllToTrash = true;
          },
          onFinish: () => {
            this.movingAllToTrash = false;
          },
        }
      );
    },
    cancelOngoingRequests() {
      if (this.abortController) {
        this.abortController.abort();
        this.abortController = null;
      }
    },
    moveToTrash({ id }) {
      this.messages = this.messages.filter((m) => m.id != id);
    },
  },
  watch: {
    filters: {
      deep: true,
      handler() {
        this.refreshMessages();
      },
    },
    searchQuery: _.debounce(function (newVal) {
      if (!newVal || newVal.length >= 3) {
        this.refreshMessages();
      }
    }, 500),
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
