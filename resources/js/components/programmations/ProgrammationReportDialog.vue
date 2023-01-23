<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      :persistent="overlay"
      width="500"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
          <v-icon class="mr-1" small>fas fa-file</v-icon>
          Gerar Relatório
        </v-btn>
      </template>

      <v-card class="px-3 pb-6 pt-2">
        <v-card-title
          class="headline"
          primary-title
        >
          Relatório de Programações
          <v-spacer></v-spacer>
          <v-btn icon :disabled="overlay" @click="dialog = false">
            <v-icon>fas fa-times</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text>
          <div class="row">
            <div class="col-md-12">
              <label for="function">Agenda</label>
              <v-autocomplete
                v-model="schedule"
                :items="schedulesList"
                :loading="schedulesLoading"
                item-text="name"
                item-value="id"
                label="Agenda"
                no-data-text="Nenhuma agenda encontrada"
                hide-details
                solo
              ></v-autocomplete>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="function">Espaço</label>
              <v-autocomplete
                v-model="spaces"
                :items="spacesList"
                :loading="spacesLoading"
                item-text="name"
                item-value="id"
                label="Espaços"
                no-data-text="Nenhum espaço encontrado"
                multiple
                clearable
                hide-details
                solo
              ></v-autocomplete>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="function">Categorias</label>
              <v-autocomplete
                v-model="categories"
                :items="categoriesList"
                :loading="catogoriesLoading"
                item-text="name"
                item-value="id"
                label="Categorias"
                no-data-text="Nenhuma categoria encontrada"
                multiple
                clearable
                hide-details
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
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="function">Período</label>
              <v-date-picker
                v-model="date"
                type="month"
                color="primary"
                full-width
                no-title
                range
              ></v-date-picker>
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
            @click="generateReport()"
          >
            Gerar Relatório
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
      schedulesLoading: true,
      schedulesList: [],
      spacesLoading: true,
      spacesList: [],
      catogoriesLoading: true,
      categoriesList: [],
      schedule: null,
      spaces: [],
      categories: [],
      date: [moment().format('YYYY-MM')]
    }),
    props: {
      defaultSchedule: null,
      defaultSpaces: [],
      defaultCategories: []
    },
    watch: {
      dialog() {
        if (!this.dialog) return;

        this.listSchedules();
        this.listSpaces();
        this.listCategories();

        if (this.defaultSchedule) this.schedule = this.defaultSchedule;
        if (this.defaultSpaces) this.spaces = this.defaultSpaces;
        if (this.defaultCategories) this.categories = this.defaultCategories;
      }
    },
    methods: {
      listSchedules() {
        this.schedulesLoading = true;
        this.schedulesList    = [];

        axios.get(`/api/schedule`)
          .then(response => this.schedulesList = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.schedulesLoading = false)
        ;
      },
      listSpaces() {
        this.spacesLoading = true;
        this.spacesList    = [];

        axios.get(`/api/space`)
          .then(response => this.spacesList = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.spacesLoading = false)
        ;
      },
      listCategories() {
        this.catogoriesLoading = true;
        this.categoriesList    = [];

        axios.get(`/api/category`)
          .then(response => this.categoriesList = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.catogoriesLoading = false)
        ;
      },
      generateReport() {
        if (!this.schedule || !this.date) {
          this.snackbarMessage = "Escolha uma agenda e uma data para gerar o relatório";
          this.snackbarVisible = true;

          return;
        }

        let query = "";

        if (typeof this.date === 'string') {
          query = `?date=${this.date}`;
        } else if (typeof this.date === 'object' && this.date.length === 1) {
          query = `?date=${this.date[0]}`;
        } else {
          query = `?date[]=${this.date[0]}&date[]=${this.date[1]}`;
        }

        if (this.spaces.length) this.spaces.forEach(space => query += `&spaces[]=${space}`);
        if (this.categories.length) this.categories.forEach(category => query += `&categories[]=${category}`);

        window.open(`/programacao/relatorio/${this.schedule}${query}`, '_blank');
      }
    }
  }
</script>