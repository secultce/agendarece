<template>
  <div id="programmation-list-component">
    <template v-if="!programmations.length">
      <v-alert type="info" dense text>Nenhuma programação no período escolhido.</v-alert>
    </template>

    <template v-for="programmationGroup, monthYear of programmationGroups">
      <div class="programmation-group" :key="monthYear">
        <h5 class="time-divider">
          <time>{{ `${monthYear}-01` | date('MMMM YYYY') | captalize }}</time>
        </h5>

        <div class="programmation-item justify-content-start" v-for="programmation of programmationGroup" :key="programmation.id">
          <v-card class="elevation-0">
            <v-card-subtitle class="px-0 pb-2 d-flex align-items-center">
              <v-icon small color="red" class="mr-2">fas fa-calendar-alt</v-icon>
              {{ programmation.start_date | date('MMM DD') | captalize }}, {{ programmation.start_time | date('HH:mm', 'HH:mm:ss') }} - 
              {{ (programmation.end_date && programmation.end_date > programmation.start_date ? programmation.end_date : '') | date('MMM DD') | captalize }}
              {{ programmation.end_time | date('HH:mm', 'HH:mm:ss') }}
            </v-card-subtitle>
            <v-card-title class="px-0 pt-0">{{ programmation.title }}</v-card-title>
            <v-card-subtitle class="px-0 text-dark">{{ programmation.category.name }}</v-card-subtitle>

            <v-alert v-for="space of programmation.spaces" :key="space.id" dense text class="mb-0 d-inline-block" :style="{ '--icon-filter': iconFilter(programmation.category.color), color: programmation.category.color }">
              <template v-slot:prepend>
                <img :src="space.space.icon_url" :alt="`${space.space.name} Icon`" class="mr-3 space-icon" width="30px" height="25px">
              </template>

              {{ space.space.name }}
            </v-alert>

            <v-card-text class="px-0">{{ programmation.description ? programmation.description : 'Programação não possui uma descrição' }}</v-card-text>
          </v-card>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
  import { generateFilter } from 'colorize-filter';

  export default {
    data: () => ({}),
    props: {
      programmations: []
    },
    methods: {
      iconFilter(color) {
        return generateFilter(color);
      }
    },
    computed: {
      programmationGroups() {
        return _.groupBy(_.cloneDeep(this.programmations), programmation => {
          return moment(programmation.start_date).format('YYYY-MM')
        });
      }
    }
  }
</script>