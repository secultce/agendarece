<template>
  <div id="programmation-caption-component">
    <v-expansion-panels>
      <v-expansion-panel class="mb-5">
        <v-expansion-panel-header expand-icon="">
          <h5 class="mb-0 text-center">Legendas de Categoria</h5>
          <v-icon class="ml-3">fas fa-caret-down</v-icon>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <div class="row caption-wrapper">
            <div class="col-md-3 caption-item" v-for="category in categories" :key="category.id">
              <div class="color-preview mr-3" v-bind:style="{backgroundColor: category.color}"></div>
              {{ category.name }}
            </div>
          </div>
        </v-expansion-panel-content>
      </v-expansion-panel>

      <v-expansion-panel v-if="schedule && schedule.calendar_icons">
        <v-expansion-panel-header expand-icon="">
          <h5 class="mb-0 text-center">Legendas de Espaços</h5>
          <v-icon class="ml-3">fas fa-caret-down</v-icon>
        </v-expansion-panel-header>
        <v-expansion-panel-content>
          <div class="row caption-wrapper">
            <div class="col-md-3 caption-item" v-for="space in spaces" :key="space.id" v-bind:style="{'--icon-filter': iconFilter}">
              <img :src="space.icon_url" class="mr-3" :alt="`${space.name} Icon`" width="30px" height="30px">
              {{ space.name }}
            </div>
          </div>
        </v-expansion-panel-content>
      </v-expansion-panel>
    </v-expansion-panels>
  </div>
</template>

<script>
  import { generateFilter } from 'colorize-filter';

  export default {
    data: () => ({
      iconFilter: generateFilter(getComputedStyle(document.documentElement).getPropertyValue('--dark').replace('#', '').trim()),
    }),
    props: {
      categories: {},
      spaces: {},
      schedule: {}
    }
  }
</script>