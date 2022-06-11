<template>
  <v-container class="my-5 px-5">
    <v-sheet elevation="4" rounded="xl">
      <div class="d-flex justify-space-between align-center pt-8 px-6">
        <h3 class="ma-0">PROJECT PRIORITIES</h3>
        <create-Project-priority-dialog />
      </div>
      <div class="mx-5">
        <v-text-field
          v-model="searchQuery"
          label="Search By Name And Color"
          clearable
          :error="searchQueryError"
          :error-messages="searchQueryError ? searchQueryErrorMessage : ''"
        ></v-text-field>
      </div>
      <v-simple-table class="rounded-xl">
        <thead>
          <tr>
            <th class="text-h6 text-center font-weight-bold py-3">Name</th>
            <th class="text-h6 text-center font-weight-bold py-3">Color</th>
            <th class="text-h6 text-center font-weight-bold py-3">
              # Of Projects
            </th>
            <th class="text-h6 text-center font-weight-bold py-3">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="priority in allPriorities" :key="priority.id">
            <td class="py-6">
              <div class="text-center">
                {{ priority.name }}
              </div>
            </td>
            <td class="py-6">
              <div class="text-center">
                <color-box :color="priority.color" />
                {{ priority.color }}
              </div>
            </td>
            <td class="py-6">
              <div class="text-center">
                {{ priority.projectsCount || 0 }}
              </div>
            </td>
            <td class="py-6">
              <div class="text-center">
                <edit-Project-priority-dialog :projectPriority="priority" />
                <delete-Project-priority-dialog :projectPriority="priority" />
              </div>
            </td>
          </tr>
        </tbody>
      </v-simple-table>
      <div
        v-if="prioritiesCount"
        class="d-flex justify-center align-center pl-4 pb-3"
      >
        <v-pagination
          class="mr-5"
          v-model="page"
          :length="paginationLength"
          @input="filterPriorites"
        ></v-pagination>
      </div>
      <div
        v-else
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
        No Project Priorites To Show
      </div>
    </v-sheet>
  </v-container>
</template>

<script>
import MainLayout from "@/Layouts/Main";
import ScrollToTopOnCreate from "@/Mixins/ScrollToTopOnCreate.js";
import CreateProjectPriorityDialog from "@/Components/CreateProjectPriorityDialog.vue";
import EditProjectPriorityDialog from "@/Components/EditProjectPriorityDialog.vue";
import DeleteProjectPriorityDialog from "@/Components/DeleteProjectPriorityDialog.vue";
import ColorBox from "@/Components/Reusable/ColorBox.vue";

export default {
  components: {
    CreateProjectPriorityDialog,
    EditProjectPriorityDialog,
    DeleteProjectPriorityDialog,
    ColorBox,
  },
  mixins: [ScrollToTopOnCreate],
  layout: MainLayout,
  props: {
    priorities: {
      type: Object,
      required: true,
    },
  },
  data() {
    const { page, searchQuery } = route().params;
    return {
      page: parseInt(page) ?? 1,
      searchQuery: searchQuery ?? "",
      searchQueryError: !!searchQuery && !this.validSearchQuery(searchQuery),
      cancelToken: null,
    };
  },
  computed: {
    allPriorities() {
      return this.priorities.data;
    },
    prioritiesCount() {
      return this.priorities.meta.total;
    },
    paginationLength() {
      return this.priorities.meta.last_page;
    },
    searchQueryErrorMessage() {
      return "Search query must be greater than 3 characters long.";
    },
  },
  methods: {
    filterPriorites() {
      if (this.cancelToken) {
        this.cancelToken.cancel();
      }

      const queryParams = _.pickBy({
        searchQuery: this.searchQuery,
        page: this.page,
      });

      this.$inertia.get(route("projectPriorities.index"), queryParams, {
        only: ["priorities"],
        preserveState: true,
        preserveScroll: true,
        replace: true,
        onCancelToken: (token) => {
          this.cancelToken = token;
        },
      });
    },
    validSearchQuery(val) {
      return !val || val.length == 0 || val.length > 3;
    },
  },
  watch: {
    searchQuery: _.debounce(function (newVal) {
      if (!this.validSearchQuery(newVal)) {
        this.searchQueryError = true;
        return;
      }
      this.searchQueryError = false;
      this.page = 1;
      this.filterPriorites();
    }, 500),
  },
};
</script>

<style lang="scss" scoped>
</style>
