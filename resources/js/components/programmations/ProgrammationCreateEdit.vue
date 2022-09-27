<template>
    <div v-bind:class="programmation ? 'd-none' : 'd-inline'">
      <v-dialog
        v-model="dialog"
        :persistent="overlay"
        width="900"
        eager
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-if="!programmation" v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
            <v-icon class="mr-1" small>fas fa-calendar-alt</v-icon>
            Nova Programação
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
                <label>Espaço <span class="text-danger">*</span></label>
                <v-autocomplete
                  v-model="spaces"
                  :items="spacesList"
                  :loading="spacesLoading"
                  item-text="name"
                  item-value="id"
                  label="Lista de Espaços"
                  no-data-text="Nenhum espaço encontrado"
                  hide-details
                  multiple
                  clearable
                  solo
                ></v-autocomplete>
              </div>

              <div class="col-md-6">
                <label>Categoria <span class="text-danger">*</span></label>
                <v-autocomplete
                  v-model="category"
                  :items="categoriesList"
                  :loading="categoriesLoading"
                  item-text="name"
                  item-value="id"
                  label="Lista de Categorias"
                  no-data-text="Nenhuma categoria encontrada"
                  hide-details
                  clearable
                  solo
                >
                  <template v-slot:selection="data">
                    <div v-if="data.item.color" class="color-preview mr-3" v-bind:style="{backgroundColor: data.item.color}"></div>
                    {{ data.item.name }}
                  </template>
                  
                  <template v-slot:item="data">
                    <v-list-item-content>
                      {{ data.item.name }}
                      <div v-if="data.item.color" class="color-preview ml-auto" v-bind:style="{backgroundColor: data.item.color}"></div>
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <label for="title">Título <span class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fas fa-calendar-alt"></i>
                    </span>
                  </div>

                  <input v-model="title" class="form-control" type="text" placeholder="Digite o título">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <label for="description">Descrição (Opcional)</label>
                <div class="input-group">
                  <textarea v-model="description" rows="3" class="form-control no-resize border-0" type="text" placeholder="Digite uma breve descrição da Programação"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <label v-if="this.authUser.role.tag === 'administrator'">Responsáveis <span class="text-danger">*</span></label>
                <label v-else>Participantes (opcional)</label>
                <v-autocomplete
                  v-model="users"
                  :items="usersList"
                  :loading="usersLoading"
                  item-text="name"
                  item-value="id"
                  label="Lista de Responsáveis"
                  no-data-text="Nenhum responsável encontrado"
                  hide-details
                  multiple
                  clearable
                  solo
                ></v-autocomplete>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label class="mb-3">Horário de Inicio <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="startTime"
                  v-mask="['##:##']"
                  type="tel"
                  label="Horário de Inicio"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>
              </div>

              <div class="col-md-6">
                <label class="mb-3">Horário de Término <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="endTime"
                  v-mask="['##:##']"
                  type="tel"
                  label="Horário de Término"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label class="mb-3">Data de Inicio <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="startDate"
                  v-mask="['##/##/####']"
                  type="tel"
                  label="Data de Inicio"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>
              </div>

              <div class="col-md-6">
                <label class="mb-3">Data de Término (Opcional)</label>
                <v-text-field
                  v-model="endDate"
                  v-mask="['##/##/####']"
                  type="tel"
                  label="Data de Término"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>
              </div>
            </div>
          </v-card-text>
  
          <v-card-actions>
            <v-btn
              color="primary"
              class="elevation-0 mt-3"
              block
              large
              rounded
              :loading="overlay"
              @click="saveProgrammation()"
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
        users: [],
        spaces: [],
        category: null,
        title: "",
        description: "",
        startTime: "13:00",
        endTime: "20:00",
        startDate: moment().format('DD/MM/YYYY'),
        endDate: "",
        usersLoading: true,
        spacesLoading: true,
        categoriesLoading: true,
        usersList: [],
        spacesList: [],
        categoriesList: []
      }),
      methods: {
        async saveProgrammationDates(startDate, endDate) {
          return new Promise((resolve, reject) => {
            if (!startDate || !endDate) {
              reject("As datas devem ser especificadas");
  
              return;
            }

            axios({
                method: 'put',
                url: `/api/programmation/${this.programmation.id}/date`,
                data: {
                  spaces: _.map(this.programmation.spaces, 'space_id'),
                  start_time: this.programmation.start_time,
                  end_time: this.programmation.end_time,
                  start_date: startDate,
                  end_date: endDate
                }
              })
              .then(() => resolve())
              .catch(error => reject(error.response.data.message))
            ;
          });
        },
        saveProgrammation() {
          if ((!this.users.length && this.authUser.role.tag === 'administrator') || !this.spaces.length ||
            !this.category || !this.title ||
            !this.startTime || !this.endTime ||
            !this.startDate) {
            this.$emit("error", "Preencha todos os campos obrigatórios");

            return;
          }

          this.overlay = true;

          axios({
            method: this.programmation ? 'put' : 'post',
            url: `/api/programmation${this.programmation ? `/${this.programmation.id}` : ''}`,
            data: {
              schedule: this.schedule,
              users: this.users,
              spaces: this.spaces,
              category: this.category,
              title: this.title,
              description: this.description,
              start_time: this.startTime,
              end_time: this.endTime,
              start_date: moment(this.startDate, 'DD/MM/YYYY').format('YYYY-MM-DD'),
              end_date: this.endDate ? moment(this.endDate, 'DD/MM/YYYY').format('YYYY-MM-DD') : null
            }
          }).then(response => {
            this.$emit("success", response.data.message);
            
            this.dialog = false;
          })
          .catch(error => this.$emit("error", error.response.data.message))
          .finally(() => this.overlay = false);
        },
        clearCredentials() {
          this.users       = [];
          this.spaces      = [];
          this.category    = null;
          this.title       = "";
          this.description = "";
          this.startTime   = "13:00";
          this.endTime     = "20:00";
          this.startDate   = moment().format('DD/MM/YYYY');
          this.endDate     = "";
        },
        listSchedulerUsers() {
          this.usersLoading = true;
          this.usersList    = [];

          axios.get(`/api/user/scheduler`, {})
            .then(response => {
              this.usersList = response.data.data;

              if (this.authUser.role.tag === 'scheduler') this.usersList = this.usersList.filter(user => user.id !== this.authUser.id)
            })
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.usersLoading = false)
          ;
        },
        listSpaces() {
          this.spacesLoading = true;
          this.spacesList    = [];

          axios.get(`/api/space`, {})
            .then(response => this.spacesList = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.spacesLoading = false)
          ;
        },
        listCategories() {
          this.categoriesLoading = true;
          this.categoriesList    = [];

          axios.get(`/api/category`, {})
            .then(response => this.categoriesList = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.categoriesLoading = false)
          ;
        }
      },
      watch: {
        dialog() {
          if (!this.dialog) {
            this.clearCredentials();

            return;
          }

          this.listSpaces();
          this.listCategories();
          this.listSchedulerUsers();

          if (this.defaultSpaces) this.spaces = this.defaultSpaces;
          if (this.defaultCategory) this.category = this.defaultCategory;

          if (this.programmation) {
            this.users       = _.map(this.programmation.users, 'user_id');
            this.spaces      = _.map(this.programmation.spaces, 'space_id');
            this.category    = this.programmation.category_id;
            this.title       = this.programmation.title;
            this.description = this.programmation.description;
            this.startTime   = this.programmation.start_time.substring(0, 5);
            this.endTime     = this.programmation.end_time.substring(0, 5);
            this.startDate   = moment(this.programmation.start_date).format('DD/MM/YYYY');
            this.endDate     = this.programmation.end_date ? moment(this.programmation.end_date).format('DD/MM/YYYY') : "";
          }
        }
      },
      props: {
        authUser: {},
        schedule: '',
        programmation: {},
        color: "",
        defaultSpaces: null,
        defaultCategory: null,
        defaultStartDate: null
      }
    }
  </script>