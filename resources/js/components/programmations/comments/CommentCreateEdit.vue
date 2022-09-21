<template>
  <div class="d-inline">
    <v-dialog
      v-model="dialog"
      width="700"
      :persistent="loading"
      eager
    >
      <template v-slot:activator="{ on, attrs }">
        <v-btn v-if="!comment" text small color="primary" v-bind="attrs" v-on="on">
          <v-icon x-small class="mr-1">fas fa-comment-alt</v-icon>
          Adicionar Comentário
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
              <label for="name">Texto do Comentário <span class="text-danger">*</span></label>
              <div class="input-group">
                <textarea v-model="text" maxlength="255" rows="3" class="form-control no-resize border-0" type="text" placeholder="Digite aqui seu comentário"></textarea>
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
            @click="saveComment()"
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
      saveComment() {
        if (!this.text) {
          this.$emit("error", "Preencha todos os campos");

          return;
        }

        this.loading = true;

        axios({
          method: this.comment ? 'put' : 'post',
          url: `/api/programmation/${this.programmation.id}/comment${this.comment ? `/${this.comment.id}` : ''}`,
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
        if (!this.comment) return;

        this.text = this.comment.comment;
      }
    },
    props: {
      programmation: '',
      comment: {}
    }
  }
</script>