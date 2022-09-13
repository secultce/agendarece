<template>
    <div class="d-inline">
      <v-dialog
        v-model="dialog"
        width="700"
        :persistent="overlay"
        eager
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn v-bind="attrs" v-on="on" color="primary" class="elevation-0" large rounded>
            <v-icon class="mr-1" small>fas fa-calendar-alt</v-icon>
            Nova Programação
          </v-btn>
        </template>
  
        <v-card class="px-3 pb-6 pt-2">
          <v-card-title
            class="headline"
            primary-title
          >
            <v-spacer></v-spacer>
            <v-btn icon :disabled="overlay" @click="dialog = false">
              <v-icon>fas fa-times</v-icon>
            </v-btn>
          </v-card-title>
  
          <v-card-text>
  
          </v-card-text>
  
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="primary"
              class="elevation-0 mt-3"
              large
              rounded
              block
              :loading="overlay"
              @click="saveProgrammation()"
            >
              Salvar
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
  
      <v-overlay :value="overlay">
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
    </div>
  </template>
  <script>
    export default {
      data: () => ({
        overlay: false,
        dialog: false,
      }),
      methods: {
        saveProgrammation() {
          // 
        },
        clearCredentials() {
          // 
        }
      }
    }
  </script>