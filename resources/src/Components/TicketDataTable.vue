<template>
  <v-sheet elevation="4" rounded="xl">
    <div class="d-flex justify-space-between align-center pt-8 px-6">
      <h3 class="ma-0">{{ title || "ALL TICKETS" }}</h3>
      <new-ticket-dialog
        :canAssignTicket="canAssignTicket"
        :projects="auxillaryData.projects"
        :types="auxillaryData.types"
        :priorities="auxillaryData.priorities"
        :initialValues="auxillaryData.initialValuesForNewTicketForm || {}"
      />
    </div>
    <div class="mx-5 d-flex align-center">
      <div class="mr-4">
        <filter-tickets-dialog
          :ticket-filters="filters"
          @filtersApplied="applyFilters"
          :dataForFilters="auxillaryData"
          :showFilters="showFilters"
          :key="filtersDialogKey"
        />
      </div>
      <v-text-field
        v-model="searchQuery"
        label="Search By Title And Description"
        clearable
        :error="searchQueryError"
        :error-messages="searchQueryError ? searchQueryErrorMessage : ''"
      ></v-text-field>
    </div>
    <v-simple-table class="rounded-lg">
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
              (sortBy && sortBy.field) == 'creator.profile.name'
                ? sortBy.order
                : sortOrders.None
            "
            title="Submitter"
            class="text-left text-h6 font-weight-bold py-3"
            v-on="{ sortOrderChanged: modifySortField('creator.profile.name') }"
          />
          <sortable-table-header
            :initialSortOrder="
              (sortBy && sortBy.field) == 'assignee.profile.name'
                ? sortBy.order
                : sortOrders.None
            "
            title="Developer"
            class="text-left text-h6 font-weight-bold py-3"
            v-on="{
              sortOrderChanged: modifySortField('assignee.profile.name'),
            }"
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
              (sortBy && sortBy.field) == 'priority.name'
                ? sortBy.order
                : sortOrders.None
            "
            title="Priority"
            class="text-left text-h6 font-weight-bold py-3"
            v-on="{ sortOrderChanged: modifySortField('priority.name') }"
          />
          <sortable-table-header
            title="Updated"
            class="text-left text-h6 font-weight-bold py-3"
            v-on="{ sortOrderChanged: modifySortField('updatedAt') }"
          />
          <th class="text-left text-h6 font-weight-bold py-3">
            <div class="fill-height d-flex align-top">Action</div>
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="ticket in tickets" :key="ticket.id">
          <td class="py-6">
            <v-tooltip left>
              <template v-slot:activator="{ on, attrs }">
                <span v-bind="attrs" v-on="on">
                  {{ ticket.title | truncate }}
                </span>
              </template>
              <span>{{ ticket.title }}</span>
            </v-tooltip>
          </td>
          <td class="py-6">
            <span v-if="ticket.creator">
              {{ ticket.creator.profile.name }}
            </span>
            <span v-else class="text--secondary"> Not Available </span>
          </td>
          <td class="py-6">
            <div v-if="ticket.assignee">
              {{ ticket.assignee.profile.name }}
            </div>
            <assign-dev-dialog
              v-else-if="Boolean(ticket['can-assign'])"
              :ticket="ticket"
            />
            <div v-else class="text--secondary">Not Assigned</div>
          </td>
          <td class="py-6">
            <v-chip
              class="white--text"
              :color="ticketStatusToColorMap[ticket.status]"
            >
              {{ ticket.status }}
            </v-chip>
          </td>
          <td class="py-6">
            <v-chip
              v-if="ticket.priority"
              class="white--text"
              :color="ticket.priority.color"
            >
              {{ ticket.priority.name }}
            </v-chip>
            <span v-else class="text--secondary"> Not Available </span>
          </td>
          <td class="py-6 font-weight-bold text-center">
            {{ ticket.updatedAt | formatDate }}
          </td>
          <td class="py-6">
            <Link
              v-if="Boolean(ticket['can-view'])"
              :href="route('tickets.show', { ticket: ticket })"
              method="GET"
              as="span"
            >
              <v-btn icon><v-icon color="info">mdi-eye</v-icon></v-btn>
            </Link>
            <edit-ticket-dialog
              v-if="Boolean(ticket['can-edit'])"
              :canAssignTicket="Boolean(ticket['can-assign'])"
              :ticket="ticket"
              :types="auxillaryData.types"
              :priorities="auxillaryData.priorities"
            />
            <delete-ticket-dialog
              v-if="Boolean(ticket['can-delete'])"
              :ticket="ticket"
            />
          </td>
        </tr>
      </tbody>
    </v-simple-table>
    <div
      v-if="ticketsCount"
      class="d-flex justify-center align-center pl-4 pb-3"
    >
      <div class="mr-2">
        {{ itemStart }} - {{ itemEnd }} of
        {{ ticketsCount }}
      </div>
      <v-pagination
        class="mr-5"
        v-model="pageNo"
        :length="paginationLength"
        @input="filterTickets"
      ></v-pagination>
    </div>
    <div
      v-else
      class="d-flex justify-center align-center pl-4 pb-3 mt-4 font-weight-bold"
    >
      No Tickets To Show
    </div>
  </v-sheet>
