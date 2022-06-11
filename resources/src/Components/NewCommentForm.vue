<template>
  <div>
    <div class="d-flex align-end">
      <v-textarea
        v-model="form.body"
        :label="`Add ${parentComment ? 'Reply' : 'Comment'}`"
        rows="1"
        class="reply-from rounded-pill d-block white ma-0 pa-0 flex-grow-1"
        outlined
        auto-grow
        :loading="currCreateState == createState.Creating"
        :disabled="currCreateState == createState.Creating"
        :error="
          Boolean(errorMsgs) || currCreateState == createState.FailedToCreate
        "
        :error-messages="errorMsgs"
      ></v-textarea>
      <div>
        <div>
          <v-btn
            v-if="currCreateState != createState.Creating"
            color="primary"
            @click="createComment"
            :loading="currCreateState == createState.Creating"
            :disabled="currCreateState == createState.Creating"
            class="ml-2"
          >
            Add
          </v-btn>
        </div>
        <div>
          <v-btn
            v-if="currCreateState == createState.Creating"
            class="ml-2"
            color="error"
            @click="cancelCreate"
          >
            <v-icon color="white">mdi-cancel</v-icon>
          </v-btn>
        </div>
      </div>
    </div>
    <span
      v-if="currCreateState == createState.FailedToCreate"
      class="d-inline-block ml-3 mt-2 error--text"
    >
      Failed To Add
    </span>
  </div>
</template>

<script>
export default {
  props: {
    parentComment: {
      type: Object,
      default: null,
    },
    ticket: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      form: {
        body: "",
      },
      errorMsgs: null,
      currCreateState: null,
      createCommentController: null,
    };
  },
  computed: {
    createState() {
      return {
        NotCreating: 0,
        Creating: 1,
        FailedToCreate: 2,
      };
    },
  },
  created() {
    this.currCreateState = this.createState.NotCreating;
  },
  methods: {
    async createComment() {
      try {
        this.currCreateState = this.createState.Creating;

        this.createCommentController = new AbortController();

        const res = await axios.post(
          route("comments.store"),
          {
            ...this.form,
            parent: this.parentComment?.id,
            ticket: this.ticket.id,
          },
          { signal: this.createCommentController.signal }
        );

        this.currCreateState = this.createState.NotCreating;

        this.$emit("created", { comment: res.data.data });
        this.resetForm();
      } catch (error) {
        if (error.response && error.response.status == 422) {
          this.errorMsgs = error.response.data.errors.body;

          this.currCreateState = this.createState.NotCreating;
        } else {
          this.currCreateState = this.createState.FailedToCreate;
        }
      }
    },
    cancelCreate() {
      if (this.currCreateState == this.createState.Creating) {
        this.createCommentController.abort();
        this.createCommentController = null;
      }
      this.resetForm();
      this.$emit("createCancelled");
    },
    resetForm() {
      this.form = { body: "" };
      this.errorMsgs = null;
    },
  },
};
</script>

<style lang="scss">
.reply-form .v-application--is-ltr .v-messages {
  background: white !important;
}
</style>
