<template>
    <div>
      <template v-if="layout === 'v1'">
        <programmation-calendar></programmation-calendar>
      </template>
  
      <template v-else>
        <programmation-sheet></programmation-sheet>
      </template>
    </div>
  </template>
  
  <script>
    export default {
      data: () => ({}),
      props: {
        layout: {
          type: String,
          default: 'v1'
        }
      }
    }
  </script>