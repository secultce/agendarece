<template>
  <v-menu
    v-model="popover"
    :close-on-content-click="false"
    :close-on-click="false"
    persistent
    nudge-width="600"
    max-width="600"
    transition="slide-x-transition"
    offset-y
    offset-x
    top
    right
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