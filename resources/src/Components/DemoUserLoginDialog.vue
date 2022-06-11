<template>
  <v-dialog v-model="dialogShown" max-width="600px">
    <template v-slot:activator="{ on, attrs }">
      <span v-bind="attrs" v-on="on">
        <slot>
          <v-btn color="tertiary" block large>
            <span class="white--text">Login As Demo User</span>
          </v-btn>
        </slot>
      </span>
    </template>

    <v-card class="pa-8">
      <div class="d-flex justify-space-between align-center">
        <div class="text-h5 mx-3">Login As Demo User</div>
        <v-btn icon @click="dialogShown = false">
          <v-icon>mdi-close</v-icon>
        </v-btn>
      </div>
      <hr class="my-3" />
      <v-row>
        <v-col cols="12" sm="6">
          <h3 class="text-center">Demo Admin</h3>
          <p class="text-justify">
            Log in as an Admin to fully control the system.
          </p>
          <v-btn
            color="secondary"
            :disabled="currUiState != uiStates.None"
            :loading="currUiState == uiStates.AdminLogin"
            @click="adminLogin"
            block
          >
            Login As Demo Admin
            <v-icon right>mdi-login</v-icon>
          </v-btn>
        </v-col>
        <v-col cols="12" sm="6">
          <h3 class="text-center">Demo PM</h3>
          <p class="text-justify">
            Log in as a Project Manager to manage your projects.
          </p>
          <v-btn
            color="secondary"
            :disabled="currUiState != uiStates.None"
            :loading="currUiState == uiStates.PMLogin"
            @click="pmLogin"
            block
          >
            Login As Demo PM
            <v-icon right>mdi-login</v-icon>
          </v-btn>
        </v-col>
        <v-col cols="12" sm="6">
          <h3 class="text-center">Demo Dev</h3>
          <p class="text-justify">Log in as a Developer to resolve issues.</p>
          <v-btn
            color="secondary"
            :disabled="currUiState != uiStates.None"
            :loading="currUiState == uiStates.DevLogin"
            @click="devLogin"
            block
          >
            Login As Demo Dev
            <v-icon right>mdi-login</v-icon>
          </v-btn>
        </v-col>
        <v-col cols="12" sm="6">
          <h3 class="text-center">Demo Tester</h3>
          <p class="text-justify">
            Log in as a Teser to report and verify issues.
          </p>
          <v-btn
            color="secondary"
            :disabled="currUiState != uiStates.None"
            :loading="currUiState == uiStates.TesterLogin"
            @click="testerLogin"
            block
          >
            Login As Demo Tester
            <v-icon right>mdi-login</v-icon>
          </v-btn>
        </v-col>
      </v-row>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  data() {
    return {
      dialogShown: false,
      currUiState: null,

      loginForm: this.$inertia.form({
        email: "",
        password: "password",
      }),
    };
  },
  computed: {
    uiStates() {
      return {
        None: 0,
        AdminLogin: 1,
        PMLogin: 2,
        DevLogin: 3,
        TesterLogin: 4,
      };
    },
  },
  created() {
    this.currUiState = this.uiStates.None;
  },
  methods: {
    adminLogin() {
      this.loginForm.email = "demo-admin";
      this.login({
        onStart: () => {
          this.currUiState = this.uiStates.AdminLogin;
        },
      });
    },
    pmLogin() {
      this.loginForm.email = "demo-pm";
      this.login({
        onStart: () => {
          this.currUiState = this.uiStates.PMLogin;
        },
      });
    },
    devLogin() {
      this.loginForm.email = "demo-dev";
      this.login({
        onStart: () => {
          this.currUiState = this.uiStates.DevLogin;
        },
      });
    },
    testerLogin() {
      this.loginForm.email = "demo-tester";
      this.login({
        onStart: () => {
          this.currUiState = this.uiStates.TesterLogin;
        },
      });
    },
    login(options = {}) {
      this.loginForm.post("/login", {
        onFinish: () => {
          this.currUiState = this.uiStates.None;
        },
        ...options,
      });
    },
  },
};
</script>

<style lang="scss">
</style>
