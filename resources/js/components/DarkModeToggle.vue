<template>
  <a v-if="dropdownMode" class="dropdown-item d-flex justify-content-between" @click="active = !active">
    <span>
      <i class="fas fa-moon"></i>
      Modo Escuro
    </span>

    <v-switch
      v-model="active"
      readonly
      class="m-0"
      hide-details
      dense
      flat
    ></v-switch>
  </a>

  <li class="nav-item" @click="active = !active" v-else>
    <a class="nav-link d-flex">
      <v-switch
        v-model="active"
        class="m-0"
        readonly
        hide-details
        dense
        flat
      ></v-switch>

      <span class="ms-3">
        <i class="fas fa-moon"></i>
        Modo Escuro
      </span>
    </a>
  </li>
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
      dropdownMode: true,
      authUser: {}
    }
  }
</script>