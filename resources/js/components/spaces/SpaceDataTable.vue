<template>
  <div>
    <v-card class="elevation-0">
      <div class="row">
        <div class="col-md-12">
          <div class="text-right">
            <space-create-edit
              v-on:success="listSpaces(); snackbarMessage = $event.message; snackbarVisible = true;"
              v-on:error="snackbarMessage = $event.message; snackbarVisible = true;"
            ></space-create-edit>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-4 offset-8">
          <label>
            <i class="fas fa-search"></i>
            Pesquisar
          </label>
          <input v-model="search" type="text" class="form-control" placeholder="Digite aqui">
        </div>
      </div>

      <div class="row">
        <div class="col-md-12">
          <h5>Total de Espaços: <span>{{ spaces.length }}</span></h5>
        </div>
      </div>

      <hr>

      <v-data-table
        :headers="headers"
        :items="spaces"
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
              <td class="text-center" :colspan="headers.length">Buscando espaços...</td>
            </tr>
            <tr v-else-if="!items.length">
              <td class="text-center" :colspan="headers.length">Nenhum(a) espaços encontrado(a)</td>
            </tr>
            <tr v-else v-for="item in items" :key="item.id">
              <td>{{ item.name }}</td>
              <td>{{ item.active ? "Sim" : "Não" }}</td>

              <td>
                <space-create-edit
                  v-on:success="listSpaces(); snackbarMessage = $event.message; snackbarVisible = true;"
                  v-on:error="snackbarMessage = $event.message; snackbarVisible = true;"
                  :space="item"
                ></space-create-edit>

                <v-btn
                  @click="toggleSpaceActivation(item)"
                  :title="`${item.active ? 'Desativar' : 'Ativar'} Espaço`"
                  class="elevation-1"
                  color="primary"
                  fab 
                  x-small
                >
                  <v-icon x-small>{{ item.active ? 'fa-eye-slash' : 'fa-eye' }}</v-icon>
                </v-btn>

                <v-btn
                  @click="removeSpace(item)"
                  class="elevation-1"
                  color="primary"
                  :title="`Remover Espaço`"
                  fab 
                  x-small
                >
                  <v-icon x-small>fa-trash</v-icon>
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

      }
    },
    computed: {
      headers() {
        let headers = [
          { text: "Nome", value: "name" },
          { text: "Ativo(a)", value: "active" },
          { text: "Ações", value: "action", sortable: false }
        ];

        return headers;
      }
    },
    props: {
      authUser: {}
    }
  }
</script>