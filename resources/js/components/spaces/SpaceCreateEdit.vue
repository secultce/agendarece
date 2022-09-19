<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="overlay"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!space" v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
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
              <label class="mb-3">Ícone para Representação <span class="text-danger">*</span></label>
              <v-file-input 
                v-model="icon"
                prepend-icon=""
                placeholder="Escolha um Ícone"
                accept="image/svg"
                solo
                hide-details
              ></v-file-input>
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
            class="elevation-0 mt-3"
            large
            rounded
            block
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
      name: "",
      active: true,
    }),
    methods: {
      saveSpace() {
        if (!this.name) {
          this.$emit("error", "Preencha todos os campos");

          return;
        }

        this.overlay = true;

        let formData = new FormData();
        let config   = { headers: { 'Content-Type': 'multipart/form-data' } };

        formData.append('icon', this.icon);
        formData.append('name', this.name);
        formData.append('active', this.active);

        if (this.file) {
          config = {};
          formData = {
            name: this.name,
            active: this.active
          }
        }

        axios({
          method: this.space ? 'put' : 'post',
          url: `/api/space${this.space ? `/${this.space.id}` : ''}`,
          data: formData,
          config
        }).then(response => {
          this.$emit("success", response.data.message);
          this.clearCredentials();
        })
        .catch(error => this.$emit("error", error.response.data.message))
        .finally(() => {
          this.overlay = false;
          this.dialog = false;
        });
      },
      clearCredentials() {
        this.icon   = null;
        this.name   = "";
        this.active = true;
      }
    },
    watch: {
      dialog() {
        if (!this.space) return;

        this.name   = this.space.name;
        this.active = this.space.active;
      }
    },
    props: {
      space: {}
    }
  }
</script>