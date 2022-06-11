<template>
  <div>
    <div>
      <div
        class="text-center"
        v-if="currRecepientsState == recepientsState.FailedToLoad"
      >
        <span class="error--text">Error Loading Receipents</span>
        <v-icon @click="loadRecepients" class="cur-point"> mdi-restart </v-icon>
      </div>
      <v-select
        v-model="messageData.recepients"
        :items="possibleRecepientsFormatted"
        :menu-props="{ maxHeight: '400' }"
        label="Receipents"
        multiple
        clearable
        :error="Boolean(errors.recepients)"
        :error-messages="errors.recepients"
        :loading="currRecepientsState == recepientsState.Loading"
        :disabled="
          disableRecepients || currRecepientsState != recepientsState.Loaded
        "
        chips
        deletable-chips
        class="mb-3"
      >
      </v-select>
    </div>
    <v-text-field
      v-model="messageData.subject"
      label="Subject"
      outlined
      clearable
      :error="Boolean(errors.subject)"
      :error-messages="errors.subject"
      class="mb-3"
    ></v-text-field>
    <v-textarea
      v-model="messageData.body"
      outlined
      label="Body"
      :error="Boolean(errors.body)"
      :error-messages="errors.body"
      class="mb-3"
    ></v-textarea>
    <slot
      name="saveActionButtons"
      :data="messageDataCloned"
      :reset="resetMessageData"
    >
    </slot>
  </div>
</template>

<script>
export default {
  props: {
    message: {
      type: Object,
      default: () => ({}),
    },
    errors: {
      type: Object,
      default: () => ({}),
    },
    allRecepients: {
      type: Object,
      default: null,
    },
    disableRecepients: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    const { subject, body } = this.message;
    return {
      messageData: {
        subject,
        body,
        recepients: this.message?.receipents?.map((u) => u.id.toString()) ?? [],
      },
      possibleRecepients: [],
      currRecepientsState: null,
    };
  },
  computed: {
    authUser() {
      return this.$page.props.auth.user?.data;
    },
    messageDataCloned() {
      return _.cloneDeep(this.messageData);
    },
    possibleRecepientsFormatted() {
      return this.possibleRecepients.map((u) => ({
        value: u.id.toString(),
        text: u.profile.name,
      }));
    },
    recepientsState() {
      return {
        Loading: 0,
        Loaded: 1,
        FailedToLoad: 2,
      };
    },
  },
  async created() {
    if (this.allRecepients) {
      this.possibleRecepients = this.allRecepients.data;
      this.currRecepientsState = this.recepientsState.Loaded;
      return;
    }
    this.currRecepientsState = this.recepientsState.Loading;
    await this.loadRecepients();
  },
  methods: {
    resetMessageData() {
      const { subject, body } = this.message;
      this.messageData = {
        subject,
        body,
        recepients: this.message?.receipents?.map((u) => u.id.toString()) ?? [],
      };
      this.$emit("data-reset");
    },
    async loadRecepients() {
      try {
        this.currRecepientsState = this.recepientsState.Loading;

        const res = await axios.get(
          route("users.possibleMessageRecepients.index", {
            user: this.authUser,
          })
        );

        this.possibleRecepients = res.data.data;
        this.currRecepientsState = this.recepientsState.Loaded;
      } catch (error) {
        this.currRecepientsState = this.recepientsState.FailedToLoad;
      }
    },
  },
  watch: {
    message() {
      this.resetMessageData();
    },
  },
};
</script>

<style lang="scss" scoped>
</style>
