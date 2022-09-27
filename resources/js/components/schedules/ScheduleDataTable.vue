<template>
    <div>
      <v-card class="elevation-0 p-3">
        <div class="row">
          <div class="col-md-12">
            <div class="text-right">
              <schedule-create-edit
                v-on:success="listSchedules(); snackbarMessage = $event; snackbarVisible = true;"
                v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              ></schedule-create-edit>
            </div>
          </div>
        </div>
  
        <div class="row mb-3">
          <div class="col-lg-4 offset-8">
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
  
        <v-data-table
          :headers="headers"
          :items="schedules"
          :loading="loading"
          :page.sync="page"
          :search="search"
          :items-per-page="10"
          @page-count="pageCount = $event"
          hide-default-footer
        >
          <template v-slot:body="{ items }">
            <tbody>
              <tr v-if="loading">
                <td class="text-center" :colspan="headers.length">Buscando agendas...</td>
              </tr>
              <tr v-else-if="!items.length">
                <td class="text-center" :colspan="headers.length">Nenhuma agenda encontrada</td>
              </tr>
              <tr v-else v-for="item in items" :key="item.id">
                <td>{{ item.name }}</td>
                <td>{{ item.private ? "Sim" : "Não" }}</td>
  
                <td v-if="authUser.id === item.user_id || authUser.role.tag === 'administrator'">
                  <schedule-create-edit
                    v-on:success="listSchedules(); snackbarMessage = $event; snackbarVisible = true;"
                    v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                    :schedule="item"
                  ></schedule-create-edit>
  
                  <v-btn
                    @click="toggleScheduleStatus(item)"
                    :title="`${item.active ? 'Desprivatizar' : 'Privatizar'} Agenda`"
                    class="elevation-0 mr-1"
                    color="primary"
                    fab
                    small
                  >
                    <v-icon small>{{ item.active ? 'fa-eye-slash' : 'fa-eye' }}</v-icon>
                  </v-btn>
  
                  <v-btn
                    @click="removeSchedule(item)"
                    class="elevation-0"
                    color="primary"
                    title="Remover Agenda"
                    fab 
                    small
                  >
                    <v-icon small>fa-trash</v-icon>
                  </v-btn>
                </td>
              </tr>
            </tbody>
          </template>
        </v-data-table>
  
        <div class="text-center pt-2" v-if="pageCount > 1">
          <v-pagination v-model="page" :length="pageCount" total-visible="10"></v-pagination>
        </div>
  
        <v-overlay :value="overlay">
          <v-progress-circular indeterminate size="64"></v-progress-circular>
        </v-overlay>
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
        snackbarMessage: "",
        snackbarVisible: false,
        page: 1,
        pageCount: 0,
        loading: true,
        overlay: false,
        search: "",
        schedules: []
      }),
      mounted() {
        this.listSchedules();
      },
      props: {
        authUser: {}
      },
      methods: {
        listSchedules() {
          this.loading   = true;
          this.schedules = [];

          axios.get(`/api/schedule`, {})
            .then(response => this.schedules = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.loading = false)
          ;
        },
        async toggleScheduleStatus(schedule) {
          const confirm = await this.$confirm(`Deseja ${schedule.private ? 'desprivatizar' : 'privatizar'} a agenda ${schedule.name}?`);

          if (!confirm) return;

          this.overlay = true;

          axios.put(`/api/schedule/${schedule.id}/toggle-status`, {}).then(response => {
            this.snackbarMessage = response.data.message;
            this.snackbarVisible = true;
          }).catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          }).finally(() => {
            this.overlay = false;

            this.listSchedules();
          });
        },
        async removeSchedule(schedule) {
          const confirm = await this.$confirm(`Deseja remover a agenda ${schedule.name}?`);

          if (!confirm) return;

          this.overlay = true;

          axios.delete(`/api/schedule/${schedule.id}`, {}).then(response => {
            this.snackbarMessage = response.data.message;
            this.snackbarVisible = true;
          }).catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          }).finally(() => {
            this.overlay = false;

            this.listSchedules();
          });
        }
      },
      computed: {
        headers() {
          let headers = [
            { text: "Nome", value: "name" },
            { text: "Privado", value: "private" },
            { text: "", value: "action", sortable: false }
          ];
  
          return headers;
        }
      }
    }
  </script>