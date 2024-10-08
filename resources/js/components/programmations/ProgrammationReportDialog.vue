<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      :persistent="overlay"
      width="600"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-bind="attrs" v-on="on" color="primary" class="elevation-0" >
          <v-icon class="mr-1" small>fas fa-file</v-icon>
          Gerar Relatório
        </v-btn>
      </template>

      <v-card class="px-3 pb-6 pt-2">
        <v-card-title
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
              <label for="function">Equipamento Cultural</label>
              <v-autocomplete
                v-model="sector"
                :items="sectorsList"
                :loading="sectorsLoading"
                :readonly="authUser.role.tag !== 'administrator'"
                item-text="name"
                item-value="id"
                label="Equipamento Cultural"
                no-data-text="Nenhum equipamento encontrado"
                hide-details
                solo
              ></v-autocomplete>
            </div>
          </div>

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

          <div class="row align-items-center">
            <div class="col-md-8">
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

            <div class="col-md-4">
              <v-switch
                v-model="spacesNegative"
                label="Negativar filtro"
                :disabled="!spaces.length"
                hide-details
              ></v-switch>
            </div>
          </div>

          <div class="row align-items-center">
            <div class="col-md-8">
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

            <div class="col-md-4">
              <v-switch
                v-model="categoriesNegative"
                label="Negativar filtro"
                :disabled="!categories.length"
                hide-details
              ></v-switch>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="function">Solicitações</label>
              <v-select
                v-model="solicitation"
                :items="solicitationOptions"
                item-text="name"
                item-value="id"
                label="Solicitações"
                hide-details
                solo
              ></v-select>
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
      sectorsLoading: true,
      sectorsList: [],
      schedulesLoading: true,
      schedulesList: [],
      spacesLoading: true,
      spacesList: [],
      spacesNegative: false,
      catogoriesLoading: true,
      categoriesList: [],
      categoriesNegative: false,
      solicitation: 3,
      schedule: null,
      sector: null,
      spaces: [],
      categories: [],
      date: [moment().format('YYYY-MM')],
      solicitationOptions: [
        {id: 1, name: 'Incluir solicitações'},
        {id: 2, name: 'Somente solicitações'},
        {id: 3, name: 'Não incluir solicitações'},
      ]
    }),
    props: {
      defaultSector: null,
      defaultSchedule: null,
      defaultSpaces: [],
      defaultCategories: [],
      authUser: {}
    },
    watch: {
      dialog() {
        if (!this.dialog) return;

        this.listSectors();
      },
      sector() {
        this.listSchedules();
        this.listSpaces();
        this.listCategories();
      }
    },
    methods: {
      listSectors() {
        this.sectorsLoading = true;
        this.sectorsList    = [];

        axios.get(`/api/sector`)
          .then(response => {
            this.sectorsList = response.data.data;

            if (this.defaultSector) this.sector = this.defaultSector;
            else if (this.authUser.sector) this.sector = this.authUser.sector.id;
            else if (this.sectorsList.length) this.sector = this.sectorsList[0].id;
          })
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.sectorsLoading = false)
        ;
      },
      listSchedules() {
        this.schedulesLoading = true;
        this.schedulesList    = [];

        axios.get(`/api/schedule${this.sector ? `/${this.sector}` : ''}`)
          .then(response => {
            this.schedulesList = response.data.data;

            if (this.defaultSchedule) this.schedule = this.defaultSchedule;
            else if (this.schedulesList.length) this.schedule = this.schedulesList[0].id;
          })
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

        axios.get(`/api/space${this.sector ? `/${this.sector}` : ''}`)
          .then(response => {
            this.spacesList = response.data.data;

            if (this.defaultSpaces) this.spaces = this.defaultSpaces;
          })
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

        axios.get(`/api/category${this.sector ? `/${this.sector}` : ''}`)
          .then(response => {
            this.categoriesList = response.data.data;

            if (this.defaultCategories) this.categories = this.defaultCategories;
          })
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
        if (this.spacesNegative) query += '&negative-spaces=1';
        if (this.categoriesNegative) query += '&negative-categories=1';

        query += `&solicitation=${this.solicitation}`;

        window.open(`/programacao/relatorio/${this.schedule}${query}`, '_blank');
      }
    }
  }
</script>