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
    bottom
  >
    <template v-slot:activator="{ on: menu, attrs }">
      <v-tooltip top>
        <template v-slot:activator="{ on: tooltip }">
          <v-btn icon x-small v-bind="attrs" v-on="{...tooltip, ...menu}" :color="color" @click.stop="linksMenu = true">
            <v-icon x-small>fas fa-sticky-note</v-icon>
          </v-btn>
        </template>
        <span>Notas</span>
      </v-tooltip>
    </template>

    <v-card>
      <v-list>
        <v-list-item>
          <v-list-item-content>
            <v-list-item-title>Lista de Notas</v-list-item-title>
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

        <v-card-text v-else-if="!notes.length">
          <v-alert
            type="info"
            class="mb-0"
            dense
          >
            Nenhuma nota cadastrada
          </v-alert>
        </v-card-text>

        <template v-else v-for="note, index of notes">
          <v-card-text class="d-flex justify-content-start">
            {{ note.note }}

            <v-menu offset-x>
              <template v-slot:activator="{ on, attrs }">
                <v-btn icon small v-bind="attrs" v-on="on">
                  <v-icon small>fas fa-ellipsis-v</v-icon>
                </v-btn>
              </template>
              <v-list>
                <programmation-note-create-edit 
                  :programmation="programmation"
                  :note="note"
                  v-on:success="listNotes()"
                  v-on:error="handleError"
                ></programmation-note-create-edit>

                <v-list-item @click.stop="removeNote(note)">
                  <v-list-item-title>Remover</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </v-card-text>

          <v-divider class="mx-4 my-0" v-if="index + 1 !== notes.length"></v-divider>
        </template>
      </v-list>

      <v-divider class="my-0"></v-divider>

      <v-card-actions>
        <v-spacer></v-spacer>
        <programmation-note-create-edit 
          :programmation="programmation"
          v-on:success="listNotes()"
          v-on:error="handleError"
        ></programmation-note-create-edit>
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
  export default {
    data: () => ({
      popover: false,
      notes: [],
      loading: false
    }),
    props: {
      color: '',
      programmation: {}
    },
    watch: {
      popover() {
        if (!this.popover) return;

        this.listNotes();
      }
    },
    methods: {
      listNotes() {
        this.loading = true;
        this.notes   = [];

        axios.get(`/api/programmation/${this.programmation.id}/note`, {})
          .then(response => this.notes = response.data.data)
          .catch(error => this.handleError(error.response.data.message))
          .finally(() => this.loading = false)
        ;
      },
      async removeNote(note) {
        const confirm = await this.$confirm(`Deseja remover esta nota?`);

        if (!confirm) return;

        this.loading = true;

        axios
          .delete(`/api/programmation/${this.programmation.id}/note/${note.id}`, {})
          .then(() => this.listNotes())
          .catch(error => this.handleError(error.response.data.message))
        ;
      },
      handleError(message) {
        this.$emit('error', message);
      }
    }
  }
</script>