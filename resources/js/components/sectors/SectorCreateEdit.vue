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
          Novo Setor
        </v-btn>
        <v-btn
          v-else
          v-bind="attrs"
          v-on="on"
          class="elevation-0 mr-1"
          color="primary"
          title="Editar Setor"
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
      name: "",
      active: true,
      fieldErrors: [],
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
      clearCredentials() {
        this.name   = "";
        this.active = true;
      }
    },
    watch: {
      dialog() {
        if (!this.dialog || !this.sector) return;

        this.name   = this.sector.name;
        this.active = this.sector.active;
      }
    },
    props: {
      sector: {}
    }
  }
</script>