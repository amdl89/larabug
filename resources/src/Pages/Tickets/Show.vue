<template>
  <v-container class="my-5 px-5">
    <v-row>
      <v-col cols="12" sm="6" md="7">
        <v-sheet elevation="3" rounded="xl" class="pa-6">
          <h2 class="text-center">
            {{ ticketData.title }}
          </h2>
          <p class="text-justify mt-3">
            {{ ticketData.description }}
          </p>
          <hr />
          <v-row class="mt-4">
            <v-col cols="12" sm="6">
              <span class="font-weight-bold">Created At:</span>
              {{ ticketData.createdAt | formatDate }}
            </v-col>
            <v-col cols="12" sm="6">
              <span class="font-weight-bold">Last Updated At:</span>
              {{ ticketData.updatedAt | formatDate }}
            </v-col>
          </v-row>
          <v-row class="mt-4">
            <v-col cols="12" sm="6">
              <span class="font-weight-bold">Created By:</span>
              <span v-if="ticketData.creator">
                <Link
                  :href="
                    route('users.profiles.show', { user: ticketData.creator })
                  "
                  as="span"
                  class="cur-point"
                >
                  {{ ticketData.creator.profile.name }}
                </Link>
              </span>
              <span v-else class="text--secondary"> Not Available </span>
            </v-col>
            <v-col cols="12" sm="6">
              <span class="font-weight-bold">Assigned To:</span>
              <span v-if="ticketData.assignee">
                <Link
                  :href="
                    route('users.profiles.show', { user: ticketData.assignee })
                  "
                  as="span"
                  class="cur-point"
                >
                  {{ ticketData.assignee.profile.name }}
                </Link>
              </span>
              <assign-dev-dialog
                v-else-if="can['store-ticket-assignee']"
                :ticket="ticketData"
                class="ml-2"
              />
              <div v-else class="text--secondary ml-2">Not Assigned</div>
            </v-col>
          </v-row>
          <v-row>
            <v-col>
              <span class="font-weight-bold">Project:</span>
              <Link
                :href="route('projects.show', { project: ticketData.project })"
                as="span"
                class="cur-point"
              >
                {{ ticketData.project.title }}
              </Link>
            </v-col>
          </v-row>
          <v-row>
            <v-col cols="12" sm="4">
              <span class="font-weight-bold">Status:</span>
              <v-chip
                class="white--text ml-1"
                :color="ticketStatusToColorMap[ticketData.status]"
              >
                {{ ticketData.status }}
              </v-chip>
            </v-col>
            <v-col cols="12" sm="4">
              <span class="font-weight-bold">Priority:</span>
              <v-chip
                v-if="ticketData.priority"
                class="white--text ml-1"
                :color="ticketData.priority.color"
              >
                {{ ticketData.priority.name }}
              </v-chip>
              <span v-else class="text--secondary"> Not Available </span>
            </v-col>
            <v-col cols="12" sm="4">
              <span class="font-weight-bold">Type:</span>
              <v-chip
                v-if="ticketData.type"
                class="white--text ml-1"
                :color="ticketData.type.color"
              >
                {{ ticketData.type.name }}
              </v-chip>
              <span v-else class="text--secondary"> Not Available </span>
            </v-col>
          </v-row>
          <hr class="mt-4" />
          <div class="d-sm-flex justify-space-around mt-4">
            <div v-if="can['store-ticket-attachment']">
              <new-attachment-dialog
                :storeUrl="
                  route('tickets.attachments.store', { ticket: ticketData })
                "
              />
            </div>
            <div v-if="can['update-ticket']">
              <edit-ticket-dialog
                :canAssignTicket="can['store-ticket-assignee']"
                :ticket="ticketData"
                :types="types"
                :priorities="priorities"
              >
                <v-btn color="primary">
                  Edit
                  <v-icon right color="white">mdi-pencil</v-icon>
                </v-btn>
              </edit-ticket-dialog>
            </div>
            <div v-if="can['destroy-ticket']">
              <delete-ticket-dialog
                :ticket="ticketData"
                :redirectUrl="route('home')"
              >
                <v-btn color="error">
                  Delete
                  <v-icon right color="white">mdi-delete</v-icon>
                </v-btn>
              </delete-ticket-dialog>
            </div>
          </div>
        </v-sheet>
      </v-col>
      <v-col cols="12" sm="6" md="5">
        <v-sheet elevation="3" rounded="xl" class="attachment-con" color="#eee">
          <h3 class="text-center white--text tertiary py-2">
            Attachments ({{ ticketAttachmentsCount }})
          </h3>
          <div class="pa-6">
            <v-row>
              <v-col cols="12" v-if="ticketAttachmentsCount">
                <v-sheet
                  elevation="3"
                  rounded
                  class="pa-6 mb-4"
                  v-for="attachment in ticketAttachments"
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
                  v-if="ticketAttachments.length"
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
      <v-col cols="12">
        <v-sheet elevation="3" rounded="xl" style="overflow-y: auto">
          <h3 class="text-center py-2 white--text teal darken-4">
            Ticket History ({{ ticketChangeLogsCount }})
          </h3>
          <div class="pa-6">
            <v-row class="mt-2">
              <v-col cols="12" sm="3">
                <v-select
                  v-model="ticketChangeLogFilters.property"
                  :items="ticketProperties"
                  label="Property"
                  outlined
                  clearable
                  :menu-props="{ maxHeight: '200' }"
                >
                </v-select>
              </v-col>
              <v-col cols="12" sm="3">
                <v-select
                  v-model="ticketChangeLogFilters.initiator"
                  :items="ticketUsers"
                  label="Users"
                  outlined
                  clearable
                  :menu-props="{ maxHeight: '200' }"
                >
                </v-select>
              </v-col>
              <v-col cols="12" sm="3">
                <v-select
                  v-model="ticketChangeLogFilters.dateRange"
                  :items="allDateRanges"
                  label="Date Range"
                  outlined
                  clearable
                  :menu-props="{ maxHeight: '200' }"
                >
                </v-select>
              </v-col>
              <v-col cols="12" sm="3">
                <v-select
                  v-model="ticketChangeLogSortOrder"
                  :items="allSortOrders"
                  label="Sort Order"
                  outlined
                  :menu-props="{ maxHeight: '200' }"
                >
                </v-select>
              </v-col>
            </v-row>
            <div style="min-height: 50vh">
              <div v-if="loadingChangeLogs" class="mt-3 text-center">
                <v-progress-circular
                  indeterminate
                  color="primary"
                ></v-progress-circular>
              </div>
              <v-simple-table v-else>
                <thead>
                  <tr>
                    <th class="text-left text-h6 font-weight-bold py-3">
                      Date
                    </th>
                    <th class="text-left text-h6 font-weight-bold py-3">
                      Property
                    </th>
                    <th class="text-left text-h6 font-weight-bold py-3">
                      Old Value
                    </th>
                    <th class="text-left text-h6 font-weight-bold py-3">
                      New Value
                    </th>
                    <th class="text-left text-h6 font-weight-bold py-3">
                      User
                    </th>
                  </tr>
                </thead>
                <tbody v-if="ticketChangeLogsCount">
                  <tr v-for="changeLog in ticketChangeLogs" :key="changeLog.id">
                    <td class="py-5">{{ changeLog.date | formatDate }}</td>
                    <td class="py-5">{{ changeLog.resolvedData.property }}</td>
                    <td
                      class="py-5"
                      v-if="changeLog.resolvedData.property != 'Description'"
                    >
                      <span v-if="changeLog.resolvedData.oldValue">
                        {{ changeLog.resolvedData.oldValue }}
                      </span>
                      <span v-else class="text--secondary"> No Value </span>
                    </td>
                    <td
                      class="py-5"
                      v-if="changeLog.resolvedData.property != 'Description'"
                    >
                      <span v-if="changeLog.resolvedData.newValue">
                        {{ changeLog.resolvedData.newValue }}
                      </span>
                      <span v-else class="text--secondary"> No Value </span>
                    </td>
                    <td
                      v-if="changeLog.resolvedData.property == 'Description'"
                      colspan="2"
                      class="text-center"
                    >
                      <compare-ticket-descriptions-dialog
                        :oldDescription="changeLog.resolvedData.oldValue"
                        :newDescription="changeLog.resolvedData.newValue"
                      />
                    </td>
                    <td class="py-5">
                      <span v-if="changeLog.initiator">
                        <Link
                          :href="
                            route('users.profiles.show', {
                              user: changeLog.initiator,
                            })
                          "
                          as="span"
                          class="cur-point"
                        >
                          {{ changeLog.initiator.profile.name }}
                        </Link>
                      </span>
                      <span v-else class="text--secondary">
                        Not Available
                      </span>
                    </td>
                  </tr>
                </tbody>
              </v-simple-table>
              <div
                v-if="ticketChangeLogsCount && !loadingChangeLogs"
                class="text-center"
              >
                <v-pagination
                  v-model="ticketChangeLogPage"
                  :length="changeLogNoOfPages"
                  @input="reloadPage(['changeLogs'])"
                ></v-pagination>
              </div>
              <div
                v-else-if="!loadingChangeLogs"
                class="text-center mt-3 font-weight-bold"
              >
                <span> Nothing To Show </span>
              </div>
            </div>
          </div>
        </v-sheet>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <comment-container
          :ticket="ticketData"
          :canAddComment="can['store-comment']"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import constants from "@/constants.js";
