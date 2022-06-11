<template>
  <v-container class="my-5 px-5">
    <v-row>
      <v-col cols="12" sm="6" md="7">
        <v-sheet elevation="3" rounded="xl">
          <v-img :src="projectCoverImage" height="200px" class="rounded-t-xl">
            <div
              v-if="can['update-project-cover-image']"
              style="position: absolute; right: 20px; bottom: 20px"
            >
              <edit-cover-image-dialog
                :initialCoverImageUrl="projectCoverImage"
                :updateUrl="
                  route('projects.coverImage.update', { project: projectData })
                "
              />
            </div>
            <div
              v-if="projectHasCoverImage && can['destroy-project-cover-image']"
              style="position: absolute; left: 20px; bottom: 20px"
            >
              <delete-cover-image-dialog
                :destroyUrl="
                  route('projects.coverImage.destroy', { project: projectData })
                "
              />
            </div>
          </v-img>
          <div class="pa-6">
            <h2 class="text-center">
              {{ projectData.title }}
            </h2>
            <p class="text-justify mt-3">
              {{ projectData.description }}
            </p>
            <hr />
            <v-row class="mt-4">
              <v-col cols="12" sm="6">
                <span class="font-weight-bold">Created At:</span>
                {{ projectData.createdAt | formatDate }}
              </v-col>
              <v-col cols="12" sm="6">
                <span class="font-weight-bold">Last Updated At:</span>
                {{ projectData.updatedAt | formatDate }}
              </v-col>
            </v-row>
            <v-row class="mt-4">
              <v-col cols="12" sm="6">
                <span class="font-weight-bold">Deadline:</span>
                <span v-if="projectData.deadline">
                  {{ projectData.deadline | formatDate }}
                </span>
                <span v-else class="text--secondary"> Not Available </span>
              </v-col>
              <v-col cols="12" sm="6">
                <span class="font-weight-bold">Supervisor:</span>
                <span v-if="projectData.supervisor">
                  <Link
                    :href="
                      route('users.profiles.show', {
                        user: projectData.supervisor,
                      })
                    "
                    as="span"
                    class="cur-point"
                  >
                    {{ projectData.supervisor.profile.name }}
                  </Link>
                </span>
                <assign-supervisor-dialog
                  v-else-if="can['update-project-supervisor']"
                  :project="projectData"
                  :supervisors="supervisors"
                  class="ml-2"
                />
                <div v-else class="text--secondary ml-2">Not Assigned</div>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" sm="4">
                <span class="font-weight-bold">Status:</span>
                <v-chip
                  class="white--text ml-1"
                  :color="projectStatusToColorMap[projectData.status]"
                >
                  {{ projectData.status }}
                </v-chip>
              </v-col>
              <v-col cols="12" sm="4">
                <span class="font-weight-bold">Priority:</span>
                <v-chip
                  v-if="projectData.priority"
                  class="white--text"
                  :color="projectData.priority.color"
                >
                  {{ projectData.priority.name }}
                </v-chip>
                <span v-else class="text--secondary"> Not Available </span>
              </v-col>
              <v-col cols="12" sm="4">
                <a
                  v-if="can['view-all-project-ticket']"
                  :href="
                    route('projects.tickets.index', { project: projectData })
                  "
                  target="_blank"
                  class="white--text text-decoration-none"
                >
                  <v-btn color="tertiary">
                    <span class="white--text">View Tickets</span>
                    <v-icon right color="white">mdi-eye</v-icon>
                  </v-btn>
                </a>
              </v-col>
            </v-row>
            <hr class="mt-4" />
            <div class="d-sm-flex justify-space-around mt-4">
              <div v-if="can['store-project-attachment']">
                <new-attachment-dialog
                  :storeUrl="
                    route('projects.attachments.store', {
                      project: projectData,
                    })
                  "
                />
              </div>
              <div v-if="can['update-project']">
                <edit-project-dialog
                  :canAssignProject="can['update-project-supervisor']"
                  :project="projectData"
                  :statuses="allProjectStatuses"
                  :priorities="allProjectPriorities"
                  :supervisors="allSupervisors"
                >
                  <v-btn color="primary">
                    Edit
                    <v-icon right color="white">mdi-pencil</v-icon>
                  </v-btn>
                </edit-project-dialog>
              </div>
              <div v-if="can['destroy-project']">
                <delete-project-dialog
                  :project="projectData"
                  :redirectUrl="route('home')"
                >
                  <v-btn color="error">
                    Delete
                    <v-icon right color="white">mdi-delete</v-icon>
                  </v-btn>
                </delete-project-dialog>
              </div>
            </div>
          </div>
        </v-sheet>
      </v-col>
      <v-col cols="12" sm="6" md="5">
        <v-sheet elevation="3" rounded="xl" class="attachment-con" color="#eee">
          <h3 class="text-center white--text tertiary py-2">
            Attachments ({{ projectAttachmentsCount }})
          </h3>
          <div class="pa-6">
            <v-row>
              <v-col cols="12" v-if="projectAttachmentsCount">
                <v-sheet
                  elevation="3"
                  rounded
                  class="pa-6 mb-4"
                  v-for="attachment in projectAttachments"
                  :key="attachment.id"
                >
                  <div class="font-weight-bold">
                    {{ attachment.name }}
                  </div>
                  <p class="mt-4 text-justify">
                    {{ attachment.notes }}
                  </p>
                  <hr class="mt-2" />
                  <p class="mt-4">
                    <span class="font-weight-bold">File name:</span>
                    {{ attachment.file.fileName }}
                  </p>
                  <p class="mt-4">
                    <span class="font-weight-bold">Uploaded At:</span>
                    {{ attachment.file.createdAt | formatDate }}
                  </p>
                  <p class="mt-4">
                    <span class="font-weight-bold">Type:</span>
                    {{ attachment.file.mimeType }}
                  </p>
                  <p class="mt-4">
                    <span class="font-weight-bold">Uploaded By:</span>
                    <span v-if="attachment.uploader">
                      <Link
                        :href="
                          route('users.profiles.show', {
                            user: attachment.uploader,
                          })
                        "
                        as="span"
                        class="cur-point"
                      >
                        {{ attachment.uploader.profile.name }}
                      </Link>
                    </span>
                    <span v-else class="text--secondary"> Not Available </span>
                  </p>
                  <hr class="mt-2" />
                  <div class="d-flex mt-4 justify-space-between">
                    <a
                      v-if="Boolean(attachment['can-view'])"
                      :href="
                        route('attachments.attachedFiles.show', { attachment })
                      "
                      class="white--text text-decoration-none"
                    >
                      <v-btn color="primary">
                        {{ attachment.file.size }}
                        <v-icon right color="white">mdi-download</v-icon>
                      </v-btn>
                    </a>
                    <delete-attachment-dialog
                      v-if="Boolean(attachment['can-delete'])"
                      :attachment="attachment"
                    >
                      <v-btn color="error">
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </delete-attachment-dialog>
                  </div>
                </v-sheet>
                <v-pagination
                  v-if="projectAttachments.length"
                  class="mr-5"
                  v-model="attachmentPage"
                  :length="attachmentNoOfPages"
                  @input="reloadPage(['attachments'])"
                ></v-pagination>
              </v-col>
              <v-col cols="12" v-else>
                <div class="text-center font-weight-bold">
                  No Attachments Yet!
                </div>
              </v-col>
            </v-row>
          </div>
        </v-sheet>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12" sm="4">
        <v-sheet elevation="6" rounded="lg" class="pa-6">
          <ticket-priority-chart
            :ticketPriorityChartData="ticketPriorityChartData"
          />
        </v-sheet>
      </v-col>
      <v-col cols="12" sm="4">
        <v-sheet elevation="6" rounded="lg" class="pa-6">
          <ticket-status-chart :ticketStatusChartData="ticketStatusChartData" />
        </v-sheet>
      </v-col>
      <v-col cols="12" sm="4">
        <v-sheet elevation="6" rounded="lg" class="pa-6">
          <ticket-type-chart :ticketTypeChartData="ticketTypeChartData" />
        </v-sheet>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12">
        <v-sheet elevation="6" rounded="xl" style="background: #eee">
          <h3
            class="text-center py-2 white--text secondary darken-4 rounded-t-xl"
          >
            Project Members ({{ projectTeamCount }})
          </h3>
          <div class="pa-6">
            <v-row>
              <v-col cols="12">
                <div class="d-sm-flex justify-space-between align-center pa-2">
                  <v-sheet elevation="3" rounded="xl" class="pa-3">
                    <span class="font-weight-bold"> Supervisor: </span>
                    <span v-if="projectData.supervisor" class="cur-point">
                      <Link
                        :href="
                          route('users.profiles.show', {
                            user: projectData.supervisor,
                          })
                        "
                        as="span"
                        class="cur-point"
                      >
                        <v-avatar size="60px" class="ml-3">
                          <img
                            :src="
                              projectData.supervisor.profile.avatar.thumbnail ||
                              projectData.supervisor.profile.avatar.original ||
                              './assets/anonymousUser.jpg'
                            "
                          />
                        </v-avatar>
                        {{ projectData.supervisor.profile.name }}
                      </Link>
                    </span>
                    <span v-else class="text--secondary"> Not Assigned </span>
                  </v-sheet>
                  <div v-if="can['edit-all-project-team']" class="mt-3 mt-sm-0">
                    <Link
                      :href="
                        route('allProjects.teams.edit', {
                          initialProject: projectData.id,
                        })
                      "
                      as="span"
                    >
                      <v-btn class="success">
                        Manage Team
                        <v-icon right>mdi-account-multiple</v-icon>
                      </v-btn>
                    </Link>
                  </div>
                </div>
              </v-col>
            </v-row>
            <v-row>
              <v-col cols="12" sm="6">
                <h3 class="mb-1">Devs:</h3>
                <v-sheet
                  elevation="3"
                  v-if="projectData.devs.length"
                  style="overflow-y: auto; max-height: 60vh; background: #fff"
                  class="pa-2 rounded-xl"
                >
                  <Link
                    :href="route('users.profiles.show', { user: dev })"
                    as="div"
                    v-for="dev in projectData.devs"
                    :key="dev.id"
                    class="user-list-item cur-point py-3"
                  >
                    <v-avatar size="60px" class="ml-3">
                      <img
                        :src="
                          dev.profile.avatar.thumbnail ||
                          dev.profile.avatar.original ||
                          './assets/anonymousUser.jpg'
                        "
                      />
                    </v-avatar>
                    {{ dev.profile.name }}
                  </Link>
                </v-sheet>
                <div v-else>
                  <h2 class="text-center">No Devs Assigned</h2>
                </div>
              </v-col>
              <v-col class="mt-2 mt-sm-0" cols="12" sm="6">
                <h3 class="mb-1">Testers:</h3>
                <v-sheet
                  elevation="3"
                  v-if="projectData.devs.length"
                  style="overflow-y: auto; max-height: 60vh; background: #fff"
                  class="pa-2 rounded-xl"
                >
                  <Link
                    :href="route('users.profiles.show', { user: tester })"
                    as="div"
                    v-for="tester in projectData.testers"
                    :key="tester.id"
                    class="user-list-item cur-point py-3"
                  >
                    <v-avatar size="60px" class="ml-3">
                      <img
                        :src="
                          tester.profile.avatar.thumbnail ||
                          tester.profile.avatar.original ||
                          './assets/anonymousUser.jpg'
                        "
                      />
                    </v-avatar>
                    {{ tester.profile.name }}
                  </Link>
                </v-sheet>
                <div v-else>
                  <h2 class="text-center">No Testers Assigned</h2>
                </div>
              </v-col>
            </v-row>
          </div>
        </v-sheet>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import constants from "@/constants.js";
