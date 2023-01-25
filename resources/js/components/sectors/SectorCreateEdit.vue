<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="overlay"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!sector" v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
          <v-icon class="mr-1" small>fas fa-building</v-icon>
          Novo Equipamento Cultural
        </v-btn>
        <v-btn
          v-else
          v-bind="attrs"
          v-on="on"
          class="elevation-0 mr-1"
          color="primary"
          title="Editar Equipamento Cultural"
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
              <label>Responsável <span class="text-danger">*</span></label>
              <v-autocomplete
                v-model="user"
                :items="usersList"
                :loading="usersLoading"
                item-text="name"
                item-value="id"
                label="Lista de Responsáveis"
                no-data-text="Nenhum responsável encontrado"
                hide-details
                solo
              ></v-autocomplete>

              <template v-for="(errorMessage, index) in errorMessages('responsible')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="name">Nome <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-building"></i>
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
            @click="saveSector()"
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
      user: null,
      name: "",
      active: true,
      fieldErrors: [],
      usersLoading: true,
      usersList: []
    }),
    methods: {
      errorMessages(field) {
        if (!(`${field}` in this.fieldErrors)) return [];

        return this.fieldErrors[`${field}`];
      },
      saveSector() {
        this.overlay = true;
        
        axios({
          method: this.sector ? 'put' : 'post',
          url: `/api/sector${this.sector ? `/${this.sector.id}` : ''}`,
          data: {
            user: this.user,
            name: this.name,
            active: this.active
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
      listResponsibleUsers() {
          this.usersLoading = true;
          this.usersList    = [];

          axios.get(`/api/user/responsible`, {})
            .then(response => this.usersList = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.usersLoading = false)
          ;
      },
      clearCredentials() {
        this.user   = null;
        this.name   = "";
        this.active = true;
      }
    },
    watch: {
      dialog() {
        if (!this.dialog) return;

        this.listResponsibleUsers();

        if (!this.sector) return;

        this.user   = this.sector.user.id;
        this.name   = this.sector.name;
        this.active = this.sector.active;
      }
    },
    props: {
      sector: {}
    }
  }
</script>