<template>
  <div>
    <v-card class="elevation-0 p-3">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <occupation-create-edit
              v-on:success="listOccupations(); snackbarMessage = $event; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              :auth-user="authUser"
            ></occupation-create-edit>
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
        :items="occupations"
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
              <td class="text-center" :colspan="headers.length">Buscando ocupações...</td>
            </tr>
            <tr v-else-if="!items.length">
              <td class="text-center" :colspan="headers.length">Nenhuma ocupação encontrado(a)</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td v-if="authUser.role.tag === 'administrator'">{{ item.sector ? item.sector.name : "Nenhum" }}</td>
              <td>{{ item.name }}</td>

              <td>
                <occupation-create-edit
                  v-on:success="listOccupations(); snackbarMessage = $event; snackbarVisible = true;"
                  v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                  :auth-user="authUser"
                  :occupation="item"
                ></occupation-create-edit>

                <v-btn
                  @click="removeOccupation(item)"
                  class="elevation-0"
                  color="primary"
                  title="Remover Ocupação"
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
        <v-pagination next-icon="fas fa-caret-right" prev-icon="fas fa-caret-left" v-model="page" :length="pageCount" total-visible="10"></v-pagination>
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
      occupations: []
    }),
    mounted() {
      this.listOccupations();
    },
    props: {
      authUser: {}
    },
    methods: {
      listOccupations() {
        this.loading   = true;
        this.occupations = [];

        axios.get(`/api/occupation`, {})
          .then(response => this.occupations = response.data.data)
          .catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          })
          .finally(() => this.loading = false)
        ;
      },
      async removeOccupation(occupation) {
        const confirm = await this.$confirm(`Deseja remover a ocupação ${occupation.name}?`);

        if (!confirm) return;

        this.overlay = true;

        axios.delete(`/api/occupation/${occupation.id}`, {}).then(response => {
          this.snackbarMessage = response.data.message;
          this.snackbarVisible = true;
        }).catch(error => {
          this.snackbarMessage = error.response.data.message;
          this.snackbarVisible = true;
        }).finally(() => {
          this.overlay = false;

          this.listOccupations();
        });
      }
    },
    computed: {
      headers() {
        let headers = [];

        if (this.authUser.role.tag === 'administrator') headers.push({ text: "Equipamento Cultural", value: "sector.name" });
          
        headers = headers.concat([
          { text: "Nome", value: "name" },
          { text: "", value: "action", sortable: false }
        ]);

        return headers;
      }
    }
  }
</script>