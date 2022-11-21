<template>
  <div>
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <custom-holiday-create-edit
              v-on:success="listCustomHolidays(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
            ></custom-holiday-create-edit>
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
        :items="customHolidays"
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
              <td class="text-center" :colspan="headers.length">Buscando datas comemorativas...</td>
            </tr>
            <tr v-else-if="!items.length">
              <td class="text-center" :colspan="headers.length">Nenhuma data comemorativa encontrada</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.start_at | date('DD/MM/YYYY') }}</td>
              <td>{{ item.end_at | date('DD/MM/YYYY') }}</td>

              <td>
                <custom-holiday-create-edit
                  v-on:success="listCustomHolidays(); snackbarMessage = $event; snackbarVisible = true;"
                  v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                  :custom-holiday="item"
                ></custom-holiday-create-edit>

                <v-btn
                  @click="removeCustomHoliday(item)"
                  class="elevation-0"
                  color="primary"
                  title="Remover Data Comemorativa"
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
      customHolidays: []
    }),
    mounted() {
      this.listCustomHolidays();
    },
    methods: {
      listCustomHolidays() {
        this.loading   = true;
        this.customHolidays = [];

        axios.get(`/api/custom-holiday`, {})
          .then(response => this.customHolidays = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.loading = false)
        ;
      },
      async removeCustomHoliday(customHoliday) {
        const confirm = await this.$confirm(`Deseja remover a data comemorativa ${customHoliday.name}?`);

        if (!confirm) return;

        this.overlay = true;

        axios.delete(`/api/custom-holiday/${customHoliday.id}`, {}).then(response => {
          this.snackbarMessage = response.data.message;
          this.snackbarVisible = true;
        }).catch(error => {
          this.snackbarMessage = error.response.data.message;
          this.snackbarVisible = true;
        }).finally(() => {
          this.overlay = false;

          this.listCustomHolidays();
        });
      }
    },
    computed: {
      headers() {
        let headers = [
          { text: "Nome", value: "name" },
          { text: "Inicia em", value: "start_at" },
          { text: "Termina em", value: "end_at" },
          { text: "", value: "action", sortable: false }
        ];

        return headers;
      }
    }
  }
</script>