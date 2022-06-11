<template>
  <div>
    <div class="d-flex align-top mb-3">
      <v-avatar>
        <img :src="commentUserAvatar" />
      </v-avatar>
      <div class="flex-grow-1">
        <div v-if="editing && canUpdate" class="comment-body">
          <comment-edit-form
            :comment="comment"
            @updateCancelled="cancelUpdate"
            @updated="updateComment"
            @deleted="$emit('deleted', { id: comment.id })"
          />
        </div>
        <div v-else class="comment-body">
          <div class="font-weight-bold">
            {{ commentUserName }}
          </div>
          <span v-if="repliedComment" class="primary--text">
            @{{ repliedComment.user.profile.name }}
          </span>
          {{ comment.body }}
        </div>
        <span v-if="currRelpiesState == repliesState.Loaded" class="mt-1 ml-2">
          <v-icon @click="refreshReplies" class="cur-point">mdi-restart</v-icon>
        </span>
        <div
          v-if="currRelpiesState == repliesState.Loading"
          class="ml-2 mt-1 d-inline-block"
        >
          <v-progress-circular
            indeterminate
            color="primary"
          ></v-progress-circular>
          <span class="ml-1"> &#9679;</span>
        </div>
        <div
          v-else-if="currRelpiesState == repliesState.ErrorLoading"
          class="mt-1 d-inline-block"
        >
          <span class="error--text">Error Loading Replies</span>
          <v-icon @click="loadReplies" class="cur-point">mdi-restart</v-icon>
          <span class="ml-2"> &#9679;</span>
        </div>
        <span
          v-else
          class="cur-point font-weight-bold primary--text d-inline-block mt-1"
          @click="loadOrShowReplies"
        >
          {{
            currRelpiesState == repliesState.NotLoaded
              ? "Load"
              : repliesShown
              ? "Hide"
              : "View"
          }}
          Replies
          <v-icon
            v-show="currRelpiesState == repliesState.Loaded"
            :class="{ rotated: repliesShown }"
            >mdi-menu-down</v-icon
          >
          <v-icon
            v-show="currRelpiesState == repliesState.NotLoaded"
            @click.stop="toggleDropdown"
            :class="{ rotated: repliesShown }"
            >mdi-menu-down</v-icon
          >
          <span class="ml-1 secondary--text"> &#9679;</span>
        </span>
        <span
          v-if="!editing && canUpdate"
          @click="editComment"
          class="
            cur-point
            font-weight-bold
            d-inline-block
            primary--text
            mt-1
            ml-1
          "
          >Edit
          <span class="ml-1 secondary--text"> &#9679;</span>
        </span>
        <span
          v-if="canDelete && currDeleteState == deleteState.NotDeleting"
          @click="deleteComment"
          class="
            cur-point
            font-weight-bold
            d-inline-block
            error--text
            mt-1
            ml-1
          "
        >
          Delete
        </span>
        <span v-else-if="currDeleteState == deleteState.Deleting" class="mt-1">
          <v-progress-circular
            indeterminate
            color="error"
          ></v-progress-circular>
        </span>
        <span
          v-else-if="currDeleteState == deleteState.FailedToDelete"
          class="mt-1 error--text"
        >
          Failed To Delete
        </span>
        <div class="mt-2 mb-1">
          <new-comment-form
            v-if="canAddReply"
            :parentComment="comment"
            @created="addReply"
            :ticket="ticket"
          />
        </div>
        <transition name="slide">
          <div v-show="repliesShown" class="replies-list">
            <comment
              v-for="cmt in replies"
              :canAddReply="canAddReply"
              :canUpdate="Boolean(cmt['can-update'])"
              :canDelete="Boolean(cmt['can-delete'])"
              :key="cmt.id"
              :comment="cmt"
              :ticket="ticket"
              :repliedComment="comment"
              @updated="updateReply"
              @deleted="deleteReply({ id: cmt.id })"
            />
            <div v-if="currRelpiesState == repliesState.Loaded">
              <div
                v-if="currMoreRepliesState == moreRepliesState.MoreNotLoaded"
              >
                <v-btn small @click="loadMoreReplies">Load More</v-btn>
              </div>
              <div
                v-else-if="currMoreRepliesState == moreRepliesState.MoreLoading"
              >
                <v-progress-circular
                  indeterminate
                  color="secondary"
                ></v-progress-circular>
              </div>
              <div
                v-else-if="
                  currMoreRepliesState == moreRepliesState.ErrorLoadingMore
                "
              >
                <span class="error--text">Error Loading More Replies</span>
                <v-icon @click="loadMoreReplies" class="cur-point">
                  mdi-restart
                </v-icon>
              </div>
              <div
                v-else-if="currMoreRepliesState == moreRepliesState.AllLoaded"
              >
                <small>No More To Show</small>
              </div>
            </div>
          </div>
        </transition>
      </div>
    </div>
  </div>
</template>

<script>
import Vue from "vue";
import CommentEditForm from "@/Components/CommentEditForm.vue";
import NewCommentForm from "@/Components/NewCommentForm.vue";

