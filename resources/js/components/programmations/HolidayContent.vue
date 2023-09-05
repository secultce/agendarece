<template>
  <v-menu
    v-model="popover"
    :close-on-content-click="false"
    :close-on-click="false"
    nudge-top="10"
    content-class="holiday-content"
    nudge-width="600"
    max-width="600"
    transition="slide-x-transition"
    open-on-hover
    offset-y
    top
  >
    <template v-slot:activator="{ on, attrs }">
      <p v-bind="attrs" v-on="on" class="holiday-title">{{ holiday.name }}</p>
    </template>

    <v-card>
      <v-card-text v-html="holiday.body"></v-card-text>
    </v-card>
  </v-menu>
</template>

<script>
  export default {
    data: () => ({
      popover: false,
    }),
    props: {
      holiday: {},
    }
  }
</script>