<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="overlay"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!space" v-bind="attrs" v-on="on" color="primary" class="elevation-0" >
          <v-icon class="mr-1" small>fas fa-map-marker-alt</v-icon>
          Novo Espaço
        </v-btn>
        <v-btn
          v-else
          v-bind="attrs"
          v-on="on"
          class="elevation-0 mr-1"
          color="primary"
          title="Editar Espaço"
          fab 
          small
        >
          <v-icon small>fa-edit</v-icon>
        </v-btn>
      </template>

      <v-card class="px-3 pb-6 pt-2">
        <v-card-title
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
              <label class="mb-3">Ícone para Representação <span class="text-danger">*</span></label>
              <v-file-input 
                v-model="icon"
                prepend-icon=""
                placeholder="Escolha um Ícone"
                accept="image/svg+xml"
                solo
                hide-details
              ></v-file-input>

              <template v-for="(errorMessage, index) in errorMessages('icon')">
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
                    <i class="fas fa-map-marker-alt"></i>
                  </span>
                </div>

                <input v-model="name" class="form-control" type="text" placeholder="Digite o nome">
              </div>

              <template v-for="(errorMessage, index) in errorMessages('name')">
                <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
              </template>
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
                label="Equipamento Cultural do Espaço"
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
            :loading="overlay"
            @click="saveSpace()"
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
      icon: null,
      sector: null,
      name: "",
      active: true,
      fieldErrors: [],
      sectorsLoading: true,
      sectorsList: [],
    }),
    methods: {
      errorMessages(field) {
        if (!(`${field}` in this.fieldErrors)) return [];

        return this.fieldErrors[`${field}`];
      },
      saveSpace() {
        this.overlay = true;

        let formData = new FormData();
        let config   = { headers: { 'Content-Type': 'multipart/form-data' } };

        if (this.icon) formData.append('icon', this.icon);
        
        formData.append('_method', this.space ? 'PUT' : 'POST');
        formData.append('name', this.name);
        formData.append('active', this.active);
        formData.append('sector', this.isSectorSelectable ? (this.sector ? this.sector : '') : '');

        axios({
          method: 'POST',
          url: `/api/space${this.space ? `/${this.space.id}` : ''}`,
          data: formData,
          config
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
          
          this.$emit("error", error.response.data.message);
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
        this.sector = null;
        this.icon   = null;
        this.name   = "";
        this.active = true;
      }
    },
    watch: {
      dialog() {
        if (!this.dialog) return;

        if (this.isSectorSelectable) this.listSectors();

        if (!this.space) return;

        this.sector = this.space.sector_id;
        this.name   = this.space.name;
        this.active = this.space.active;
      }
    },
    computed: {
      isSectorSelectable() {
        return this.authUser.role.tag === 'administrator';
      }
    },
    props: {
      space: {},
      authUser: {}
    }
  }
</script>