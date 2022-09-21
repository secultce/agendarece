<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="loading"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!link" text small color="primary" v-bind="attrs" v-on="on">
          <v-icon x-small class="mr-1">fas fa-link</v-icon>
          Adicionar Link
        </v-btn>
        <v-list-item v-else v-bind="attrs" v-on="on">
          <v-list-item-title>Editar</v-list-item-title>
        </v-list-item>
      </template>

      <v-card class="px-3 pb-6 pt-2">
        <v-card-title
          class="headline"
          primary-title
        >
          <v-spacer></v-spacer>
          <v-btn icon :disabled="loading" @click="dialog = false">
            <v-icon>fas fa-times</v-icon>
          </v-btn>
        </v-card-title>

        <v-card-text>
          <div class="row">
            <div class="col-md-12">
              <label for="name">Nome <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-tag"></i>
                  </span>
                </div>

                <input v-model="name" class="form-control" type="text" placeholder="Digite o nome">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <label for="url">Url <span class="text-danger">*</span></label>
              <div class="input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text">
                    <i class="fas fa-link"></i>
                  </span>
                </div>

                <input v-model="url" class="form-control" type="url" placeholder="Digite a url">
              </div>
            </div>
          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            class="elevation-0 mt-3"
            large
            rounded
            block
            :loading="loading"
            @click="saveLink()"
          >
            Salvar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>
<script>
  export default {
    data: () => ({
      loading: false,
      dialog: false,
      name: "",
      url: ""
    }),
    methods: {
      saveLink() {
        if (!this.name || !this.url) {
          this.$emit("error", "Preencha todos os campos");

          return;
        }

        this.loading = true;

        axios({
          method: this.link ? 'put' : 'post',
          url: `/api/programmation/${this.programmation.id}/link${this.link ? `/${this.link.id}` : ''}`,
          data: {
            name: this.name,
            url: this.url
          }
        }).then(response => {
          this.$emit("success", response.data.message);
          this.clearCredentials();
        })
        .catch(error => this.$emit("error", error.response.data.message))
        .finally(() => {
          this.loading = false;
          this.dialog = false;
        });
      },
      clearCredentials() {
        this.name = "";
        this.url = "";
      }
    },
    watch: {
      dialog() {
        if (!this.link) return;

        this.name = this.link.name;
        this.url  = this.link.link;
      }
    },
    props: {
      programmation: '',
      link: {}
    }
  }
</script>