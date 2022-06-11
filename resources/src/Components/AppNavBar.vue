<template>
  <nav>
    <v-app-bar app elevation="3" light>
      <v-app-bar-nav-icon @click="toggleDrawer"> </v-app-bar-nav-icon>
      <v-app-bar-title class="grey--text text--lighten-2">
        <v-avatar size="50" class="mr-1">
          <v-img src="/assets/icon.png"></v-img>
        </v-avatar>
      </v-app-bar-title>
      <h2 class="d-inline">
        <span class="font-weight-light">Lara</span><span>BUG</span>
      </h2>
      <v-spacer />
      <div class="d-inline-block mr-6">
        <v-menu offset-y offset-x style="z-index: 100">
          <template v-slot:activator="{ on, attrs }">
            <v-avatar v-bind="attrs" v-on="on">
              <v-img :src="userAvatar" />
            </v-avatar>
          </template>
          <v-list dense class="mt-1 rounded-lg">
            <Link
              v-if="authUserCan['show-user-profile']"
              :href="route('users.profiles.show', { user: authUser })"
              as="span"
            >
              <v-list-item
                class="cur-point hover"
                :class="{
                  active: route().current('users.profiles.show'),
                }"
              >
                <v-icon class="mr-2">mdi-account</v-icon>
                <v-list-item-title>Profile</v-list-item-title>
              </v-list-item>
            </Link>
            <div class="dropdown-divider"></div>
            <Link :href="route('logout')" method="POST" as="span">
              <v-list-item class="cur-point hover">
                <v-icon class="mr-2">mdi-logout</v-icon>
                <v-list-item-title>Sign Out</v-list-item-title>
              </v-list-item>
            </Link>
          </v-list>
        </v-menu>
      </div>
    </v-app-bar>
    <v-navigation-drawer
      app
      dark
      color="secondary"
      v-model="drawerOpen"
      width="280px"
      style="z-index: 200"
    >
      <v-img src="/assets/navDrawerImg.jpg" height="120px" class="mb-2">
        <v-overlay absolute>
          <v-list>
            <v-list-item class="d-flex justify-center">
              <v-list-item-avatar size="70">
                <v-img :src="userAvatar" />
              </v-list-item-avatar>
              <v-list-item-title class="text-center">
                <h4 class="font-weight-bold">{{ authUser.profile.name }}</h4>
                <small>{{ authUser.username }}</small>
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-overlay>
      </v-img>
      <div class="px-4 mt-3">
        <nav-menu :menu="menu" :currRoute="currRoute" />
      </div>
    </v-navigation-drawer>
  </nav>
</template>

<script>
import NavMenu from "@/components/NavMenu.vue";
import { uuid } from "vue-uuid";

export default {
  components: { NavMenu },
  data() {
    return {
      drawerOpen: false,
      currRoute: route().current(),
    };
  },
  beforeUpdate() {
    this.currRoute = route().current();
  },
  computed: {
    authUser() {
      return this.$page.props.auth.user?.data;
    },
    authUserCan() {
      return this.$page.props.auth.can;
    },
    userAvatar() {
      const possibleAvatars = this.authUser?.profile?.avatar;

      return possibleAvatars
        ? possibleAvatars.thumbnail ||
            possibleAvatars.original ||
            "./assets/anonymousUser.jpg"
        : "./assets/anonymousUser.jpg";
    },
    menu() {
      return [
        {
          id: uuid.v4(),
          type: "link",
          icon: "mdi-view-dashboard",
          text: "Dashboard",
          route: { name: "home" },
          show: this.authUserCan["view-dashboard"],
        },
        {
          id: uuid.v4(),
          type: "link",
          icon: "mdi-email",
          text: "Messages",
          route: {
            name: "users.notInTrashReceivedMessages.index",
            params: {
              user: this.authUser,
            },
          },
          show: this.authUserCan["manage-own-message"],
        },
        {
          id: uuid.v4(),
          type: "dropdown",
          icon: "mdi-ladybug",
          text: "Tickets",
          show: true,
          children: [
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-playlist-star",
              text: "All Tickets",
              route: { name: "tickets.index" },
              show: this.authUserCan["view-all-ticket"],
            },
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-account-star",
              text: "Created By Me",
              route: {
                name: "users.tickets.index",
                params: {
                  user: this.authUser,
                },
              },
              show: this.authUserCan["view-all-user-ticket"],
            },
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-briefcase",
              text: "From my projects",
              route: {
                name: "users.allProjects.tickets.index",
                params: {
                  user: this.authUser,
                },
              },
              show: this.authUserCan["view-all-all-project-ticket"],
            },
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-pencil-ruler",
              text: "Assigned To Me",
              route: {
                name: "users.assignedTickets.index",
                params: {
                  user: this.authUser,
                },
              },
              show: this.authUserCan["view-all-assigned-ticket"],
            },
          ],
        },
        {
          id: uuid.v4(),
          type: "dropdown",
          icon: "mdi-notebook-multiple",
          text: "Projects",
          show: true,
          children: [
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-book",
              text: "All Projects",
              route: { name: "projects.index" },
              show: this.authUserCan["view-all-project"],
            },
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-account-cog",
              text: "My Projects",
              route: {
                name: "users.projects.index",
                params: {
                  user: this.authUser,
                },
              },
              show: this.authUserCan["view-all-user-project"],
            },
          ],
        },
        {
          id: uuid.v4(),
          type: "link",
          icon: "mdi-account-multiple",
          text: "Project Teams",
          route: { name: "allProjects.teams.edit" },
          show: this.authUserCan["edit-all-project-team"],
        },
        {
          id: uuid.v4(),
          type: "dropdown",
          icon: "mdi-briefcase-account",
          text: "Admin",
          show: this.authUserCan["admin"],
          children: [
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-account-multiple-check",
              text: "Users",
              route: { name: "users.index" },
              show: this.authUserCan["admin"],
            },
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-priority-high",
              text: "Ticket Priorities",
              route: { name: "ticketPriorities.index" },
              show: this.authUserCan["admin"],
            },
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-shape",
              text: "Ticket Types",
              route: { name: "ticketTypes.index" },
              show: this.authUserCan["admin"],
            },
            {
              id: uuid.v4(),
              type: "link",
              icon: "mdi-priority-low",
              text: "Project Priorities",
              route: { name: "projectPriorities.index" },
              show: this.authUserCan["admin"],
            },
          ],
        },
      ];
    },
  },
  methods: {
    toggleDrawer() {
      this.drawerOpen = !this.drawerOpen;
    },
  },
};
</script>

<style lang="scss">
.dropdown-divider {
  height: 0;
  margin: 0.5rem 0;
  overflow: hidden;
  border-top: 1px solid #e9ecef;
}

.cur-point {
  cursor: pointer !important;
}

.active {
  background-color: #f7edf0 !important;
}

.hover:hover {
  background-color: #f7edf0;
}
</style>
