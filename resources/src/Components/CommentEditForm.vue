<template>
  <div>
    <v-textarea
      ref="textarea"
      v-model="form.body"
      class="d-block white ma-0 pa-0 comment-text-area"
      outlined
      auto-grow
      :error="
        Boolean(errorMsgs) || currUpdateState == updateState.FailedToUpdate
      "
      :error-messages="errorMsgs"
    ></v-textarea>
    <v-btn
      color="primary"
      @click="updateComment"
      :loading="currUpdateState == updateState.Updating"
      :disabled="currUpdateState == updateState.Updating"
      class="mt-2"
    >
      Update
    </v-btn>
    <v-btn class="mt-2 ml-2" @click="cancelUpdate">Cancel</v-btn>
    <span
      v-if="currUpdateState == updateState.FailedToUpdate"
      class="d-inline-block ml-3 mt-4 error--text"
    >
      Failed To Update
    </span>
  </div>
</template>

<script>
export default {
  props: {
    comment: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: {
        body: this.comment.body,
      },
      errorMsgs: null,
      currUpdateState: null,
      updateCommentController: null,
    };
  },
  computed: {
    updateState() {
      return {
        NotUpdating: 0,
        Updating: 1,
        FailedToUpdate: 2,
      };
    },
  },
  created() {
    this.currUpdateState = this.updateState.NotUpdating;
  },
  mounted() {
    this.$refs.textarea.focus();
  },
  methods: {
    async updateComment() {
      try {
        this.currUpdateState = this.updateState.Updating;

        this.updateCommentController = new AbortController();

        await axios.put(
          route("comments.update", { comment: this.comment }),
          this.form,
          { signal: this.updateCommentController.signal }
        );

        this.currUpdateState = this.updateState.NotUpdating;

        this.$emit("updated", { id: this.comment.id, data: this.form });
      } catch (error) {
        if (error.response && error.response.status == 422) {
          this.errorMsgs = error.response.data.errors.body;

          this.currUpdateState = this.updateState.NotUpdating;
        } else if (error.response && error.response.status == 404) {
          this.$toasted.error("Comment Already Deleted", {
            duration: 3000,
            posiiton: "top-center",
            icon: "close",
          });
          this.$emit("deleted", { id: this.comment.id });
        } else {
          this.currUpdateState = this.updateState.FailedToUpdate;
        }
      }
    },
    cancelUpdate() {
      if (this.currUpdateState == this.updateState.Updating) {
        this.updateCommentController.abort();
        this.updateCommentController = null;
      }
      this.resetForm();
      this.$emit("updateCancelled");
    },
    resetForm() {
      this.form = { body: this.comment.body };
      this.errorMsgs = null;
    },
  },
  watch: {
    comment: {
      deep: true,
      handler(newVal) {
        this.form = { body: newVal.body };
      },
    },
  },
};
</script>

<style lang="scss">
.comment-text-area .v-text-field__details {
  margin-bottom: 0px !important;
  padding: 0px !important;
  background: #eee !important;
}

.comment-text-area .v-input__slot {
  margin-bottom: 0px !important;
  overflow: hidden !important;
}

.comment-text-area .v-messages__message {
  margin-top: 10px !important;
}
</style>
