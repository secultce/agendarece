<template>
    <div class="d-inline">
      <v-dialog
        v-model="dialog"
        width="700"
        :persistent="overlay"
        eager
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-if="!customHoliday" v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
            <v-icon class="mr-1" small>fas fa-calendar</v-icon>
            Nova Data Comemorativa
          </v-btn>
          <v-btn
            v-else
            v-bind="attrs"
            v-on="on"
            class="elevation-0 mr-1"
            color="primary"
            title="Editar Data Comemorativa"
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
                      <i class="fas fa-calendar"></i>
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
              <div class="col-md-6">
                <label class="mb-3">Data de Inicio <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="startAt"
                  v-mask="'##/##'"
                  type="tel"
                  label="Data de Inicio"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>

                <template v-for="(errorMessage, index) in errorMessages('start_at')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>

              <div class="col-md-6">
                <label class="mb-3">Data de Término <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="endAt"
                  v-mask="'##/##'"
                  type="tel"
                  label="Data de Término"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>

                <template v-for="(errorMessage, index) in errorMessages('end_at')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
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
              @click="saveCustomHoliday()"
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
    import { maska } from 'maska';

    export default {
      directives: { mask: maska },
      data: () => ({
        overlay: false,
        dialog: false,
        name: "",
        startAt: "",
        endAt: "",
        fieldErrors: [],
      }),
      methods: {
        errorMessages(field) {
          if (!(`${field}` in this.fieldErrors)) return [];
  
          return this.fieldErrors[`${field}`];
        },
        saveCustomHoliday() {
          this.overlay = true;
          
          axios({
            method: this.customHoliday ? 'put' : 'post',
            url: `/api/custom-holiday${this.customHoliday ? `/${this.customHoliday.id}` : ''}`,
            data: {
              name: this.name,
              start_at: this.startAt.split('/').reverse().join('-'),
              end_at: this.endAt.split('/').reverse().join('-')
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
          this.name    = "";
          this.startAt = "";
          this.endAt   = "";
        }
      },
      watch: {
        dialog() {
          if (!this.customHoliday) return;
  
          this.name    = this.customHoliday.name;
          this.startAt = this.customHoliday.start_at.split('-').reverse().join('/');
          this.endAt   = this.customHoliday.end_at.split('-').reverse().join('/');
        }
      },
      props: {
        customHoliday: {}
      }
    }
  </script>