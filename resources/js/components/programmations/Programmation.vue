<template>
  <div id="programmation-component">
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <programmation-create-edit
              v-if="authUser.role.tag !== 'user'"
              ref="programmationCreate"
              v-on:success="listProgrammations(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              :default-spaces="spaces"
              :default-category="categories.length >= 1 ? categories[0] : null"
              :schedule="schedule"
              :auth-user="authUser"
            ></programmation-create-edit>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-3">
          <label>Agenda</label>
          <v-autocomplete
            v-model="schedule"
            :items="schedulesList"
            :loading="schedulesLoading"
            item-text="name"
            item-value="id"
            label="Selecione uma Agenda"
            no-data-text="Nenhuma agenda encontrada"
            return-object
            hide-details
            solo
          ></v-autocomplete>
        </div>

        <div class="col-md-3">
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

        <div class="col-md-3">
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

        <div class="col-md-3">
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

      <div class="row mb-3">
        <div class="col-md-12">
          <v-tabs v-model="section" right hide-slider>
            <v-tab color="red" href="#calendar">Calendário de Programações</v-tab>
            <v-tab href="#list">Lista de Programações</v-tab>
            <v-tab href="#per-day">Programações por Dia</v-tab>
            <v-tab href="#day">Programações do Dia</v-tab>
          </v-tabs>
        </div>
      </div>

      <v-toolbar class="elevation-0">
        <template v-if="section !== 'day'">
          <v-btn icon class="mr-1" @click="modifyMonth(-1)">
            <v-icon small>fas fa-chevron-left</v-icon>
          </v-btn>
  
          <v-btn icon class="mr-1" @click="modifyMonth(1)">
            <v-icon small>fas fa-chevron-right</v-icon>
          </v-btn>
  
          <v-btn rounded small class="elevation-0 mr-4" color="primary" @click="resetDate()">
            Hoje
          </v-btn>
        </template>

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
              <template v-if="section !== 'day'">
                {{ date | date(section === 'calendar' ? 'MMM YYYY' : (section === 'per-day' ? 'MMM DD' : 'MMM DD [em diante]')) | captalize }}
                <v-icon small class="ml-2">fas fa-chevron-down</v-icon>
              </template>

              <span v-else>Hoje</span>
            </h2>
          </template>

          <v-date-picker
            v-if="section !== 'day'"
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

            <programmation-caption :categories="categoriesList" :spaces="spacesList"></programmation-caption>

            <programmation-calendar 
              v-on:select="addProgrammation" 
              v-on:success="listProgrammations(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              :auth-user="authUser"
              :programmations="programmations" 
              :date="date"
              :schedule="schedule"
              :holidays="holidays"
            ></programmation-calendar>

            <programmation-caption :categories="categoriesList" :spaces="spacesList"></programmation-caption>
          </v-tab-item>
          <v-tab-item transition="fade-transition" value="list">
            <programmation-list :programmations="programmations"></programmation-list>
          </v-tab-item>
          <v-tab-item transition="fade-transition" value="per-day">
            <programmation-list :programmations="programmations"></programmation-list>
          </v-tab-item>
          <v-tab-item transition="fade-transition" value="day">
            <programmation-list :programmations="programmations"></programmation-list>
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
  import Holidays from 'date-holidays';

  export default {
    data: () => ({
      snackbarVisible: false,
      snackbarMessage: "",
      search: "",
      dateHolidays: new Holidays('BR', 'ce'),
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
      schedule: null,
      schedulesList: [],
      schedulesLoading: true,
      holidaysList: []
    }),
    mounted() {
      this.listSchedules();
      this.listSpaces();
      this.listCategories();
      this.listHolidays();
    },
    props: {
      authUser: {}
    },
    watch: {
      schedule() {
        this.listProgrammations();
      },
      date() {
        this.listProgrammations();
        this.listHolidays();
      },
      section() {
        if (this.section === 'day') this.resetDate();

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
      },
      holidays() {
        return this.holidaysList;
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

        component.schedule = this.schedule;
        component.authUser = this.authUser;

        if (!$event.allDay) {
          component.startTime = startDate.format('hh:mm');
          component.endTime   = endDate.format('hh:mm');
        }

        component.endDate   = endDate.format('DD/MM/YYYY');
        component.startDate = startDate.format('DD/MM/YYYY');
        component.dialog    = true;
      },
      listSchedules() {
        this.schedulesLoading = true;
        this.schedulesList    = [];

        axios.get(`/api/schedule`, {})
          .then(response => {
            this.schedulesList = response.data.data;

            if (this.schedulesList.length) this.schedule = this.schedulesList[0];
          })
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.schedulesLoading = false)
        ;
      },
      listHolidays() {
        this.holidaysList = [];

        axios.get(`/api/custom-holiday`, {})
          .then(response => {
            let year = moment(this.date).format('Y');

            this.dateHolidays.getHolidays(year).forEach(holiday => {
              this.holidaysList.push({
                name: holiday.name,
                start_at: holiday.start,
                end_end: holiday.end,
                custom: false
              });
            });

            response.data.data.forEach(holiday => {
              this.holidaysList.push({
                name: holiday.name,
                start_at: `${year}-${holiday.start_at}`,
                end_at: `${year}-${holiday.end_at}`,
                custom: true
              });
            });
          })
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
        ;
      },
      listProgrammations() {
        if (!this.schedule) {
          this.snackbarMessage = "Selecione uma agenda para listar as programações";
          this.snackbarVisible = true;

          return;
        }

        this.programmationsList = [];

        axios.get(`/api/programmation`, {
          params: { date: this.date, type: this.section, schedule: this.schedule.id }
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