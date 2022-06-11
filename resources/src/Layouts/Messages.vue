<template>
  <div id="msgLayout">
    <v-app-bar style="height: auto; margin-top: 64px" app flat>
      <v-sheet elevation="3" class="tertiary" style="width: 100%">
        <v-container class="pa-0 pb-1 mx-auto">
          <v-tabs
            class="d-block"
            v-model="tab"
            background-color="transparent"
            dark
            centered
            style="width: 100%"
          >
            <v-tab class="flex-grow-1">
              <v-progress-circular
                v-if="loadingTab == 0"
                indeterminate
                color="white"
                class="mr-2"
              ></v-progress-circular>
              <v-icon left v-else>mdi-inbox</v-icon>
              <span class="d-none d-sm-inline-block"> Inbox </span>
            </v-tab>
            <v-tab class="flex-grow-1">
              <v-progress-circular
                v-if="loadingTab == 1"
                indeterminate
                color="white"
                class="mr-2"
              ></v-progress-circular>
              <v-icon v-else left>mdi-send-check</v-icon>
              <span class="d-none d-sm-inline-block"> Sent </span>
            </v-tab>
            <v-tab class="flex-grow-1">
              <v-progress-circular
                v-if="loadingTab == 2"
                indeterminate
                color="white"
                class="mr-2"
              ></v-progress-circular>
              <v-icon v-else left>mdi-file</v-icon>
              <span class="d-none d-sm-inline-block"> Draft </span>
            </v-tab>
            <v-tab class="flex-grow-1">
              <v-progress-circular
                v-if="loadingTab == 3"
                indeterminate
                color="white"
                class="mr-2"
              ></v-progress-circular>
              <v-icon v-else left>mdi-trash-can</v-icon>
              <span class="d-none d-sm-inline-block"> Trash </span>
            </v-tab>
          </v-tabs>
        </v-container>
      </v-sheet>
    </v-app-bar>
    <div>
      <slot />
    </div>
    <new-message-dialog>
      <v-tooltip right>
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            v-bind="attrs"
            v-on="on"
            fab
            dark
            fixed
            bottom
            left
            color="primary"
          >
            <v-icon>mdi-message-plus</v-icon>
          </v-btn>
        </template>
        <span>New Message</span>
      </v-tooltip>
    </new-message-dialog>
  </div>
</template>

<script>
import NewMessageDialog from "@/Components/NewMessageDialog.vue";
export default {
  components: { NewMessageDialog },
  data() {
    return {
      tab: this.initialTabValue(),
      loadingTab: null,
      cancelToken: null,
    };
  },
  computed: {
    authUser() {
      return this.$page.props.auth.user?.data;
    },
  },
  methods: {
    initialTabValue() {
      switch (route().current()) {
        case "users.notInTrashReceivedMessages.index":
          return 0;

        case "users.notInTrashSentMessages.index":
          return 1;

        case "users.draftMessages.index":
          return 2;

        case "users.trashedMessages.index":
          return 3;
      }
    },
    requestOptions(tab) {
      return {
        onStart: () => {
          this.loadingTab = tab;
        },
        onSuccess: () => {
          this.loadingTab = null;
        },
        onCancel: () => {
          this.loadingTab = null;
        },
        onCancelToken: (token) => {
          this.cancelToken = token;
        },
      };
    },
  },
  watch: {
    tab(newVal) {
      if (this.cancelToken) {
        this.cancelToken.cancel();
      }

      switch (newVal) {
        case 0:
          this.$inertia.get(
            route("users.notInTrashReceivedMessages.index", {
              user: this.authUser,
            }),
            {},
            this.requestOptions(0)
          );
          break;

        case 1:
          this.$inertia.get(
            route("users.notInTrashSentMessages.index", {
              user: this.authUser,
            }),
            {},
            this.requestOptions(1)
          );
          break;

        case 2:
          this.$inertia.get(
            route("users.draftMessages.index", { user: this.authUser }),
            {},
            this.requestOptions(2)
          );
          break;

        case 3:
          this.$inertia.get(
            route("users.trashedMessages.index", { user: this.authUser }),
            {},
            this.requestOptions(3)
          );
          break;
      }
    },
  },
};
</script>

<style lang="scss">
#msgLayout .v-toolbar__content {
  height: auto !important;
  padding: 0px !important;
}
</style>
