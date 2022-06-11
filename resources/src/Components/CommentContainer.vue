<template>
  <v-sheet
    elevation="3"
    rounded="xl"
    style="overflow-y: auto; min-height: 100vh"
  >
    <h3 class="text-center py-2 white--text secondary">Comments</h3>
    <div class="pa-6">
      <new-comment-form
        v-if="canAddComment"
        @created="addComment"
        :ticket="ticket"
      />
      <div
        v-if="
          currCommentsState == commentsState.Loaded ||
          currCommentsState == commentsState.AllLoaded
        "
        @click="refreshComments"
        class="text-center mb-3 cur-point"
      >
        <v-icon class="cur-point">mdi-restart</v-icon>
        Click to refresh
      </div>
      <div>
        <comment
          :canAddReply="canAddComment"
          :canUpdate="Boolean(comment['can-update'])"
          :canDelete="Boolean(comment['can-delete'])"
          :comment="comment"
          :ticket="ticket"
          v-for="comment in comments"
          :key="comment.id"
          @updated="updateComment"
          @deleted="deleteComment({ id: comment.id })"
        />
        <div
          v-if="currCommentsState == commentsState.Loaded"
          class="mt-2 text-center"
        >
          <v-btn @click="loadComments">Load More</v-btn>
        </div>
        <div
          v-else-if="currCommentsState == commentsState.AllLoaded"
          class="text-center"
        >
          <span class="font-weight-bold">No More To Show</span>
        </div>
      </div>
      <div
        v-if="currCommentsState == commentsState.Loading"
        class="text-center"
      >
        <v-progress-circular
          indeterminate
          color="primary"
        ></v-progress-circular>
      </div>
      <div
        v-else-if="currCommentsState == commentsState.ErrorLoading"
        class="text-center"
      >
        <span class="error--text font-weight-bold">Error Loading Comments</span>
        <v-icon @click="loadComments" class="cur-point"> mdi-restart </v-icon>
      </div>
    </div>
  </v-sheet>
</template>

<script>
import Comment from "@/Components/Comment.vue";
import NewCommentForm from "@/Components/NewCommentForm.vue";
import Vue from "vue";

export default {
  props: {
    ticket: {
      type: Object,
      required: true,
    },
    canAddComment: {
      type: Boolean,
      default: true,
    },
  },
  components: { Comment, NewCommentForm },
  data() {
    return {
      currCommentsState: null,
      commentsCursor: null,
      comments: [],
    };
  },
  computed: {
    commentsState() {
      return {
        Loading: 0,
        Loaded: 1,
        ErrorLoading: 2,
        AllLoaded: 3,
      };
    },
  },
  async created() {
    await this.loadComments();
  },
  methods: {
    async loadComments() {
      try {
        this.currCommentsState = this.commentsState.Loading;

        const res = await axios.get(
          route("tickets.topLevelComments.index", {
            ticket: this.ticket,
            cursor: this.commentsCursor,
          })
        );
        this.comments.push(...res.data.data);
        this.commentsCursor = res.data.meta.next_cursor;

        if (!this.commentsCursor) {
          this.currCommentsState = this.commentsState.AllLoaded;
        } else {
          this.currCommentsState = this.commentsState.Loaded;
        }
      } catch (error) {
        this.currCommentsState = this.commentsState.ErrorLoading;
      }
    },
    async refreshComments() {
      this.comments = [];
      this.commentsCursor = null;
      await this.loadComments();
    },
    updateComment({ id, data }) {
      const index = this.comments.findIndex((c) => c.id == id);
      if (index >= 0) {
        Vue.set(this.comments, index, { ...this.comments[index], ...data });
      }
    },
    deleteComment({ id }) {
      this.comments = this.comments.filter((c) => c.id != id);
    },
    addComment({ comment }) {
      this.comments.unshift(comment);
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
