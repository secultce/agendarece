<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="overlay"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!category" v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
          <v-icon class="mr-1" small>fas fa-tags</v-icon>
          Nova Categoria
        </v-btn>
        <v-btn
          v-else
          v-bind="attrs"
          v-on="on"
          class="elevation-0 mr-1"
          color="primary"
          title="Editar Categoria"
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
                    <i class="fas fa-tag"></i>
                  </span>
                </div>

                <input v-model="name" class="form-control" type="text" placeholder="Digite o nome">
              </div>
            </div>
          </div>

          <div class="row">
              <div class="col-md-12">
                  <label class="mb-3">Cor para Representação <span class="text-danger">*</span></label>
                  <v-text-field 
                  v-model="color"
                  v-mask="['!#XXXXXXXX']"
                  solo
                  single-line
                  hide-details
                  dense
                  readonly
                  @click="colorMenu = true"
                  >
                  <template v-slot:append>
                      <v-menu v-model="colorMenu" top nudge-bottom="105" nudge-left="16" :close-on-content-click="false">
                      <template v-slot:activator="{ on }">
                          <div :style="swatchStyle" v-on="on" />
                      </template>
                      <v-card>
                          <v-card-text class="pa-0">
                          <v-color-picker v-model="color" flat hide-inputs />
                          </v-card-text>
                      </v-card>
                      </v-menu>
                  </template>
                  </v-text-field>
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
  import { mask } from 'vue-the-mask';
  
  export default {
    directives: { mask: mask },
    data: () => ({
      overlay: false,
      dialog: false,
      name: "",
      color: "#2196F3FF",
      colorMenu: false
    }),
    computed: {
      swatchStyle() {
        const { color, colorMenu } = this
        
        return {
          backgroundColor: color,
          cursor: 'pointer',
          height: '30px',
          width: '30px',
          borderRadius: colorMenu ? '50%' : '4px',
          transition: 'border-radius 200ms ease-in-out'
        }
      },
    },
    methods: {
      saveSpace() {
        if (!this.name || !this.color) {
          this.$emit("error", "Preencha todos os campos");

          return;
        }

        this.overlay = true;

        axios({
          method: this.category ? 'put' : 'post',
          url: `/api/category${this.category ? `/${this.category.id}` : ''}`,
          data: {
            name: this.name,
            color: this.color
          }
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
        this.name  = "";
        this.color = "";
      }
    },
    watch: {
      dialog() {
        if (!this.category) return;

        this.name  = this.category.name;
        this.color = (this.category.color.length === 7 ? `${this.category.color}FF` : this.category.color).toUpperCase();
      }
    },
    props: {
      category: {}
    }
  }
</script>