import AssignDevDialog from "@/Components/AssignDevDialog.vue";
import DeleteTicketDialog from "@/Components/DeleteTicketDialog.vue";
import EditTicketDialog from "@/Components/EditTicketDialog.vue";
import NewAttachmentDialog from "@/Components/NewAttachmentDialog.vue";
import DeleteAttachmentDialog from "@/Components/DeleteAttachmentDialog.vue";
import CommentContainer from "@/Components/CommentContainer.vue";
import CompareTicketDescriptionsDialog from "@/Components/CompareTicketDescriptionsDialog.vue";

export default {
  mixins: [ScrollToTopOnCreate],
  components: {
    AssignDevDialog,
    DeleteTicketDialog,
    EditTicketDialog,
    NewAttachmentDialog,
    DeleteAttachmentDialog,
    CommentContainer,
    CompareTicketDescriptionsDialog,
  },
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    types: {
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
    changeLogs: {
      type: Object,
      required: true,
    },
    ticketModifiers: {
      type: Object,
      required: true,
    },
    can: {
      type: Object,
      required: true,
    },
  },
  layout: MainLayout,
  data() {
    const {
      attachmentPage,
      ticketChangeLogPage,
      ticketChangeLogFilters,
      ticketChangeLogSortOrder,
    } = route().params;

    return {
      loadingChangeLogs: false,

      attachmentPage: parseInt(attachmentPage) || 1,
      ticketChangeLogPage: parseInt(ticketChangeLogPage) || 1,
      ticketChangeLogFilters: {
        property: null,
        initiator: null,
        dateRange: null,
        ...(ticketChangeLogFilters ?? {}),
      },
      ticketChangeLogSortOrder:
        ticketChangeLogSortOrder || constants.DateSortOrder.LatestFirst,

      cancelToken: null,
    };
  },
  filters: {
    formatDate(date) {
      return moment(date).format("DD MMMM YYYY, h A");
    },
  },
  computed: {
    ticketData() {
      return this.ticket.data;
    },
    ticketAttachments() {
      return this.attachments.data;
    },
    ticketAttachmentsCount() {
      return this.attachments.meta.total;
    },
    attachmentNoOfPages() {
      return this.attachments.meta.last_page;
    },
    ticketChangeLogs() {
      return this.changeLogs.data;
    },
    ticketChangeLogsCount() {
      return this.changeLogs.meta.total;
    },
    changeLogNoOfPages() {
      return this.changeLogs.meta.last_page;
    },
    ticketStatusToColorMap() {
      return constants.TicketStatusColor;
    },
    ticketProperties() {
      return Object.values(constants.TicketProperty);
    },
    ticketUsers() {
      return this.ticketModifiers.data.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
    allDateRanges() {
      return Object.values(constants.DateRange);
    },
    allSortOrders() {
      return Object.entries(constants.DateSortOrder).map(([key, val]) => ({
        text: key,
        value: val,
      }));
    },
  },
  methods: {
    reloadPage(partialData = null, optherOptions = {}) {
      if (this.cancelToken) {
        this.cancelToken.cancel();
      }

      const reqOptions = {
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onCancelToken: (token) => {
          this.cancelToken = token;
        },
        ...optherOptions,
      };

      if (partialData) {
        reqOptions.only = partialData;
      }

      this.$inertia.get(
        route("tickets.show", { ticket: this.ticketData }),
        {
          attachmentPage: this.attachmentPage,
          ticketChangeLogPage: this.ticketChangeLogPage,
          ticketChangeLogFilters: _.pickBy(this.ticketChangeLogFilters),
          ticketChangeLogSortOrder: this.ticketChangeLogSortOrder,
        },
        reqOptions
      );
    },
  },
  watch: {
    ticketChangeLogSortOrder() {
      this.ticketChangeLogPage = 1;
      this.reloadPage(["changeLogs"], {
        onStart: () => {
          this.loadingChangeLogs = true;
        },
        onSuccess: () => {
          this.loadingChangeLogs = false;
        },
      });
    },
    ticketChangeLogFilters: {
      deep: true,
      handler() {
        this.ticketChangeLogPage = 1;
        this.reloadPage(["changeLogs"], {
          onStart: () => {
            this.loadingChangeLogs = true;
          },
          onSuccess: () => {
            this.loadingChangeLogs = false;
          },
        });
      },
    },
  },
};
</script>

<style lang="scss" scoped>
.attachment-con {
  overflow-y: auto;
  max-height: 500px;
}
</style>
