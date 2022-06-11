<template>
  <v-container class="my-5 px-5">
    <v-sheet elevation="3" class="pa-6" rounded="xl">
      <v-row>
        <v-col cols="12" sm="6" md="5">
          <v-img :src="avatarUrl" height="400px" class="rounded-lg">
            <div
              v-if="can['update-profile-avatar']"
              style="position: absolute; right: 20px; bottom: 20px"
            >
              <edit-avatar-dialog
                :initialAvatarUrl="avatarUrl"
                :updateUrl="
                  route('profiles.avatar.update', { profile: profileData })
                "
              />
            </div>
            <div
              v-if="profileHasAvatar && can['destroy-profile-avatar']"
              style="position: absolute; left: 20px; bottom: 20px"
            >
              <delete-avatar-dialog
                :destroyUrl="
                  route('profiles.avatar.destroy', { profile: profileData })
                "
              />
            </div>
          </v-img>
        </v-col>
        <v-col cols="12" sm="6" md="7">
          <div class="d-sm-flex justify-space-between align-center">
            <div class="d-flex align-center">
              <h2>
                {{ profileData.name }}
              </h2>
              <v-chip
                class="white--text ml-3"
                :color="userRoleToColorMap[userData.role]"
              >
                {{ userData.role }}
              </v-chip>
            </div>
            <edit-profile-dialog
              v-if="can['update-profile']"
              :profile="profileData"
            >
              <v-btn color="tertiary" class="white--text">
                Edit
                <v-icon right color="white">mdi-pencil</v-icon>
              </v-btn>
            </edit-profile-dialog>
          </div>
          <hr class="my-3" />
          <div class="mb-2">
            <span class="font-weight-bold">Username:</span>
            {{ userData.username }}
          </div>
          <div class="mb-2">
            <span class="font-weight-bold">Email:</span>
            {{ userData.email }}
          </div>
          <div class="mb-2">
            <span class="font-weight-bold">Joined:</span>
            {{ userData.createdAt | formatDate }}
          </div>
          <div class="mb-2">
            <span class="font-weight-bold">Title:</span>
            {{ profileData.title }}
          </div>
          <div class="mb-2">
            <span class="font-weight-bold">Education:</span>
            {{ profileData.education }}
          </div>
          <div class="mb-2">
            <span class="font-weight-bold">Address:</span>
            {{ profileData.address }}
          </div>
          <hr class="my-3" />
          <p class="text-justify">
            {{ profileData.bio }}
          </p>
        </v-col>
      </v-row>
    </v-sheet>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import EditAvatarDialog from "@/Components/EditAvatarDialog.vue";
import DeleteAvatarDialog from "@/Components/DeleteAvatarDialog.vue";
import EditProfileDialog from "@/Components/EditProfileDialog.vue";
import constants from "@/constants.js";

export default {
  props: {
    profile: {
      type: Object,
      required: true,
    },
    can: {
      type: Object,
      required: true,
    },
  },
  components: { EditAvatarDialog, DeleteAvatarDialog, EditProfileDialog },
  layout: MainLayout,
  mixins: [ScrollToTopOnCreate],
  computed: {
    profileData() {
      return this.profile.data;
    },
    userData() {
      return this.profileData.user;
    },
    userRoleToColorMap() {
      return constants.RoleColor;
    },
    avatarUrl() {
      const possibleAvatars = this.profileData.avatar;

      return possibleAvatars
        ? possibleAvatars.original || "/assets/anonymousUser.jpg"
        : "/assets/anonymousUser.jpg";
    },
    profileHasAvatar() {
      const possibleAvatars = this.profileData.avatar;

      return possibleAvatars.original || possibleAvatars.thumbnail;
    },
  },
  filters: {
    formatDate(date) {
      return moment(date).diffForHumans();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
