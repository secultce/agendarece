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

      <programmation-note-list 
        :programmation="event.extendedProps.programmation" 
        :color="color"
        v-on:error="actionError"
      ></programmation-note-list>

      <programmation-link-list 
        :programmation="event.extendedProps.programmation" 
        :color="color"
        v-on:error="actionError"
      ></programmation-link-list>

      <v-tooltip top>
        <template v-slot:activator="{ on, attrs }">
          <v-btn icon x-small v-bind="attrs" v-on="on" :color="color">
            <v-icon x-small>fas fa-trash</v-icon>
          </v-btn>
        </template>
        <span>Remover</span>
      </v-tooltip>

      <programmation-create-edit
        :programmation="event.extendedProps.programmation" 
        :color="color"
        ref="programmationEdit"
        v-on:success="actionSuccess"
        v-on:error="actionError"
      ></programmation-create-edit>
    </div>
  </div>
</template>

<script>
  export default {
    data: () => ({
      linksMenu: false
    }),
    methods: {
      async silentEdit(startDate, endDate) {
        return this.$refs.programmationEdit.saveProgrammationDates(startDate, endDate);
      },
      showEditDialog() {
        this.$refs.programmationEdit.dialog = true;
      },
      actionSuccess($event) {
        this.$emit('success', $event);
      },
      actionError($event) {
        this.$emit('error', $event);
      },
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