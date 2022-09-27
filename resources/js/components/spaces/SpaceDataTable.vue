<template>
  <div>
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <space-create-edit
              v-on:success="listSpaces(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
            ></space-create-edit>
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
        :items="spaces"
        :loading="loading"
        :page.sync="page"
        :search="search"
        :items-per-page="6"
        @page-count="pageCount = $event"
        hide-default-footer
      >
        <template v-slot:body="{ items }">
          <tbody>
            <tr v-if="loading">
              <td class="text-center" :colspan="headers.length">Buscando espaços...</td>
            </tr>
            <tr v-else-if="!items.length">
              <td class="text-center" :colspan="headers.length">Nenhum espaço encontrado</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td>
                <img :src="item.icon_url" :alt="`${item.name} Icon`" width="40px" height="40px">
              </td>
              <td>{{ item.name }}</td>
              <td>{{ item.active ? "Sim" : "Não" }}</td>

              <td>
                <space-create-edit
                  v-on:success="listSpaces(); snackbarMessage = $event; snackbarVisible = true;"
                  v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                  :space="item"
                ></space-create-edit>

                <v-btn
                  @click="toggleSpaceActivation(item)"
                  :title="`${item.active ? 'Desativar' : 'Ativar'} Espaço`"
                  class="elevation-1"
                  color="primary"
                  fab 
                  small
                >
                  <v-icon small>{{ item.active ? 'fa-eye-slash' : 'fa-eye' }}</v-icon>
                </v-btn>

                <v-btn
                  @click="removeSpace(item)"
                  class="elevation-1"
                  color="primary"
                  :title="`Remover Espaço`"
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
      spaces: []
    }),
    mounted() {
      this.listSpaces();
    },
    methods: {
      listSpaces() {
          this.loading = true;
          this.spaces  = [];

          axios.get(`/api/space`, {})
            .then(response => this.spaces = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.loading = false)
          ;
        },
        async toggleSpaceActivation(space) {
          const confirm = await this.$confirm(`Deseja ${space.active ? 'desativar' : 'ativar'} o espaço ${space.name}?`);

          if (!confirm) return;

          this.overlay = true;

          axios.put(`/api/space/${space.id}/toggle-activation`, {}).then(response => {
            this.snackbarMessage = response.data.message;
            this.snackbarVisible = true;
          }).catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          }).finally(() => {
            this.overlay = false;

            this.listSpaces();
          });
        },
        async removeSpace(space) {
          const confirm = await this.$confirm(`Deseja remover o espaço ${space.name}?`);

          if (!confirm) return;

          this.overlay = true;

          axios.delete(`/api/space/${space.id}`, {}).then(response => {
            this.snackbarMessage = response.data.message;
            this.snackbarVisible = true;
          }).catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          }).finally(() => {
            this.overlay = false;

            this.listSpaces();
          });
        }
    },
    computed: {
      headers() {
        let headers = [
          { text: "Ícone", value: "icon_url", sortable: false },
          { text: "Nome", value: "name" },
          { text: "Ativo(a)", value: "active" },
          { text: "", value: "action", sortable: false }
        ];

        return headers;
      }
    }
  }
</script>