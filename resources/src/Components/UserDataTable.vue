<template>
  <v-sheet elevation="4" rounded="xl">
    <h3 class="px-6 pt-6 ma-0">ALL USERS</h3>
    <div class="mx-5">
      <v-text-field
        v-model="searchQuery"
        label="Search By Name And Email"
        clearable
        :error="searchQueryError"
        :error-messages="searchQueryError ? searchQueryErrorMessage : ''"
      ></v-text-field>
    </div>
    <v-row class="mx-5 mt-1">
      <v-col cols="12" sm="3">
        <v-select
          v-model="filters.roles"
          :items="allUserRoles"
          label="Role"
          outlined
          clearable
          multiple
          :menu-props="{ maxHeight: '200' }"
        >
          <template v-slot:selection="{ item, index }">
            <v-chip v-if="index === 0">
              <span>{{ item.text }}</span>
            </v-chip>
            <span v-if="index === 1" class="grey--text text-caption">
              (+{{ filters.roles.length - 1 }} others)
            </span>
          </template>
        </v-select>
      </v-col>
      <v-col cols="12" sm="3">
        <v-select
          v-model="filters.createdRange"
          :items="allDateRanges"
          label="Joined"
          outlined
          :menu-props="{ maxHeight: '200' }"
        >
        </v-select>
      </v-col>
      <v-col cols="12" sm="6">
        <v-select
          v-model="filters.projects"
          :items="allProjects"
          label="Projects"
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
              (+{{ filters.projects.length - 1 }})
            </span>
          </template>
        </v-select>
      </v-col>
    </v-row>
    <div style="min-height: 50vh">
      <div v-if="loadingUsers" class="mt-3 text-center">
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>
      <v-simple-table v-else class="rounded-lg">
        <thead>
          <tr>
            <th></th>
            <sortable-table-header
              title="Name"
              :initialSortOrder="
                (sortBy && sortBy.field) == 'profile.name'
                  ? sortBy.order
                  : sortOrders.None
              "
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('profile.name') }"
            />

            <sortable-table-header
              title="Email"
              :initialSortOrder="
                (sortBy && sortBy.field) == 'email'
                  ? sortBy.order
                  : sortOrders.None
              "
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('email') }"
            />

            <th>
              <h2>Role</h2>
            </th>

            <sortable-table-header
              :initialSortOrder="
                (sortBy && sortBy.field) == 'createdAt'
                  ? sortBy.order
                  : sortOrders.None
              "
              title="Joined Date"
              class="text-left text-h6 font-weight-bold py-3"
              v-on="{ sortOrderChanged: modifySortField('createdAt') }"
            />
            <th class="text-left text-h6 font-weight-bold py-3">
              <div class="fill-height d-flex align-top">Action</div>
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.data" :key="user.id">
            <td class="py-6">
              <v-avatar size="36px" class="ml-3">
                <img
                  :src="
                    user.profile.avatar.thumbnail ||
                    user.profile.avatar.original ||
                    './assets/anonymousUser.jpg'
                  "
                />
              </v-avatar>
            </td>
            <td class="py-6">
              <v-tooltip left>
                <template v-slot:activator="{ on, attrs }">
                  <span v-bind="attrs" v-on="on">
                    {{ user.profile.name | truncate }}
                  </span>
                </template>
                <span>{{ user.profile.name }}</span>
              </v-tooltip>
            </td>
            <td class="py-6">
              {{ user.email }}
            </td>
            <td class="py-6">
              <v-chip
                class="white--text"
                :color="userRoleToColorMap[user.role]"
              >
                {{ user.role }}
              </v-chip>
            </td>
            <td class="py-6 font-weight-bold">
              <div class="text-left">
                {{ user.createdAt | formatDate }}
              </div>
            </td>
            <td class="py-6">
              <Link
                :href="route('users.profiles.show', { user })"
                method="GET"
                as="span"
              >
                <v-btn icon><v-icon color="info">mdi-eye</v-icon></v-btn>
              </Link>
              <assign-user-role-dialog :user="user" />
              <delete-user-dialog :user="user" />
            </td>
          </tr>
        </tbody>
      </v-simple-table>
      <div
        v-if="usersCount && !loadingUsers"
        class="d-flex justify-center align-center pl-4 pb-3"
      >
        <div class="mr-2">
          {{ itemStart }} - {{ itemEnd }} of
          {{ usersCount }}
        </div>
        <v-pagination
          class="mr-5"
          v-model="pageNo"
          :length="paginationLength"
          @input="filterUsers"
        ></v-pagination>
      </div>
      <div
        v-else-if="!loadingUsers"
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
        No users To Show
      </div>
    </div>
  </v-sheet>
</template>

<script>
import constants from "@/constants";
import moment from "moment";
import SortableTableHeader from "@/Components/Reusable/SortableTableHeader.vue";
import DeleteUserDialog from "@/Components/DeleteUserDialog.vue";
import AssignUserRoleDialog from "@/Components/AssignUserRoleDialog.vue";

export default {
  components: {
    SortableTableHeader,
    DeleteUserDialog,
    AssignUserRoleDialog,
  },
  props: {
    users: {
      type: Object,
      required: true,
    },
    fetchUrl: {
      type: String,
      required: true,
    },
    initialUserFiltersValues: {
      type: Object,
      required: true,
    },
    auxillaryData: {
      type: Object,
      required: true,
    },
  },
  data() {
    const { searchQuery, sortBy, filters, page } =
      this.initialUserFiltersValues;

    return {
      loadingUsers: false,

      pageNo: page || 1,

      searchQuery: searchQuery || "",
      searchQueryError: !!searchQuery && !this.validSearchQuery(searchQuery),

      sortBy: sortBy,

      filters: {
        roles: [],
        createdRange: constants.DateRange.All,
        projects: [],
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
    allUserRoles() {
      return this.auxillaryData.roles.data.map((r) => ({
        value: r.id.toString(),
        text: r.name,
      }));
    },
    allProjects() {
      return this.auxillaryData.projects.data.map((p) => ({
        value: p.id.toString(),
        text: p.title,
      }));
    },
    allDateRanges() {
      return Object.values(constants.DateRange);
    },
    userRoleToColorMap() {
      return constants.RoleColor;
    },
    itemStart() {
      return this.users.meta.from;
    },
    itemEnd() {
      return this.users.meta.to;
    },
    usersCount() {
      return this.users.meta.total;
    },
    paginationLength() {
      return this.users.meta.last_page;
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
    filterUsers() {
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
        only: ["users"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onStart: () => {
          this.loadingUsers = true;
        },
        onSuccess: () => {
          this.loadingUsers = false;
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
      this.filterUsers();
    }, 500),
    sortBy() {
      this.pageNo = 1;
      this.filterUsers();
    },
    filters: {
      deep: true,
      handler() {
        this.pageNo = 1;
        this.filterUsers();
      },
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
