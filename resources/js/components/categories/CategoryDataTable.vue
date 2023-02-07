<template>
    <div>
      <v-card class="elevation-0 p-3">
        <div class="row">
          <div class="col-md-12">
            <div class="text-right">
              <category-create-edit
                v-on:success="listCategories(); snackbarMessage = $event; snackbarVisible = true;"
                v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                :auth-user="authUser"
              ></category-create-edit>
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
          :items="categories"
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
                <td class="text-center" :colspan="headers.length">Buscando categorias...</td>
              </tr>
              <tr v-else-if="!items.length">
                <td class="text-center" :colspan="headers.length">Nenhuma categoria encontrada</td>
              </tr>
              <tr v-else v-for="item in items" :key="item.id">
                <td v-if="authUser.role.tag === 'administrator'">{{ item.sector ? item.sector.name : "Nenhum" }}</td>
                <td>{{ item.axis ? item.axis.name : 'Nenhum' }}</td>
                <td>{{ item.name }}</td>
                <td>
                  <div class="color-preview" v-bind:style="{backgroundColor: item.color}"></div>
                </td>
  
                <td>
                  <category-create-edit
                    v-on:success="listCategories(); snackbarMessage = $event; snackbarVisible = true;"
                    v-on:error="snackbarMessage = $event; snackbarVisible = true;"
                    :auth-user="authUser"
                    :category="item"
                  ></category-create-edit>
  
                  <v-btn
                    @click="removeCategory(item)"
                    class="elevation-1"
                    color="primary"
                    :title="`Remover Categoria`"
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
        categories: []
      }),
      mounted() {
        this.listCategories();
      },
      methods: {
        listCategories() {
            this.loading = true;
            this.categories  = [];
  
            axios.get(`/api/category`, {})
              .then(response => this.categories = response.data.data)
              .catch(error => {
                this.snackbarMessage = error.response.data.message;
                this.snackbarVisible = true;
              })
              .finally(() => this.loading = false)
            ;
          },
          async removeCategory(category) {
            const confirm = await this.$confirm(`Deseja remover a categoria ${category.name}?`);
  
            if (!confirm) return;
  
            this.overlay = true;
  
            axios.delete(`/api/category/${category.id}`, {}).then(response => {
              this.snackbarMessage = response.data.message;
              this.snackbarVisible = true;
            }).catch(error => {
              this.snackbarMessage = error.response.data.message;
              this.snackbarVisible = true;
            }).finally(() => {
              this.overlay = false;
  
              this.listCategories();
            });
          }
      },
      props: {
        authUser: {}
      },
      computed: {
        headers() {
          let headers = [];

          if (this.authUser.role.tag === 'administrator') headers.push({ text: "Equipamento Cultural", value: "sector.name" });
            
          headers = headers.concat([
            { text: "Eixo Estratégico", value: "axis.name" },
            { text: "Nome", value: "name" },
            { text: "Cor", value: "color", sortable: false },
            { text: "", value: "action", sortable: false }
          ]);
  
          return headers;
        }
      }
    }
  </script>