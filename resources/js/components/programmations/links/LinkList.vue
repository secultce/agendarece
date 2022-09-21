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
    top
  >
    <template v-slot:activator="{ on: menu, attrs }">
      <v-tooltip top>
        <template v-slot:activator="{ on: tooltip }">
          <v-btn icon x-small v-bind="attrs" v-on="{...tooltip, ...menu}" :color="color" @click.stop="popover = true">
            <v-icon x-small>fas fa-link</v-icon>
          </v-btn>
        </template>
        <span>Links</span>
      </v-tooltip>
    </template>

    <v-card>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>Lista de Links</v-list-item-title>
          </v-list-item-content>

          <v-list-item-action>
            <v-btn small icon @click="popover = false">
              <v-icon small>fas fa-times</v-icon>
            </v-btn>
          </v-list-item-action>
        </v-list-item>

        <v-divider class="my-0"></v-divider>

        <v-card-text v-if="loading">
          <v-progress-circular
            indeterminate
            color="primary"
          ></v-progress-circular>
        </v-card-text>

        <v-card-text v-else-if="!links.length">
          <v-alert
            type="info"
            class="mb-0"
            dense
          >
            Nenhum link encontrado
          </v-alert>
        </v-card-text>

        <template v-else v-for="link of links">
          <v-list-item @click="openLink(link.link)" :key="link.id">
            <v-list-item-content>
              <v-list-item-title>{{ link.name }}</v-list-item-title>
              <v-list-item-subtitle>{{ link.link }}</v-list-item-subtitle>
            </v-list-item-content>
  
            <v-list-item-action v-if="authUser.id === link.user.id">
              <v-menu offset-x>
                <template v-slot:activator="{ on, attrs }">
                  <v-btn icon small v-bind="attrs" v-on="on">
                    <v-icon small>fas fa-ellipsis-v</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <programmation-link-create-edit 
                    :programmation="programmation" 
                    :link="link"
                    v-on:success="listLinks()"
                    v-on:error="handleError"
                  ></programmation-link-create-edit>
  
                  <v-list-item @click.stop="removeLink(link)">
                    <v-list-item-title>Remover</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </v-list-item-action>
          </v-list-item>
        </template>
      </v-list>

      <v-divider class="my-0"></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <programmation-link-create-edit 
          :programmation="programmation"
          v-on:success="listLinks()"
          v-on:error="handleError"
        ></programmation-link-create-edit>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
  export default {
    data: () => ({
      popover: false,
      links: [],
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

        this.listLinks();
      }
    },
    methods: {
      listLinks() {
        this.loading = true;
        this.links   = [];

        axios.get(`/api/programmation/${this.programmation.id}/link`, {})
          .then(response => this.links = response.data.data)
          .catch(error => this.handleError(error.response.data.message))
          .finally(() => this.loading = false)
        ;
      },
      async removeLink(link) {
        const confirm = await this.$confirm(`Deseja remover o link ${link.name}?`);

        if (!confirm) return;

        this.loading = true;

        axios
          .delete(`/api/programmation/${this.programmation.id}/link/${link.id}`, {})
          .then(() => this.listLinks())
          .catch(error => this.handleError(error.response.data.message))
        ;
      },
      handleError(message) {
        this.$emit('error', message);
      },
      openLink(link) {
        window.open(link, '_blank');
      }
    }
  }
</script>