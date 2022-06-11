<template>
  <v-sheet elevation="4" rounded="xl">
    <div class="d-flex justify-space-between align-center pt-8 px-6">
      <h3 class="ma-0">{{ title || "ALL PROJECTS" }}</h3>
      <create-project-dialog
        v-if="canCreateProjects"
        :priorities="allProjectPriorities"
        :supervisors="allSupervisors"
      />
    </div>
    <div class="mx-5">
      <v-text-field
        v-model="searchQuery"
        label="Search By Title And Description"
        clearable
        :error="searchQueryError"
        :error-messages="searchQueryError ? searchQueryErrorMessage : ''"
      ></v-text-field>
    </div>
    <v-row class="mx-5 mt-1">
      <v-col cols="12" sm="3">
        <v-select
          v-model="filters.status"
          :items="allProjectStatuses"
          label="Status"
          outlined
          clearable
          :menu-props="{ maxHeight: '200' }"
        >
        </v-select>
      </v-col>
      <v-col cols="12" sm="3">
        <v-select
          v-model="filters.priorities"
          :items="allProjectPriorities"
          label="Priority"
          outlined
          multiple
          clearable
          :menu-props="{ maxHeight: '200' }"
        >
          <template v-slot:selection="{ item, index }">
            <v-chip v-if="index === 0">
              <span>{{ item.text | truncate(15) }}</span>
            </v-chip>
            <span v-if="index === 1" class="grey--text text-caption">
              (+{{ filters.priorities.length - 1 }})
            </span>
          </template>
        </v-select>
      </v-col>
      <v-col cols="12" sm="3">
        <v-select
          v-model="filters.createdRange"
          :items="allDateRanges"
          label="Date Range"
          outlined
          :menu-props="{ maxHeight: '200' }"
        >
        </v-select>
      </v-col>
      <v-col cols="12" sm="3">
        <v-select
          v-model="filters.supervisors"
          :items="allSupervisors"
          label="Supervisors"
          multiple
          outlined
          clearable
          :menu-props="{ maxHeight: '200' }"
        >
          <template v-slot:selection="{ item, index }">
            <v-chip v-if="index === 0">
              <span>{{ item.text | truncate(15) }}</span>
            </v-chip>
            <span v-if="index === 1" class="grey--text text-caption">
              (+{{ filters.supervisors.length - 1 }})
            </span>
          </template>
        </v-select>
      </v-col>
    </v-row>
    <div style="min-height: 50vh">
      <div v-if="loadingProjects" class="mt-3 text-center">
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>
      <v-simple-table v-else class="rounded-lg">
        <thead>
          <tr>
            <sortable-table-header
              title="Title"
              :initialSortOrder="
                (sortBy && sortBy.field) == 'title'
                  ? sortBy.order
                  : sortOrders.None
              "
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('title') }"
            />

            <sortable-table-header
              :initialSortOrder="
                (sortBy && sortBy.field) == 'status'
                  ? sortBy.order
                  : sortOrders.None
              "
              title="Status"
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('status') }"
            />

            <sortable-table-header
              :initialSortOrder="
                (sortBy && sortBy.field) == 'deadline'
                  ? sortBy.order
                  : sortOrders.None
              "
              title="Deadline"
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('deadline') }"
            />

            <sortable-table-header
              :initialSortOrder="
                (sortBy && sortBy.field) == 'priority.name'
                  ? sortBy.order
                  : sortOrders.None
              "
              title="Priority"
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('priority.name') }"
            />

            <sortable-table-header
              :initialSortOrder="
                (sortBy && sortBy.field) == 'createdAt'
                  ? sortBy.order
                  : sortOrders.None
              "
              title="Created"
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('createdAt') }"
            />
            <sortable-table-header
              :initialSortOrder="
                (sortBy && sortBy.field) == 'supervisor.profile.name'
                  ? sortBy.order
                  : sortOrders.None
              "
              title="Supervisor"
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{
                sortOrderChanged: modifySortField('supervisor.profile.name'),
              }"
            />
            <th class="text-left text-h6 font-weight-bold py-3">
              <div class="fill-height d-flex align-top">Action</div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="project in projects.data" :key="project.id">
            <td class="py-6">
              <v-tooltip left>
                <template v-slot:activator="{ on, attrs }">
                  <span v-bind="attrs" v-on="on">
                    {{ project.title | truncate }}
                  </span>
                </template>
                <span>{{ project.title }}</span>
              </v-tooltip>
            </td>
            <td class="py-6">
              <v-chip
                class="white--text"
                :color="projectStatusToColorMap[project.status]"
              >
                {{ project.status }}
              </v-chip>
            </td>
            <td class="py-6 font-weight-bold text-center">
              <span v-if="project.deadline">
                {{ project.deadline | formatDate }}
              </span>
              <span v-else class="text--secondary"> Not Available </span>
            </td>
            <td class="py-6">
              <v-chip
                v-if="project.priority"
                class="white--text"
                :color="project.priority.color"
              >
                {{ project.priority.name }}
              </v-chip>
              <span v-else class="text--secondary"> Not Available </span>
            </td>
            <td class="py-6 font-weight-bold text-center">
              {{ project.createdAt | formatDate }}
            </td>
            <td class="py-6">
              <div v-if="project.supervisor">
                {{ project.supervisor.profile.name }}
              </div>
              <assign-supervisor-dialog
                v-else-if="Boolean(project['can-assign'])"
                :project="project"
                :supervisors="auxillaryData.supervisors"
              />
              <div v-else class="text--secondary">No Supervisor</div>
            </td>
            <td class="py-6">
              <Link
                v-if="Boolean(project['can-view'])"
                :href="route('projects.show', { project })"
                method="GET"
                as="span"
              >
                <v-btn icon><v-icon color="info">mdi-eye</v-icon></v-btn>
              </Link>
              <edit-project-dialog
                v-if="Boolean(project['can-edit'])"
                :canAssignProject="Boolean(project['can-assign'])"
                :project="project"
                :statuses="allProjectStatuses"
                :priorities="allProjectPriorities"
                :supervisors="allSupervisors"
              />
              <delete-project-dialog
                v-if="Boolean(project['can-delete'])"
                :project="project"
              />
            </td>
          </tr>
        </tbody>
      </v-simple-table>
      <div
        v-if="projectsCount && !loadingProjects"
        class="d-flex justify-center align-center pl-4 pb-3"
      >
        <div class="mr-2">
          {{ itemStart }} - {{ itemEnd }} of
          {{ projectsCount }}
        </div>
        <v-pagination
          class="mr-5"
          v-model="pageNo"
          :length="paginationLength"
          @input="filterProjects"
        ></v-pagination>
      </div>
      <div
        v-else-if="!loadingProjects"
        class="
          d-flex
          justify-center
          align-center
          pl-4
          pb-3
          mt-4
          font-weight-bold
        "
      >
        No Projects To Show
      </div>
    </div>
  </v-sheet>
