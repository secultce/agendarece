<template>
    <div>
      <v-card class="elevation-0 p-3">
        <div class="row">
          <div class="col-md-12">
            <div class="text-right">
              <user-create-edit
                v-on:success="listUsers(); snackbarMessage = $event; snackbarVisible = true;"
                v-on:error="snackbarMessage = $event; snackbarVisible = true;"
              ></user-create-edit>
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
          :items="users"
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
                <td class="text-center" :colspan="headers.length">Buscando usuários...</td>
              </tr>
              <tr v-else-if="!items.length">
                <td class="text-center" :colspan="headers.length">Nenhum usuário encontrado(a)</td>
              </tr>
              <tr v-else v-for="item in items" :key="item.id">
                <td>{{ item.name }}</td>
                <td>{{ item.email }}</td>
                <td>{{ item.role.name }}</td>
                <td>{{ item.active ? "Sim" : "Não" }}</td>
  
                <td>
                  <user-create-edit
                    v-on:success="listUsers(); snackbarMessage = $event; snackbarVisible = true;"
                    v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                    :user="item"
                  ></user-create-edit>
  
                  <v-btn
                    @click="toggleUserActivation(item)"
                    :title="`${item.active ? 'Desativar' : 'Ativar'} Usuário`"
                    class="elevation-0 mr-1"
                    color="primary"
                    fab
                    small
                  >
                    <v-icon small>{{ item.active ? 'fa-eye-slash' : 'fa-eye' }}</v-icon>
                  </v-btn>
  
                  <v-btn
                    @click="removeUser(item)"
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
        users: []
      }),
      mounted() {
        this.listUsers();
      },
      methods: {
        listUsers() {
          this.loading   = true;
          this.users = [];

          axios.get(`/api/user`, {})
            .then(response => this.users = response.data.data)
            .catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            })
            .finally(() => this.loading = false)
          ;
        },
        async toggleUserActivation(user) {
          const confirm = await this.$confirm(`Deseja ${user.active ? 'desativar' : 'ativar'} o usuário ${user.name}?`);

          if (!confirm) return;

          this.overlay = true;

          axios.put(`/api/user/${user.id}/toggle-activation`, {}).then(response => {
            this.snackbarMessage = response.data.message;
            this.snackbarVisible = true;
          }).catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          }).finally(() => {
            this.overlay = false;

            this.listUsers();
          });
        },
        async removeUser(user) {
          const confirm = await this.$confirm(`Deseja remover o usuário ${user.name}?`);

          if (!confirm) return;

          this.overlay = true;

          axios.delete(`/api/user/${user.id}`, {}).then(response => {
            this.snackbarMessage = response.data.message;
            this.snackbarVisible = true;
          }).catch(error => {
            this.snackbarMessage = error.response.data.message;
            this.snackbarVisible = true;
          }).finally(() => {
            this.overlay = false;

            this.listUsers();
          });
        }
      },
      computed: {
        headers() {
          let headers = [
            { text: "Nome", value: "name" },
            { text: "Email", value: "email" },
            { text: "Função", value: "role.name" },
            { text: "Ativo(a)", value: "active" },
            { text: "Ações", value: "action", sortable: false }
          ];
  
          return headers;
        }
      }
    }
  </script>