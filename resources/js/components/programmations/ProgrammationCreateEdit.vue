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
            <div class="row" v-if="!readonly">
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

                <template v-for="(errorMessage, index) in errorMessages('spaces')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
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

                <template v-for="(errorMessage, index) in errorMessages('category')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <label for="title">Título <span v-if="!readonly" class="text-danger">*</span></label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="fas fa-calendar-alt"></i>
                    </span>
                  </div>

                  <input v-model="title" :readonly="readonly" class="form-control" type="text" placeholder="Digite o título">
                </div>

                <template v-for="(errorMessage, index) in errorMessages('title')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <label for="description">Descrição <span v-if="!readonly">(Opcional)</span></label>
                <div class="input-group">
                  <textarea v-model="description" :readonly="readonly" rows="3" class="form-control no-resize border-0" type="text" placeholder="Digite uma breve descrição da Programação"></textarea>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label>Classificação Indicativa <span class="text-danger" v-if="!readonly">*</span></label>
                <v-select
                  v-model="parentalRating"
                  :items="parentalRatings"
                  :readonly="readonly"
                  item-text="name"
                  item-value="id"
                  label="Classificação Indicativa"
                  hide-details
                  solo
                ></v-select>

                <template v-for="(errorMessage, index) in errorMessages('parental_rating')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>

              <div class="col-md-6">
                <label>Ocupação <span v-if="!readonly">(Opcional)</span></label>
                <v-autocomplete
                  v-model="occupation"
                  :items="occupationsList"
                  :loading="occupationsLoading"
                  :readonly="readonly"
                  item-text="name"
                  item-value="id"
                  label="Lista de Ocupações"
                  no-data-text="Nenhuma ocupação encontrada"
                  hide-details
                  clearable
                  solo
                ></v-autocomplete>

                <template v-for="(errorMessage, index) in errorMessages('occupation')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>
            </div>

            <div class="row" v-if="!readonly">
              <div class="col-md-12">
                <label v-if="this.authUser.role.tag === 'administrator'">Participantes <span class="text-danger">*</span></label>
                <label v-else>Participantes (opcional)</label>
                <v-autocomplete
                  v-model="users"
                  :items="usersList"
                  :loading="usersLoading"
                  item-text="name"
                  item-value="id"
                  label="Lista de Participantes"
                  no-data-text="Nenhum participante encontrado"
                  hide-details
                  multiple
                  clearable
                  solo
                >
                  <template v-slot:prepend-item>
                    <v-list-item @click="checkAll('users', 'usersList')">
                      <v-list-item-content>
                        <v-list-item-title>Selecionar Todos</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </template>
                </v-autocomplete>
              </div>
            </div>

            <div class="row" v-if="!readonly">
              <div class="col-md-6">
                <label class="mb-3">Horário de Inicio <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="startTime"
                  v-mask="'##:##'"
                  type="tel"
                  label="Horário de Inicio"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>

                <template v-for="(errorMessage, index) in errorMessages('start_time')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>

              <div class="col-md-6">
                <label class="mb-3">Horário de Término <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="endTime"
                  v-mask="'##:##'"
                  type="tel"
                  label="Horário de Término"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>

                <template v-for="(errorMessage, index) in errorMessages('end_time')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>
            </div>

            <div class="row" v-if="!readonly">
              <div class="col-md-6">
                <label class="mb-3">Data de Inicio <span class="text-danger">*</span></label>
                <v-text-field
                  v-model="startDate"
                  v-mask="'##/##/####'"
                  type="tel"
                  label="Data de Inicio"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>

                <template v-for="(errorMessage, index) in errorMessages('start_date')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>

              <div class="col-md-6">
                <label class="mb-3">Data de Término (Opcional)</label>
                <v-text-field
                  v-model="endDate"
                  v-mask="'##/##/####'"
                  type="tel"
                  label="Data de Término"
                  solo
                  single-line
                  hide-details
                  dense
                ></v-text-field>
              </div>
            </div>

            <div class="row" v-if="!endDate && !readonly">
              <div class="col-md-12">
                <label class="mb-3">Quais dias da semana a programação irá se repetir? <span class="text-danger">*</span></label>
                <v-select
                  v-model="loopDays"
                  :items="weekDays"
                  item-text="name"
                  item-value="id"
                  label="Dias da Semana"
                  no-data-text="Nenhum dia encontrado"
                  hide-details
                  multiple
                  clearable
                  solo
                >
                  <template v-slot:prepend-item>
                    <v-list-item @click="checkAll('loopDays', 'weekDays')">
                      <v-list-item-content>
                        <v-list-item-title>Selecionar Todos</v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </template>
                </v-select>

                <template v-for="(errorMessage, index) in errorMessages('loop_days')">
                  <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
                </template>
              </div>
            </div>
          </v-card-text>
  
          <v-card-actions v-if="!readonly">
            <v-spacer></v-spacer>
            <v-btn
              v-if="programmation"
              color="warning"
              class="elevation-0 mt-3 px-5"
              large
              rounded
              :disabled="overlay"
              @click="saveProgrammation(true)"
            >
              Clonar
            </v-btn>
            <v-btn
              color="primary"
              class="elevation-0 mt-3 px-5"
              large
              rounded
              :disabled="overlay"
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
    import { maska } from 'maska';

    export default {
      directives: { mask: maska },
      data: () => ({
        overlay: false,
        dialog: false,
        readonly: false,
        users: [],
        spaces: [],
        category: null,
        occupation: null,
        weekDays: [
          {id: 0, name: "Domingo"},
          {id: 1, name: "Segunda"},
          {id: 2, name: "Terça"},
          {id: 3, name: "Quarta"},
          {id: 4, name: "Quinta"},
          {id: 5, name: "Sexta"},
          {id: 6, name: "Sábado"}
        ],
        parentalRatings: [
          {id: 0, name: "Livre"},
          {id: 1, name: "10+"},
          {id: 2, name: "12+"},
          {id: 3, name: "14+"},
          {id: 4, name: "16+"},
          {id: 5, name: "18+"}
        ],
        title: "",
        description: "",
        parentalRating: 0,
        startTime: "13:00",
        endTime: "20:00",
        startDate: moment().format('DD/MM/YYYY'),
        endDate: "",
        loopDays: [],
        usersLoading: true,
        spacesLoading: true,
        categoriesLoading: true,
        occupationsLoading: true,
        usersList: [],
        spacesList: [],
        categoriesList: [],
        occupationsList: [],
        fieldErrors: [],
      }),
      methods: {
        errorMessages(field) {
          if (!(`${field}` in this.fieldErrors)) return [];

          return this.fieldErrors[`${field}`];
        },
        checkAll(key, list) {
          this[key] = this[list];
        },
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
                  schedule: this.schedule,
                  spaces: _.map(this.programmation.spaces, 'space_id'),
                  start_time: this.programmation.start_time,
                  end_time: this.programmation.end_time,
                  loop_days: this.programmation.loop_days,
                  start_date: startDate,
                  end_date: endDate
                }
              })
              .then(() => resolve())
              .catch(error => reject(error.response.data.message))
            ;
          });
        },
        saveProgrammation(clone = false) {
          if (!this.users.length && this.authUser.role.tag === 'administrator') {
            this.$emit("error", "Escolha pelo menos um participante");

            return;
          }

          this.overlay = true;

          axios({
            method: this.programmation && !clone ? 'put' : 'post',
            url: `/api/programmation${this.programmation && !clone ? `/${this.programmation.id}` : ''}`,
            data: {
              occupation: this.occupation,
              schedule: this.schedule,
              users: this.users,
              spaces: this.spaces,
              category: this.category,
              title: this.title,
              description: this.description,
              parental_rating: this.parentalRating,
              start_time: this.startTime,
              end_time: this.endTime,
              start_date: this.startDate ? moment(this.startDate, 'DD/MM/YYYY').format('YYYY-MM-DD') : null,
              end_date: this.endDate ? moment(this.endDate, 'DD/MM/YYYY').format('YYYY-MM-DD') : null,
              loop_days: this.loopDays
            }
          }).then(response => {
            this.$emit("success", response.data.message);
            
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
          this.users          = [];
          this.spaces         = [];
          this.category       = null;
          this.title          = "";
          this.description    = "";
          this.parentalRating = 0;
          this.startTime      = "13:00";
          this.endTime        = "20:00";
          this.startDate      = moment().format('DD/MM/YYYY');
          this.endDate        = "";
          this.loopDays       = [];
        },
        listSchedulerUsers() {
          this.usersLoading = true;
          this.usersList    = [];

          axios.get(`/api/user/scheduler`, {})
            .then(response => {
              let schedulers = response.data.data;

              if (this.authUser.role.tag === 'scheduler') schedulers = schedulers.filter(user => user.id !== this.authUser.id)

              axios.get(`/api/user/responsible`, {})
                .then(response => this.usersList = schedulers.concat(response.data.data))
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
            })
          ;
        },
        listSpaces() {
          this.spacesLoading = true;
          this.spacesList    = [];

          axios.get(`/api/space${this.sector ? `/${this.sector}` : ''}`, {})
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

          axios.get(`/api/category${this.sector ? `/${this.sector}` : ''}`, {})
            .then(response => this.categoriesList = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.categoriesLoading = false)
          ;
        },
        listOccupations() {
          this.occupationsLoading = true;
          this.occupationsList    = [];

          axios.get(`/api/occupation${this.sector ? `/${this.sector}` : ''}`, {})
            .then(response => this.occupationsList = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.occupationsLoading = false)
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
          this.listOccupations();

          if (this.schedule.shares.length) this.users = _.map(this.schedule.shares, 'id');
          if (this.defaultSpaces) this.spaces = this.defaultSpaces;
          if (this.defaultCategory) this.category = this.defaultCategory;

          if (this.programmation) {
            this.occupation     = this.programmation.occupation_id;
            this.users          = _.map(this.programmation.users, 'user_id');
            this.spaces         = _.map(this.programmation.spaces, 'space_id');
            this.category       = this.programmation.category_id;
            this.title          = this.programmation.title;
            this.description    = this.programmation.description;
            this.parentalRating = this.programmation.parental_rating;
            this.startTime      = this.programmation.start_time.substring(0, 5);
            this.endTime        = this.programmation.end_time.substring(0, 5);
            this.startDate      = moment(this.programmation.start_date).format('DD/MM/YYYY');
            this.endDate        = this.programmation.end_date ? moment(this.programmation.end_date).format('DD/MM/YYYY') : "";
            this.loopDays       = this.programmation.loop_days;
          }
        }
      },
      props: {
        authUser: {},
        schedule: null,
        programmation: {},
        color: "",
        defaultSpaces: null,
        defaultCategory: null,
        defaultStartDate: null,
        sector: null
      }
    }
  </script>