<template>
  <div id="programmation-component">
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <programmation-report-dialog 
              v-if="authUser.role.tag !== 'user'"
              :default-sector="sector ? sector : null"
              :default-schedule="schedule ? schedule.id : null"
              :default-spaces="spaces"
              :default-categories="categories"
              :auth-user="authUser"
            ></programmation-report-dialog>

            <programmation-create-edit
              v-if="authUser.role.tag !== 'user' && (authUser.role.tag === 'administrator' || (authUser.sector && sector === authUser.sector.id))"
              ref="programmationCreate"
              v-on:success="listProgrammations(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              :default-spaces="spaces"
              :default-category="categories.length >= 1 ? categories[0] : null"
              :schedule="schedule"
              :auth-user="authUser"
              :sector="sector"
            ></programmation-create-edit>
          </div>
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label>Equipamento Cultural</label>
          <v-autocomplete
            v-model="sector"
            :items="sectorsList"
            :loading="sectorsLoading"
            item-text="name"
            item-value="id"
            label="Selecione um Equipamento Cultural"
            no-data-text="Nenhum equipamento cultural encontrado"
            hide-details
            solo
          ></v-autocomplete>
        </div>

        <div class="col-md-6">
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

      <v-divider class="my-6 strong"></v-divider>

      <v-toolbar class="elevation-0" height="50px">
        <template v-if="section !== 'day'">
          <v-btn icon @click="modifyMonth(-1)">
            <v-icon>fas fa-caret-left</v-icon>
          </v-btn>
          <span class="mx-2" style="white-space: nowrap; font-size: 18px; font-weight: 400;">Ver por Mês</span>
          <v-btn icon @click="modifyMonth(1)">
            <v-icon>fas fa-caret-right</v-icon>
          </v-btn>
        </template>

        <v-divider vertical class="mx-8" v-if="section !== 'day'"></v-divider>

        <v-menu
          v-model="dateMenu"
          :close-on-content-click="false"
          transition="scale-transition"
          offset-y
          max-width="290px"
          min-width="auto"
        >
          <template v-slot:activator="{ on, attrs }">
            <h2 class="mb-0 mx-2" style="white-space: nowrap;" text v-on="on" v-bind="attrs">
              <template v-if="section !== 'day'">
                {{ date | date(section === 'calendar' ? 'MMM YYYY' : (section === 'per-day' ? 'MMM DD' : 'MMM DD [em diante]')) | captalize }}
                <v-icon class="ml-2">fas fa-caret-down</v-icon>
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

        <v-divider vertical class="mx-8" v-if="section !== 'day'"></v-divider>

        <v-btn class="elevation-0" color="primary" @click="resetDate()" :disabled="actualDate === date">
          Voltar para Mês de {{ actualDate | date('MMMM') | captalize }}
        </v-btn>

        <v-tabs height="36" class="d-flex align-items-center programmation-tabs" v-model="section" right hide-slider>
          <v-tab href="#calendar">
            <v-icon small class="mr-3">fas fa-calendar-alt</v-icon>
            Calendário de Programações
          </v-tab>
          <v-tab href="#list">Lista de Programações</v-tab>
          <v-tab href="#per-day">Programações por Dia</v-tab>
          <v-tab href="#day">Programações do Dia</v-tab>
        </v-tabs>
      </v-toolbar>

      <v-divider class="my-6 strong"></v-divider>

      <v-card-text class="p-0">
        <v-tabs-items v-model="section">
          <v-tab-item transition="fade-transition" value="calendar">
            <programmation-caption :schedule="schedule" :categories="categoriesList" :spaces="spacesList"></programmation-caption>

            <programmation-calendar 
              v-on:select="addProgrammation" 
              v-on:success="listProgrammations(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              :auth-user="authUser"
              :programmations="programmations" 
              :date="date"
              :schedule="schedule"
              :holidays="holidays"
              :sector="sector"
            ></programmation-calendar>

            <programmation-caption class="mt-5" :schedule="schedule" :categories="categoriesList" :spaces="spacesList"></programmation-caption>
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
      actualDate: moment().format('YYYY-MM-DD'),
      date: moment().format('YYYY-MM-DD'),
      spaces: [],
      spacesLoading: false,
      spacesList: [],
      categories: [],
      dateMenu: false,
      categoriesLoading: false,
      categoriesList: [],
      programmationsList: [],
      sector: null,
      schedule: null,
      schedulesList: [],
      schedulesLoading: false,
      sectorsList: [],
      sectorsLoading: true,
      holidaysList: []
    }),
    mounted() {
      this.listSectors();
    },
    props: {
      authUser: {}
    },
    watch: {
      sector() {
        this.listSchedules();
        this.listSpaces();
        this.listCategories();
        this.listHolidays();
      },
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
      fixHolidayName(name) {
        if (name.toLowerCase() === 'data magna do estado') return "Carta Magna do Ceará";

        return name;
      },
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
      listSectors() {
        this.sectorsLoading = true;
        this.sectorsList    = [];

        axios.get(`/api/sector`, {})
          .then(response => {
            this.sectorsList = response.data.data;

            if (this.authUser.sector) this.sector = this.authUser.sector.id;
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

        axios.get(`/api/schedule${this.sector ? `/${this.sector}` : ''}`, {})
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
        axios.get(`/api/custom-holiday${this.sector ? `/${this.sector}` : ''}`, {})
          .then(response => {
            this.holidaysList = [];

            let year = moment(this.date).format('Y');

            this.dateHolidays.getHolidays(year).forEach(holiday => {
              let endDate = (() => {
                let start = moment(holiday.start);
                let end   = moment(holiday.end);
                let diff  = end.diff(start, 'days');

                if (diff <= 1) return end.format('YYYY-MM-DD');

                return end.add(1, 'days').format('YYYY-MM-DD');
              })();

              this.holidaysList.push({
                id: null,
                name: this.fixHolidayName(holiday.name),
                body: null,
                start_at: holiday.start,
                end_at: endDate,
                custom: false,
                optional: holiday.type === 'observance'
              });
            });

            response.data.data.forEach(holiday => {
              this.holidaysList.push({
                id: holiday.id,
                name: holiday.name,
                body: holiday.body,
                start_at: `${year}-${holiday.start_at}`,
                end_at: moment(`${year}-${holiday.end_at}`).add(1, 'days').format('YYYY-MM-DD'),
                custom: true,
                optional: true
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
    }
  }
</script>