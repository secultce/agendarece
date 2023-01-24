<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="overlay"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!user" v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
          <v-icon class="mr-1" small>fas fa-users</v-icon>
          Novo Usuário
        </v-btn>
        <v-btn
          v-else
          v-bind="attrs"
          v-on="on"
          class="elevation-0 mr-1"
          color="primary"
          title="Editar Usuário"
          fab 
          small
        >
          <v-icon small>fa-edit</v-icon>
        </v-btn>
      </template>

      <v-card class="px-3 pb-6 pt-2">
        <v-card-title
          class="headline"
          primary-title
        >
          <v-spacer></v-spacer>
          <v-btn icon :disabled="overlay" @click="dialog = false">
            <v-icon>fas fa-times</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text>
          <div class="row">
            <div class="col-md-6">
              <label for="name">Nome <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-user"></i>
                  </span>
                </div>

                <input v-model="name" class="form-control" type="text" placeholder="Digite o nome">
              </div>

              <template v-for="(errorMessage, index) in errorMessages('name')">
                <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
              </template>
            </div>
            <div class="col-md-6">
              <label for="email">Email <span class="text-danger">*</span></label>

              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                  </span>
                </div>

                <input v-model="email" class="form-control" type="email" placeholder="Digite o email">
              </div>

              <template v-for="(errorMessage, index) in errorMessages('email')">
                <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
              </template>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="function">Função <span class="text-danger">*</span></label>
              <v-autocomplete
                v-model="role"
                :items="rolesList"
                :loading="rolesLoading"
                item-text="name"
                item-value="id"
                label="Função do Usuário"
                no-data-text="Nenhuma função encontrada"
                return-object
                hide-details
                solo
              ></v-autocomplete>

              <template v-for="(errorMessage, index) in errorMessages('role')">
                <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
              </template>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <label for="password">Senha <span v-if="!user" class="text-danger">*</span></label>
              <div class="input-group" x-data="{ visible: false }">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                  </span>
                </div>

                <input v-model="password" class="form-control" x-bind:type="visible ? 'text' : 'password'" placeholder="Digite a senha">

                <div class="input-group-append">
                  <span class="input-group-text pointer-cursor" x-on:click="visible = !visible">
                    <i x-bind:class="'fas fa-eye' + (visible ? '-slash' : '')"></i>
                  </span>
                </div>
              </div>

              <template v-for="(errorMessage, index) in errorMessages('password')">
                <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
              </template>
            </div>
            <div class="col-md-6">
              <label for="password_confirmation">Confirmar Senha <span v-if="!user" class="text-danger">*</span></label>
              <div class="input-group" x-data="{ visible: false }">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                  </span>
                </div>

                <input v-model="password_confirmation" class="form-control" x-bind:type="visible ? 'text' : 'password'" placeholder="Cofirme a senha">

                <div class="input-group-append">
                  <span class="input-group-text pointer-cursor" x-on:click="visible = !visible">
                    <i x-bind:class="'fas fa-eye' + (visible ? '-slash' : '')"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="row" v-if="isSectorSelectable">
            <div class="col-md-12">
              <label for="function">Setor (opcional)</label>
              <v-autocomplete
                v-model="sector"
                :items="sectorsList"
                :loading="sectorsLoading"
                item-text="name"
                item-value="id"
                label="Setor do Usuário"
                no-data-text="Nenhum setor encontrado"
                hide-details
                solo
              ></v-autocomplete>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <v-switch
                v-model="active"
                label="Ativo"
                hide-details
              ></v-switch>
            </div>
          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            class="elevation-0 mt-3 px-5"
            large
            rounded
            :loading="overlay"
            @click="saveUser()"
          >
            Salvar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-overlay :value="overlay">
      <v-progress-circular indeterminate size="64"></v-progress-circular>
    </v-overlay>
  </div>
</template>
<script>
  export default {
    data: () => ({
      overlay: false,
      dialog: false,
      sector: null,
      role: {},
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      active: true,
      rolesLoading: true,
      sectorsLoading: true,
      rolesList: [],
      sectorsList: [],
      fieldErrors: [],
    }),
    methods: {
      errorMessages(field) {
        if (!(`${field}` in this.fieldErrors)) return [];

        return this.fieldErrors[`${field}`];
      },
      saveUser() {
        this.overlay = true;
        
        axios({
          method: this.user ? 'put' : 'post',
          url: `/api/user${this.user ? `/${this.user.id}` : ''}`,
          data: {
            sector: this.isSectorSelectable ? this.sector : null,
            role: this.role.id,
            name: this.name,
            email: this.email,
            active: this.active,
            password: this.password,
            password_confirmation: this.password_confirmation
          }
        }).then(response => {
          this.$emit("success", response.data.message);
          this.clearCredentials();

          this.dialog = false;
        })
        .catch(error => {
          if ("errors" in error.response.data) {
            this.fieldErrors = error.response.data.errors;

            return;
          }

          this.$emit("error", error.response.data.message)
        })
        .finally(() => this.overlay = false);
      },
      listRoles() {
        this.rolesLoading = true;
        this.rolesList    = [];

        axios.get(`/api/role`, {})
          .then(response => this.rolesList = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.rolesLoading = false)
        ;
      },
      listSectors() {
        this.sectorsLoading = true;
        this.sectorsList    = [];

        axios.get(`/api/sector`, {})
          .then(response => this.sectorsList = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.sectorsLoading = false)
        ;
      },
      clearCredentials() {
        this.role                  = "";
        this.name                  = "";
        this.email                 = "";
        this.active                = true;
        this.password              = "";
        this.password_confirmation = "";
      }
    },
    watch: {
      dialog() {
        if (!this.dialog) return;

        this.listRoles();

        if (this.authUser.role.tag === 'administrator') this.listSectors();

        if (!this.user) return;

        this.sector = this.user.sector_id;
        this.role   = this.user.role;
        this.name   = this.user.name;
        this.email  = this.user.email;
        this.active = this.user.active;
      }
    },
    computed: {
      isSectorSelectable() {
        return this.authUser.role.tag === 'administrator' && ['administrator', 'responsible'].indexOf(this.role.tag) === -1;
      }
    },
    props: {
      user: {},
      authUser: {}
    }
  }
</script>