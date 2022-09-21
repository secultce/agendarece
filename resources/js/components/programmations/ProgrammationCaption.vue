<template>
  <div id="programmation-caption-component">
    <div class="row" v-if="categories.length">
      <div class="col-md-12">
        <h5>Legendas de Categoria</h5>
        <div class="row caption-wrapper">
          <div class="col-md-3 caption-item" v-for="category in categories" :key="category.id">
            <div class="color-preview mr-3" v-bind:style="{backgroundColor: category.color}"></div>
            {{ category.name }}
          </div>
        </div>
      </div>
    </div>

    <div class="row" v-if="spaces.length">
      <div class="col-md-12">
        <h5>Legendas de Espaços</h5>
        <div class="row caption-wrapper">
          <div class="col-md-3 caption-item" v-for="space in spaces" :key="space.id" v-bind:style="{'--icon-filter': iconFilter}">
            <img :src="space.icon_url" class="mr-3" :alt="`${space.name} Icon`" width="30px" height="30px">
            {{ space.name }}
          </div>
        </div>
      </div>
    </div>
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
      spaces: {}
    }
  }
</script>