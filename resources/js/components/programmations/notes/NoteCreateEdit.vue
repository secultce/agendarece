<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="loading"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!note" text small color="primary" v-bind="attrs" v-on="on">
          <v-icon x-small class="mr-1">fas fa-sticky-note</v-icon>
          Adicionar Nota
        </v-btn>
        <v-list-item v-else v-bind="attrs" v-on="on">
          <v-list-item-title>Editar</v-list-item-title>
        </v-list-item>
      </template>

      <v-card class="px-3 pb-6 pt-2">
        <v-card-title
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
              <label for="name">Texto da Nota <span class="text-danger">*</span></label>
              <div class="input-group">
                <textarea v-model="text" maxlength="255" rows="3" class="form-control no-resize border-0" type="text" placeholder="Digite aqui a descrição da nota"></textarea>
              </div>
            </div>
          </div>
        </v-card-text>

        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            color="primary"
            class="elevation-0 mt-3 px-5"
            :loading="loading"
            @click="saveNote()"
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
      text: ""
    }),
    methods: {
      saveNote() {
        if (!this.text) {
          this.$emit("error", "Preencha todos os campos");

          return;
        }

        this.loading = true;

        axios({
          method: this.note ? 'put' : 'post',
          url: `/api/programmation/${this.programmation.id}/note${this.note ? `/${this.note.id}` : ''}`,
          data: {
            text: this.text,
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
        this.text = "";
      }
    },
    watch: {
      dialog() {
        if (!this.note) return;

        this.text = this.note.note;
      }
    },
    props: {
      programmation: '',
      note: {}
    }
  }
</script>