</template>

<script>
import constants from "@/constants";
import moment from "moment";
import SortableTableHeader from "@/Components/Reusable/SortableTableHeader.vue";
import FilterTicketsDialog from "@/Components/FilterTicketsDialog.vue";
import NewTicketDialog from "@/Components/NewTicketDialog.vue";
import AssignDevDialog from "@/Components/AssignDevDialog.vue";
import EditTicketDialog from "@/Components/EditTicketDialog.vue";
import DeleteTicketDialog from "@/Components/DeleteTicketDialog.vue";

export default {
  props: {
    ticketData: {
      type: Object,
      required: true,
    },
    auxillaryData: {
      type: Object,
      required: true,
    },
    initialTicketFilterValues: {
      type: Object,
      required: true,
    },
    showFilters: {
      type: Array,
      default: null,
    },
    title: {
      type: String,
      default: null,
    },
    fetchUrl: {
      type: String,
      required: true,
    },
    canAssignTicket: {
      type: Boolean,
      default: true,
    },
  },
  components: {
    SortableTableHeader,
    FilterTicketsDialog,
    NewTicketDialog,
    AssignDevDialog,
    EditTicketDialog,
    DeleteTicketDialog,
  },
  data() {
    const { searchQuery, sortBy, filters, pageNo } =
      this.initialTicketFilterValues;

    return {
      pageNo: pageNo || 1,
      searchQuery: searchQuery || "",
      searchQueryError: !!searchQuery && !this.validSearchQuery(searchQuery),
      sortBy: sortBy || null,
      filters: {
        devs: [],
        submitters: [],
        priorities: [],
        types: [],
        statuses: [],
        updatedRange: constants.DateRange.All,
        projects: [],
        ...filters,
      },
      filtersDialogKey: this.$uuid.v4(),
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
    ticketStatusToColorMap() {
      return constants.TicketStatusColor;
    },
    tickets() {
      return this.ticketData.data;
    },
    itemStart() {
      return this.ticketData.meta.from;
    },
    itemEnd() {
      return this.ticketData.meta.to;
    },
    ticketsCount() {
      return this.ticketData.meta.total;
    },
    paginationLength() {
      return this.ticketData.meta.last_page;
    },
  },
  methods: {
    filterTickets() {
      if (this.cancelToken) {
        this.cancelToken.cancel();
      }

      const queryParams = _.pickBy({
        searchQuery: this.searchQuery,
        page: this.pageNo,
        sortBy: this.sortBy,
        filters: _.pickBy(this.filters),
      });

      this.$inertia.get(this.fetchUrl, queryParams, {
        only: ["tickets"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onSuccess: () => {
          this.filtersDialogKey = this.$uuid.v4();
          this.scrollToElementTop();
        },
        onCancelToken: (token) => {
          this.cancelToken = token;
        },
      });
    },
    validSearchQuery(val) {
      return !val || val.length == 0 || val.length >= 4;
    },
    handleFiltersCancelled() {
      this.filtersDialogShown = false;
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
    applyFilters({ filters }) {
      this.filters = filters;
    },
    scrollToElementTop() {
      window.scrollTo(0, this.$el.offsetTop);
    },
  },
  filters: {
    truncate(str) {
      return _.truncate(str, { length: 20 });
    },
    formatDate(date) {
      return moment(date).format("MMM DD, YYYY");
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
      this.filterTickets();
    }, 500),
    sortBy() {
      this.pageNo = 1;
      this.filterTickets();
    },
    filters: {
      deep: true,
      handler() {
        this.pageNo = 1;
        this.filterTickets();
      },
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
