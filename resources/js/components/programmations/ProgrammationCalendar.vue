<template>
  <div id="programmation-calendar-component">
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <programmation-create-edit
              v-on:success="listProgrammations(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              :default-space="space"
              :default-category="category"
            ></programmation-create-edit>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-4">
          <label>Espaços</label>
          <v-autocomplete
            v-model="space"
            :items="spacesList"
            :loading="spacesLoading"
            item-text="name"
            item-value="id"
            label="Lista de Espaços"
            no-data-text="Nenhum espaço encontrado"
            hide-details
            solo
          ></v-autocomplete>
        </div>

        <div class="col-md-4">
          <label>Categorias</label>
          <v-autocomplete
            v-model="category"
            :items="categoriesList"
            :loading="categoriesLoading"
            item-text="name"
            item-value="id"
            label="Lista de Categorias"
            no-data-text="Nenhuma categoria encontrada"
            hide-details
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
              {{ dateFilter | date(section === 'calendar' ? 'MMM YYYY' : 'MMM DD [em diante]') | captalize }}
              <v-icon small class="ml-2">fas fa-chevron-down</v-icon>
            </h2>
          </template>

          <v-date-picker
            v-model="dateFilter"
            color="primary"
            no-title
            @input="dateMenu = false"
          ></v-date-picker>
        </v-menu>
      </v-toolbar>

      <v-card-text>
        <v-tabs-items v-model="section">
          <v-tab-item transition="fade-transition" value="calendar">

          </v-tab-item>
          <v-tab-item transition="fade-transition" value="list">
            <h5 class="text-dark time-divider">
              <time>{{ dateFilter | date('MMMM YYYY') | captalize }}</time>
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
      dateFilter: moment().format('YYYY-MM-DD'),
      space: null,
      spacesLoading: true,
      spacesList: [],
      category: null,
      dateMenu: false,
      categoriesLoading: true,
      categoriesList: []
    }),
    mounted() {
      this.listSpaces();
      this.listCategories();
      this.listProgrammations();
    },
    methods: {
      resetDate() {
        this.dateFilter = moment().format('YYYY-MM-DD');
      },
      modifyMonth(value) {
        let date = moment(this.dateFilter, 'YYYY-MM-DD');

        if (value < 0) date = date.subtract(value * -1, 'months');
        else date = date.add(value, 'months');

        this.dateFilter = date.format('YYYY-MM-DD');
      },
      listProgrammations() {

      },
      listSpaces() {
        this.spacesLoading = true;
        this.spacesList    = [];

        axios.get(`/api/space`, {})
          .then(response => {
            this.spacesList.push({id: null, name: "Todos"});

            response.data.data.forEach(space => this.spacesList.push({id: space.id, name: space.name}));
          })
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
          .then(response => {
            this.categoriesList.push({id: null, name: "Todas", color: null});

            response.data.data.forEach(category => this.categoriesList.push({id: category.id, name: category.name, color: category.color}));
          })
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