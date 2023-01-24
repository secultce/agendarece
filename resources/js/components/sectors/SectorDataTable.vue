<template>
  <div>
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <sector-create-edit
              v-on:success="listSectors(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
            ></sector-create-edit>
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
        :items="sectors"
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
              <td class="text-center" :colspan="headers.length">Buscando setores...</td>
            </tr>
            <tr v-else-if="!items.length">
              <td class="text-center" :colspan="headers.length">Nenhum setor encontrado(a)</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td>{{ item.user.name }}</td>
              <td>{{ item.name }}</td>
              <td>{{ item.active ? "Sim" : "Não" }}</td>

              <td>
                <sector-create-edit
                  v-on:success="listSectors(); snackbarMessage = $event; snackbarVisible = true;"
                  v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                  :sector="item"
                ></sector-create-edit>

                <v-btn
                  @click="toggleSectorActivation(item)"
                  :title="`${item.active ? 'Desativar' : 'Ativar'} Usuário`"
                  class="elevation-0 mr-1"
                  color="primary"
                  fab
                  small
                >
                  <v-icon small>{{ item.active ? 'fa-eye-slash' : 'fa-eye' }}</v-icon>
                </v-btn>

                <v-btn
                  @click="removeSector(item)"
                  class="elevation-0"
                  color="primary"
                  title="Remover Usuário"
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
      sectors: []
    }),
    mounted() {
      this.listSectors();
    },
    methods: {
      listSectors() {
        this.loading   = true;
        this.sectors = [];

        axios.get(`/api/sector`, {})
          .then(response => {this.sectors = response.data.data; console.log(this.sectors)})
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.loading = false)
        ;
      },
      async toggleSectorActivation(sector) {
        const confirm = await this.$confirm(`Deseja ${sector.active ? 'desativar' : 'ativar'} o setor ${sector.name}?`);

        if (!confirm) return;

        this.overlay = true;

        axios.put(`/api/sector/${sector.id}/toggle-activation`, {}).then(response => {
          this.snackbarMessage = response.data.message;
          this.snackbarVisible = true;
        }).catch(error => {
          this.snackbarMessage = error.response.data.message;
          this.snackbarVisible = true;
        }).finally(() => {
          this.overlay = false;

          this.listSectors();
        });
      },
      async removeSector(sector) {
        const confirm = await this.$confirm(`Deseja remover o setor ${sector.name}?`);

        if (!confirm) return;

        this.overlay = true;

        axios.delete(`/api/sector/${sector.id}`, {}).then(response => {
          this.snackbarMessage = response.data.message;
          this.snackbarVisible = true;
        }).catch(error => {
          this.snackbarMessage = error.response.data.message;
          this.snackbarVisible = true;
        }).finally(() => {
          this.overlay = false;

          this.listSectors();
        });
      }
    },
    computed: {
      headers() {
        let headers = [
          { text: "Responsável", value: "user.name" },
          { text: "Nome", value: "name" },
          { text: "Ativo(a)", value: "active" },
          { text: "", value: "action", sortable: false }
        ];

        return headers;
      }
    }
  }
</script>