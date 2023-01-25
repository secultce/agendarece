<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="overlay"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!schedule" v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
          <v-icon class="mr-1" small>fas fa-calendar-alt</v-icon>
          Nova Agenda
        </v-btn>
        <v-btn
          v-else
          v-bind="attrs"
          v-on="on"
          class="elevation-0 mr-1"
          color="primary"
          title="Editar Agenda"
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
            <div class="col-md-12">
              <label for="name">Nome <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-calendar-alt"></i>
                  </span>
                </div>

                <input v-model="name" class="form-control" type="text" placeholder="Digite o nome">
              </div>

              <template v-for="(errorMessage, index) in errorMessages('name')">
                <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
              </template>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label>Após compartilhado, quem poderá criar, editar e remover eventos? (Opcional)</label>
              <v-autocomplete
                v-model="users"
                :items="usersList"
                :loading="usersLoading"
                item-text="name"
                item-value="id"
                label="Todos"
                no-data-text="Nenhum usuário encontrado"
                hide-details
                multiple
                clearable
                solo
              ></v-autocomplete>
              <small class="text-muted">* Apenas se o usuário marcado possuir acesso a Agenda</small>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label>Usuários selecionados por padrão na criação de eventos. (Opcional)</label>
              <v-autocomplete
                v-model="shares"
                :items="usersList"
                :loading="usersLoading"
                item-text="name"
                item-value="id"
                label="Nenhum"
                no-data-text="Nenhum usuário encontrado"
                hide-details
                multiple
                clearable
                solo
              ></v-autocomplete>
            </div>
          </div>

          <div class="row" v-if="isSectorSelectable">
            <div class="col-md-12">
              <label for="function">Equipamento Cultural (opcional)</label>
              <v-autocomplete
                v-model="sector"
                :items="sectorsList"
                :loading="sectorsLoading"
                item-text="name"
                item-value="id"
                label="Equipamento Cultural da Categoria"
                no-data-text="Nenhum equipamento cultural encontrado"
                hide-details
                clearable
                solo
              ></v-autocomplete>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <v-switch
                v-model="private"
                label="Agenda Privada"
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
            @click="saveSchedule()"
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
      users: [],
      shares: [],
      sector: null,
      name: "",
      private: true,
      fieldErrors: [],
      usersList: [],
      usersLoading: true,
      sectorsLoading: true,
      sectorsList: [],
    }),
    methods: {
      errorMessages(field) {
        if (!(`${field}` in this.fieldErrors)) return [];

        return this.fieldErrors[`${field}`];
      },
      listUsers() {
        this.usersLoading = true;
        this.usersList = [];

        axios.get(`/api/user/scheduler`, {})
          .then(response => {
            let schedulers = response.data.data.filter(user => user.id !== this.authUser.id);

            axios.get(`/api/user/responsible`, {})
              .then(response => this.usersList = schedulers.concat(response.data.data.filter(user => user.id !== this.authUser.id)))
              .catch(error => {
                this.snackbarMessage = error.response.data.message;
                this.snackbarVisible = true;
              })
              .finally(() => this.usersLoading = false)
            ;
          })
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
            this.usersLoading    = false;
          })
        ;
      },
      saveSchedule() {
        this.overlay = true;
        
        axios({
          method: this.schedule ? 'put' : 'post',
          url: `/api/schedule${this.schedule ? `/${this.schedule.id}` : ''}`,
          data: {
            sector: this.isSectorSelectable ? this.sector : null,
            name: this.name,
            private: this.private,
            users: this.users,
            shares: this.shares
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
        this.sector  = null;
        this.name    = "";
        this.users   = [];
        this.shares  = [];
        this.private = true;
      }
    },
    watch: {
      dialog() {
        if (!this.dialog) return;

        this.listUsers();

        if (this.isSectorSelectable) this.listSectors();

        if (!this.schedule) return;

        this.sector  = this.schedule.sector_id;
        this.name    = this.schedule.name;
        this.users   = _.map(this.schedule.users, 'id');
        this.shares  = _.map(this.schedule.shares, 'id');
        this.private = this.schedule.private;
      }
    },
    computed: {
      isSectorSelectable() {
        return this.authUser.role.tag === 'administrator';
      }
    },
    props: {
      schedule: {},
      authUser: {}
    }
  }
</script>