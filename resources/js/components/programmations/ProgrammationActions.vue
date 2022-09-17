<template>
  <div class="programmation-period d-flex">
    <span>{{ event.extendedProps.programmation.start_time | date('HH:mm', 'hh:mm:ss') }} - {{ event.extendedProps.programmation.end_time | date('HH:mm', 'hh:mm:ss') }}</span>
    <div class="ml-auto actions">
      <v-tooltip top>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon x-small v-bind="attrs" v-on="on" :color="color" @click.stop="showComments">
            <v-icon x-small>fas fa-comment-alt</v-icon>
          </v-btn>
        </template>
        <span>Comentários</span>
      </v-tooltip>

      <v-tooltip top>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon x-small v-bind="attrs" v-on="on" :color="color" @click.stop="showNotes">
            <v-icon x-small>fas fa-sticky-note</v-icon>
          </v-btn>
        </template>
        <span>Notas</span>
      </v-tooltip>

      <v-tooltip top>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon x-small v-bind="attrs" v-on="on" :color="color" @click.stop="showLinks">
            <v-icon x-small>fas fa-link</v-icon>
          </v-btn>
        </template>
        <span>Links</span>
      </v-tooltip>

      <v-tooltip top>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon x-small v-bind="attrs" v-on="on" :color="color" @click.stop="removeProgrammation">
            <v-icon x-small>fas fa-trash</v-icon>
          </v-btn>
        </template>
        <span>Remover</span>
      </v-tooltip>
    </div>
  </div>
</template>

<script>
  export default {
    data: () => ({

    }),
    methods: {
      showComments($event) {
        // 
      },
      showNotes($event) {
        // 
      },
      showLinks($event) {
        // 
      },
      removeProgrammation() {

      }
    },
    computed: {
      color() {
        return this.event.extendedProps.isDark ? 'white' : 'black';
      }
    },
    props: {
      event: {}
    }
  }
</script>