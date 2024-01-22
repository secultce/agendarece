<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      :persistent="overlay"
      width="1200"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" color="primary" class="elevation-0" >
          <v-icon class="mr-1" small>fas fa-calendar-alt</v-icon>
          Solicitar Agendamento
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
              <label for="title">Título <span class="text-danger">*</span></label>
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
              <label for="description">Descrição <span>(Opcional)</span></label>
              <div class="input-group">
                <textarea v-model="description" :readonly="readonly" rows="3" class="form-control no-resize border-0" placeholder="Digite uma breve descrição da Programação"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-4">
              <label>Classificação Indicativa <span class="text-danger">*</span></label>
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

            <div class="col-md-4">
              <label>Ocupação <span>(Opcional)</span></label>
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

            <div class="col-md-4">
              <label>Acessibilidades do Evento <span>(Opcional)</span></label>
              <v-select
                v-model="accessibilities"
                :items="accessibilitiesList"
                :readonly="readonly"
                item-text="name"
                item-value="id"
                label="Lista de Acessibilidades"
                hide-details
                multiple
                clearable
                solo
              ></v-select>

              <template v-for="(errorMessage, index) in errorMessages('occupation')">
                <small :class="`text-danger d-block ${index == 0 ? 'mt-2' : ''}`">{{ errorMessage }}</small>
              </template>
            </div>
          </div>

          <div class="row">
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

          <div class="row">
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

          <div class="row" v-if="!endDate">
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

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            class="elevation-0 mt-3 px-5"
            :disabled="overlay"
            @click="saveSolicitation()"
          >
            Solicitar Agendamento
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
      accessibilitiesList: [
        // {id: 0, name: "Arquitetônica"},
        // {id: 1, name: "Atitudinal"},
        // {id: 2, name: "Metodológica"},
        // {id: 3, name: "Instrumental"},
        // {id: 4, name: "Programática"},
        {id: 5, name: "Libras"},
        // {id: 6, name: "Natural"},
        // {id: 7, name: "Digital"}
      ],
      accessibilities: [],
      title: "",
      description: "",
      parentalRating: 0,
      startTime: "13:00",
      endTime: "20:00",
      startDate: moment().format('DD/MM/YYYY'),
      endDate: "",
      loopDays: [],
      spacesLoading: true,
      categoriesLoading: true,
      occupationsLoading: true,
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
      saveSolicitation() {
        if (this.startDate && this.endDate) {
          let startDate = moment(this.startDate, 'DD/MM/YYYY');
          let endDate   = moment(this.endDate, 'DD/MM/YYYY');

          if (endDate.diff(startDate) < 0) {
            this.$emit("error", "Período do evento inválido.");

            return;
          }
        }

        this.overlay = true;

        axios({
          method: 'post',
          url: `/api/solicitation`,
          data: {
            occupation: this.occupation,
            schedule: this.schedule,
            spaces: this.spaces,
            category: this.category,
            title: this.title,
            description: this.description,
            parental_rating: this.parentalRating,
            start_time: this.startTime,
            end_time: this.endTime,
            start_date: this.startDate ? moment(this.startDate, 'DD/MM/YYYY').format('YYYY-MM-DD') : null,
            end_date: this.endDate ? moment(this.endDate, 'DD/MM/YYYY').format('YYYY-MM-DD') : null,
            loop_days: this.loopDays,
            accessibilities: this.accessibilities,
            remind_at: this.remindAt,
            has_reminder: this.hasReminder
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
        this.listOccupations();

        if (this.defaultSpaces) this.spaces = this.defaultSpaces;
        if (this.defaultCategory) this.category = this.defaultCategory;
      }
    },
    props: {
      schedule: null,
      defaultSpaces: null,
      defaultCategory: null,
      defaultStartDate: null,
      sector: null
    }
  }
</script>