</template>

<script>
import constants from "@/constants";
import moment from "moment";
import SortableTableHeader from "@/Components/Reusable/SortableTableHeader.vue";
import AssignSupervisorDialog from "@/Components/AssignSupervisorDialog.vue";
import DeleteProjectDialog from "@/Components/DeleteProjectDialog.vue";
import CreateProjectDialog from "@/Components/CreateProjectDialog.vue";
import EditProjectDialog from "@/Components/EditProjectDialog.vue";

export default {
  components: {
    SortableTableHeader,
    AssignSupervisorDialog,
    DeleteProjectDialog,
    CreateProjectDialog,
    EditProjectDialog,
  },
  props: {
    projects: {
      type: Object,
      required: true,
    },
    fetchUrl: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      default: null,
    },
    initialProjectFiltersValues: {
      type: Object,
      required: true,
    },
    auxillaryData: {
      type: Object,
      required: true,
    },
    canCreateProjects: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    const { searchQuery, sortBy, filters, page } =
      this.initialProjectFiltersValues;

    return {
      loadingProjects: false,

      pageNo: page || 1,

      searchQuery: searchQuery || "",
      searchQueryError: !!searchQuery && !this.validSearchQuery(searchQuery),

      sortBy: sortBy,

      filters: {
        status: null,
        priorities: [],
        createdRange: constants.DateRange.All,
        supervisors: [],
        ...filters,
      },

      cancelToken: null,
    };
  },
  computed: {
    sortOrders() {
      return constants.SortOrder;
    },
    searchQueryErrorMessage() {
      return "Search query must be greater than 3 characters long.";
    },
    allProjectStatuses() {
      return Object.values(constants.ProjectStatus);
    },
    allProjectPriorities() {
      return this.auxillaryData.priorities?.data.map((pp) => ({
        value: pp.id.toString(),
        text: pp.name,
      }));
    },
    allDateRanges() {
      return Object.values(constants.DateRange);
    },
    allSupervisors() {
      return this.auxillaryData.supervisors?.data.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
    projectStatusToColorMap() {
      return constants.ProjectStatusColor;
    },
    itemStart() {
      return this.projects.meta.from;
    },
    itemEnd() {
      return this.projects.meta.to;
    },
    projectsCount() {
      return this.projects.meta.total;
    },
    paginationLength() {
      return this.projects.meta.last_page;
    },
  },
  filters: {
    truncate(str, chars = 20) {
      return _.truncate(str, { length: chars });
    },
    formatDate(date) {
      return moment(date).format("MMM DD, YYYY");
    },
  },
  methods: {
    filterProjects() {
      if (this.cancelToken) {
        this.cancelToken.cancel();
      }

      const queryParams = _.pickBy({
        page: this.pageNo,
        searchQuery: this.searchQuery,
        sortBy: this.sortBy,
        filters: _.pickBy(this.filters),
      });

      this.$inertia.get(this.fetchUrl, queryParams, {
        only: ["projects"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onStart: () => {
          this.loadingProjects = true;
        },
        onSuccess: () => {
          this.scrollToElementTop();
          this.loadingProjects = false;
        },
        onCancelToken: (token) => {
          this.cancelToken = token;
        },
      });
    },
    validSearchQuery(val) {
      return !val || val.length == 0 || val.length >= 4;
    },
    modifySortField(field) {
      return ({ sortOrder }) => {
        if (sortOrder == this.sortOrders.None) {
          this.sortBy = null;
        } else {
          this.sortBy = { field: field, order: sortOrder };
        }
      };
    },
    scrollToElementTop() {
      window.scrollTo(0, this.$el.offsetTop);
    },
  },
  watch: {
    searchQuery: _.debounce(function (newVal) {
      if (!this.validSearchQuery(newVal)) {
        this.searchQueryError = true;
        return;
      }
      this.searchQueryError = false;
      this.pageNo = 1;
      this.filterProjects();
    }, 500),
    sortBy() {
      this.pageNo = 1;
      this.filterProjects();
    },
    filters: {
      deep: true,
      handler() {
        this.pageNo = 1;
        this.filterProjects();
      },
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