export default {
  components: { CommentEditForm, NewCommentForm },
  name: "comment",
  props: {
    comment: {
      type: Object,
      required: true,
    },
    ticket: {
      type: Object,
      required: true,
    },
    repliedComment: {
      type: Object,
      default: null,
    },
    canAddReply: {
      type: Boolean,
      default: true,
    },
    canUpdate: {
      type: Boolean,
      default: true,
    },
    canDelete: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      currRelpiesState: null,
      currMoreRepliesState: null,
      currDeleteState: null,
      repliesShown: true,
      replies: [],
      nextCursor: null,
      exclude: [],
      editing: false,
    };
  },
  computed: {
    repliesState() {
      return {
        NotLoaded: 0,
        Loading: 1,
        Loaded: 2,
        ErrorLoading: 3,
      };
    },
    moreRepliesState() {
      return {
        MoreNotLoaded: 0,
        MoreLoading: 1,
        ErrorLoadingMore: 2,
        AllLoaded: 3,
      };
    },
    deleteState() {
      return {
        NotDeleting: 0,
        Deleting: 1,
        Deleted: 2,
        FailedToDelete: 3,
      };
    },
    commentUserName() {
      return this.comment.user.profile.name;
    },
    commentUserAvatar() {
      const possibleAvatars = this.comment.user.profile.avatar;
      return (
        possibleAvatars.thumbnail ||
        possibleAvatars.original ||
        "./assets/anonymousUser.jpg"
      );
    },
  },
  created() {
    this.currRelpiesState = this.repliesState.NotLoaded;
    this.currMoreRepliesState = this.moreRepliesState.MoreNotLoaded;
    this.currDeleteState = this.deleteState.NotDeleting;
  },
  methods: {
    async loadOrShowReplies() {
      switch (this.currRelpiesState) {
        case this.repliesState.NotLoaded:
          await this.loadReplies();
          break;

        case this.repliesState.Loaded:
          this.repliesShown = !this.repliesShown;
          break;
      }
    },
    async loadReplies() {
      try {
        this.currRelpiesState = this.repliesState.Loading;

        const res = await axios.get(
          route("comments.replies.index", {
            comment: this.comment,
            exclude: this.exclude,
          })
        );
        this.replies.push(...res.data.data);
        _.orderBy(this.replies, "id", "desc");
        this.nextCursor = res.data.meta.next_cursor;

        this.currRelpiesState = this.repliesState.Loaded;
        this.showDropdown();
      } catch (error) {
        this.currRelpiesState = this.repliesState.ErrorLoading;
      }
    },
    async refreshReplies() {
      this.replies = [];
      this.exclude = [];
      this.nextCursor = null;
      this.currMoreRepliesState = this.moreRepliesState.MoreNotLoaded;
      await this.loadReplies();
    },
    toggleDropdown() {
      this.repliesShown = !this.repliesShown;
    },
    showDropdown() {
      this.repliesShown = true;
    },
    async loadMoreReplies() {
      if (!this.nextCursor) {
        this.currMoreRepliesState = this.moreRepliesState.AllLoaded;
        return;
      }
      try {
        this.currMoreRepliesState = this.moreRepliesState.MoreLoading;

        const res = await axios.get(
          route("comments.replies.index", {
            comment: this.comment,
            exclude: this.exclude,
            cursor: this.nextCursor,
          })
        );
        this.replies.push(...res.data.data);
        _.orderBy(this.replies, "id", "desc");
        this.nextCursor = res.data.meta.next_cursor;

        this.currMoreRepliesState = this.moreRepliesState.MoreNotLoaded;
      } catch (error) {
        this.currMoreRepliesState = this.moreRepliesState.ErrorLoadingMore;
      }
    },
    editComment() {
      this.editing = true;
    },
    cancelUpdate() {
      this.editing = false;
    },
    updateComment({ id, data }) {
      this.editing = false;
      this.$emit("updated", { id, data });
    },
    updateReply({ id, data }) {
      const index = this.replies.findIndex((c) => c.id == id);
      if (index >= 0) {
        Vue.set(this.replies, index, { ...this.replies[index], ...data });
      }
    },
    addReply({ comment }) {
      this.replies.unshift(comment);
      this.exclude.push(comment.id);
    },
    deleteReply({ id }) {
      this.replies = this.replies.filter((c) => c.id != id);
      this.exclude = this.exclude.filter((i) => i != id);
    },
    async deleteComment() {
      try {
        this.currDeleteState = this.deleteState.Deleting;

        await axios.delete(
          route("comments.destroy", { comment: this.comment })
        );

        this.currDeleteState = this.deleteState.Deleted;

        this.$emit("deleted", { id: this.comment.id });
      } catch (error) {
        if (error.response && error.response.status == 404) {
          this.$toasted.error("Comment Already Deleted", {
            duration: 3000,
            posiiton: "top-center",
            icon: "close",
          });
          this.$emit("deleted", { id: this.comment.id });
        } else {
          this.currDeleteState = this.deleteState.FailedToDelete;
        }

        setTimeout(() => {
          this.currDeleteState = this.deleteState.NotDeleting;
        }, 2000);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.rotated {
  transform: rotate(180deg);
  transition: transform 0.3s ease-in;
}

.slide-enter,
.slide-leave-to {
  transform: scaleY(0);
  opacity: 0.5;
}

.replies-list {
  transform-origin: top;
  transition: transform 0.4s ease-in-out;
  overflow: hidden;
}

.comment-body {
  background: #eee;
  border-radius: 20px;
  padding: 12px;
  margin-left: 5px;
}

.comment-form {
  border-radius: 20px;
  padding: 12px;
  margin-left: 5px;
}
</style>
