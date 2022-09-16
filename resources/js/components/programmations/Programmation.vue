<template>
  <div id="programmation-component">
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <programmation-create-edit
              ref="programmationCreate"
              v-on:success="listProgrammations(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              :default-spaces="spaces"
              :default-category="categories.length >= 1 ? categories[0] : null"
            ></programmation-create-edit>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <label>Espaços</label>
          <v-autocomplete
            v-model="spaces"
            :items="spacesList"
            :loading="spacesLoading"
            item-text="name"
            item-value="id"
            label="Todos Espaços"
            no-data-text="Nenhum espaço encontrado"
            hide-details
            clearable
            multiple
            solo
          ></v-autocomplete>
        </div>

        <div class="col-md-4">
          <label>Categorias</label>
          <v-autocomplete
            v-model="categories"
            :items="categoriesList"
            :loading="categoriesLoading"
            item-text="name"
            item-value="id"
            label="Todas Categorias"
            no-data-text="Nenhuma categoria encontrada"
            hide-details
            multiple
            clearable
            solo
          >
            <template v-slot:selection="data">
              <div class="color-preview ml-3 mr-2" v-bind:style="{backgroundColor: data.item.color}"></div>
              {{ data.item.name }}
            </template>
            
            <template v-slot:item="data">
              <v-list-item-content>
                {{ data.item.name }}
                <div class="color-preview ml-auto" v-bind:style="{backgroundColor: data.item.color}"></div>
              </v-list-item-content>
            </template>
          </v-autocomplete>
        </div>

        <div class="col-md-4">
          <label for="search">Pesquisar</label>
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text">
                <i class="fas fa-search"></i>
              </span>
            </div>
  
            <input v-model="search" name="search" type="text" class="form-control" placeholder="Digite aqui">
          </div>
        </div>
      </div>

      <div class="row justify-content-end mb-3">
        <div class="col-md-6">
          <v-tabs v-model="section" grow hide-slider>
            <v-tab color="red" href="#calendar">Calendário de Programações</v-tab>
            <v-tab href="#list">Lista de Programações</v-tab>
            <v-tab href="#day">Programações do Dia</v-tab>
          </v-tabs>
        </div>
      </div>

      <v-toolbar class="elevation-0">
        <v-btn icon class="mr-1" @click="modifyMonth(-1)">
          <v-icon small>fas fa-chevron-left</v-icon>
        </v-btn>

        <v-btn icon class="mr-1" @click="modifyMonth(1)">
          <v-icon small>fas fa-chevron-right</v-icon>
        </v-btn>

        <v-btn rounded small class="elevation-0 mr-4" color="primary" @click="resetDate()">
          Hoje
        </v-btn>

        <v-menu
          v-model="dateMenu"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <h2 class="mb-0" text v-on="on" v-bind="attrs">
              {{ date | date(section === 'calendar' ? 'MMM YYYY' : 'MMM DD [em diante]') | captalize }}
              <v-icon small class="ml-2">fas fa-chevron-down</v-icon>
            </h2>
          </template>

          <v-date-picker
            v-model="date"
            color="primary"
            no-title
            @input="dateMenu = false"
          ></v-date-picker>
        </v-menu>
      </v-toolbar>

      <v-card-text>
        <v-tabs-items v-model="section">
          <v-tab-item transition="fade-transition" value="calendar">
            <div class="time-divider">
              <v-btn rounded small class="elevation-0 mr-4" color="primary">
                Exibição Mensal
              </v-btn>
            </div>
            <programmation-calendar v-on:select="addProgrammation" :programmations="programmations" :date="date"></programmation-calendar>
          </v-tab-item>
          <v-tab-item transition="fade-transition" value="list">
            <h5 class="text-dark time-divider">
              <time>{{ date | date('MMMM YYYY') | captalize }}</time>
            </h5>
          </v-tab-item>
          <v-tab-item transition="fade-transition" value="day">
  
          </v-tab-item>
        </v-tabs-items>
      </v-card-text>
    </v-card>

    <v-snackbar
      v-model="snackbarVisible"
      :multi-line="true"
      :right="true"
      :timeout="3000"
      :top="true"
    >
      {{ snackbarMessage }}
    </v-snackbar>
  </div>
</template>

<script>
  export default {
    data: () => ({
      snackbarVisible: false,
      snackbarMessage: "",
      search: "",
      section: 'calendar',
      date: moment().format('YYYY-MM-DD'),
      spaces: [],
      spacesLoading: true,
      spacesList: [],
      categories: [],
      dateMenu: false,
      categoriesLoading: true,
      categoriesList: [],
      programmationsList: [],
    }),
    mounted() {
      this.listSpaces();
      this.listCategories();
      this.listProgrammations();
    },
    watch: {
      date() {
        this.listProgrammations();
      }
    },
    computed: {
      programmations() {
        return this.programmationsList
          .filter(programmation => {
            if (!this.search) return true;

            let regex = this.$options.filters.regex(this.search);

            return programmation.title.toLowerCase().match(regex);
          }).filter(programmation => {
            if (!this.spaces.length) return true;

            return this.spaces.findIndex(space => programmation.spaces.findIndex(item => item.space_id === space) !== -1) !== -1;
          }).filter(programmation => {
            if (!this.categories.length) return true;

            return this.categories.findIndex(category => programmation.category_id === category) !== -1;
          })
        ;
      }
    },
    methods: {
      resetDate() {
        this.date = moment().format('YYYY-MM-DD');
      },
      modifyMonth(value) {
        let date = moment(this.date, 'YYYY-MM-DD');

        if (value < 0) date = date.subtract(value * -1, 'months');
        else date = date.add(value, 'months');

        this.date = date.format('YYYY-MM-DD');
      },
      addProgrammation($event) {
        let component = this.$refs.programmationCreate;
        let startDate = moment($event.start);
        let endDate   = moment($event.end).subtract(1, 'days');

        if (!$event.allDay) {
          component.startTime = startDate.format('hh:mm');
          component.endTime   = endDate.format('hh:mm');
        }

        component.endDate   = endDate.format('DD/MM/YYYY');
        component.startDate = startDate.format('DD/MM/YYYY');
        component.dialog    = true;
      },
      listProgrammations() {
        this.programmationsList = [];

        axios.get(`/api/programmation`, {
          params: { date: this.date, type: this.section }
        })
          .then(response => this.programmationsList = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
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
      },
    }
  }
</script>