<template>
  <div>
    <v-card class="elevation-0 p-3">
      <div class="row mb-3">
        <div class="col-lg-4 offset-lg-8">
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
        <div class="col-lg-12">
          <h4 class="total-title">Total de Logs: {{ logs.length }}</h4>
        </div>
      </div>

      <v-data-table
        :headers="headers"
        :items="logs"
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
              <td class="text-center" :colspan="headers.length">Buscando logs...</td>
            </tr>
            <tr v-else-if="!items.length">
              <td class="text-center" :colspan="headers.length">Nenhum log encontrado</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td v-if="authUser.role.tag === 'administrator'">{{ item.sector ? item.sector : "Nenhum" }}</td>
              <td>{{ item.user }}</td>
              <td>{{ item.action }}</td>
              <td>{{ item.created_at_formatted }}</td>
            </tr>
          </tbody>
        </template>
      </v-data-table>

      <div class="text-center pt-2" v-if="pageCount > 1">
        <v-pagination next-icon="fas fa-caret-right" prev-icon="fas fa-caret-left" v-model="page" :length="pageCount" total-visible="10"></v-pagination>
      </div>
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
      search: "",
      logs: []
    }),
    mounted() {
      this.listLogs();
    },
    props: {
      authUser: {}
    },
    methods: {
      listLogs() {
        this.loading = true;
        this.logs = [];

        axios.get(`/api/log`, {})
          .then(response => this.logs = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.loading = false)
        ;
      }
    },
    computed: {
      headers() {
        let headers = [];

        if (this.authUser.role.tag === 'administrator') headers.push({ text: "Equipamento Cultural", value: "sector" });

        headers = headers.concat([
          { text: "Usuário", value: "user" },
          { text: "Ação", value: "action" },
          { text: "Data", value: "created_at_formatted" }
        ]);

        return headers;
      }
    }
  }
</script>