import AssignSupervisorDialog from "@/Components/AssignSupervisorDialog.vue";
import NewAttachmentDialog from "@/Components/NewAttachmentDialog.vue";
import EditProjectDialog from "@/Components/EditProjectDialog.vue";
import DeleteProjectDialog from "@/Components/DeleteProjectDialog.vue";
import EditCoverImageDialog from "@/Components/EditCoverImageDialog.vue";
import DeleteCoverImageDialog from "@/Components/DeleteCoverImageDialog.vue";
import DeleteAttachmentDialog from "@/Components/DeleteAttachmentDialog.vue";
import TicketPriorityChart from "@/Components/Reusable/TicketPriorityChart";
import TicketStatusChart from "@/Components/Reusable/TicketStatusChart";
import TicketTypeChart from "@/Components/Reusable/TicketTypeChart";

export default {
  components: {
    AssignSupervisorDialog,
    NewAttachmentDialog,
    EditProjectDialog,
    DeleteProjectDialog,
    EditCoverImageDialog,
    DeleteCoverImageDialog,
    DeleteAttachmentDialog,
    TicketPriorityChart,
    TicketStatusChart,
    TicketTypeChart,
  },
  mixins: [ScrollToTopOnCreate],
  layout: MainLayout,
  filters: {
    formatDate(date) {
      return moment(date).format("DD MMMM YYYY");
    },
  },
  props: {
    project: {
      type: Object,
      required: true,
    },
    supervisors: {
      type: Object,
      required: true,
    },
    priorities: {
      type: Object,
      required: true,
    },
    attachments: {
      type: Object,
      required: true,
    },
    ticketPriorityChartData: {
      type: Object,
      required: true,
    },
    ticketStatusChartData: {
      type: Object,
      required: true,
    },
    ticketTypeChartData: {
      type: Object,
      required: true,
    },
    can: {
      type: Object,
      required: true,
    },
  },
  data() {
    const { attachmentPage } = route().params;

    return {
      attachmentPage: parseInt(attachmentPage) || 1,
    };
  },
  computed: {
    projectData() {
      return this.project.data;
    },
    projectStatusToColorMap() {
      return constants.ProjectStatusColor;
    },
    allProjectStatuses() {
      return Object.values(constants.ProjectStatus);
    },
    allProjectPriorities() {
      return this.priorities.data.map((pp) => ({
        value: pp.id.toString(),
        text: pp.name,
      }));
    },
    allSupervisors() {
      return this.supervisors.data.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
    projectCoverImage() {
      const possibleCoverImages = this.projectData.coverImage;

      return possibleCoverImages
        ? possibleCoverImages.thumbnail ||
            possibleCoverImages.original ||
            "/assets/defaultProjectCoverImage.png"
        : "/assets/defaultProjectCoverImage.png";
    },
    projectHasCoverImage() {
      const possibleCoverImages = this.projectData.coverImage;

      return possibleCoverImages.thumbnail || possibleCoverImages.original;
    },
    projectAttachments() {
      return this.attachments.data;
    },
    projectAttachmentsCount() {
      return this.attachments.meta.total;
    },
    attachmentNoOfPages() {
      return this.attachments.meta.last_page;
    },
    projectTeamCount() {
      return (
        this.projectData.devs.length +
        this.projectData.testers.length +
        (this.projectData.supervisor ? 1 : 0)
      );
    },
  },
  methods: {
    reloadPage(partialData = null, optherOptions = {}) {
      const reqOptions = {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        ...optherOptions,
      };

      if (partialData) {
        reqOptions.only = partialData;
      }

      this.$inertia.get(
        route("projects.show", { project: this.projectData }),
        { attachmentPage: this.attachmentPage },
        reqOptions
      );
    },
  },
};
</script>

<style lang="scss" scoped>
.attachment-con {
  overflow-y: auto;
  max-height: 600px;
}

.user-list-item {
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  border-bottom: 1px solid grey;
}

.user-list-item:last-of-type {
  border-bottom: none;
}

.user-list-item:hover {
  background-color: #f7edf0;
}
</style>
