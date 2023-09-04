<template>
  <a class="dropdown-item d-flex justify-content-between" @click="active = !active">
    <span>
      <i class="fas fa-moon"></i>
      Modo Escuro
    </span>

    <v-switch
      v-model="active"
      class="m-0"
      @click="active = !active"
      hide-details
      dense
      flat
    ></v-switch>
  </a>
</template>
<script>
  export default {
    data: () => ({
      active: false,
      initialized: false
    }),
    mounted() {
      this.active = this.authUser.dark_mode;
    },
    watch: {
      active() {
        if (!this.initialized) {
          this.initialized = true;

          return;
        }

        this.saveDarkMode();
      }
    },
    methods: {
      saveDarkMode() {
        if (this.active) $('body').addClass('dark-mode');
        else $('body').removeClass('dark-mode');

        axios.put(`/api/user/toggle-dark-mode`, {});
      }
    },
    props: {
      authUser: {}
    }
  }
</script>