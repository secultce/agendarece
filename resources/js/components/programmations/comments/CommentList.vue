<template>
  <v-menu
    v-model="popover"
    :close-on-content-click="false"
    :close-on-click="false"
    persistent
    rounded="lg"
    nudge-width="300"
    max-width="300"
    transition="slide-x-transition"
    offset-y
    left
  >
    <template v-slot:activator="{ on: menu, attrs }">
      <v-tooltip top>
        <template v-slot:activator="{ on: tooltip }">
          <v-btn icon x-small v-bind="attrs" v-on="{...tooltip, ...menu}" :color="color" @click.stop="popover = true">
            <v-icon x-small>fas fa-comment-alt</v-icon>
          </v-btn>
        </template>
        <span>Comentários</span>
      </v-tooltip>
    </template>

    <v-card>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>Lista de Comentários</v-list-item-title>
          </v-list-item-content>

          <v-list-item-action>
            <v-btn small icon @click="popover = false">
              <v-icon small>fas fa-times</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>

        <v-divider class="mt-0"></v-divider>

        <v-card-text v-if="loading">
          <v-progress-circular
            indeterminate
            color="primary"
          ></v-progress-circular>
        </v-card-text>

        <v-card-text v-else-if="!comments.length">
          <v-alert
            type="info"
            class="mb-0"
            dense
            text
          >
            Nenhum comentário encontrado
          </v-alert>
        </v-card-text>

        <template v-else v-for="comment, index of comments">
          <v-list-item :key="comment.id">
            <v-list-item-avatar>
              <img :src="comment.user.avatar_url ?? '/images/default-avatar.jpg'" :alt="comment.user.name">
            </v-list-item-avatar>

            <v-list-item-content>
              <v-list-item-title>{{ comment.user.name }}</v-list-item-title>
              <v-list-item-subtitle>
                <a :href="`mailto:${comment.user.email}`">
                  {{ comment.user.email }}
                </a>
              </v-list-item-subtitle>
            </v-list-item-content>
  
            <v-list-item-action v-if="authUser.id === comment.user.id">
              <v-menu offset-x>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon small v-bind="attrs" v-on="on">
                    <v-icon small>fas fa-ellipsis-v</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <programmation-comment-create-edit 
                    :programmation="programmation" 
                    :comment="comment"
                    v-on:success="listComments()"
                    v-on:error="handleError"
                  ></programmation-comment-create-edit>
  
                  <v-list-item @click.stop="removeComment(comment)">
                    <v-list-item-title>Remover</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-list-item-action>
          </v-list-item>

          <v-card-text>
            {{ comment.comment }}
          </v-card-text>
          
          <v-divider v-if="index + 1 !== comments.length" class="mt-0"></v-divider>
        </template>
      </v-list>

      <v-divider class="my-0"></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <programmation-comment-create-edit 
          :programmation="programmation"
          v-on:success="listComments()"
          v-on:error="handleError"
        ></programmation-comment-create-edit>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
  export default {
    data: () => ({
      popover: false,
      comments: [],
      loading: false
    }),
    props: {
      color: '',
      programmation: {},
      authUser: {}
    },
    watch: {
      popover() {
        if (!this.popover) return;

        this.listComments();
      }
    },
    methods: {
      listComments() {
        this.loading = true;
        this.comments = [];

        axios.get(`/api/programmation/${this.programmation.id}/comment`, {})
          .then(response => this.comments = response.data.data)
          .catch(error => this.handleError(error.response.data.message))
          .finally(() => this.loading = false)
        ;
      },
      async removeComment(comment) {
        const confirm = await this.$confirm(`Deseja remover este comentário?`);

        if (!confirm) return;

        this.loading = true;

        axios
          .delete(`/api/programmation/${this.programmation.id}/comment/${comment.id}`, {})
          .then(() => this.listComments())
          .catch(error => this.handleError(error.response.data.message))
        ;
      },
      handleError(message) {
        this.$emit('error', message);
      }
    }
  }
</script>