import Vue from 'vue';
import vuetify from './vuetify';
import VuetifyConfirm from 'vuetify-confirm';

Vue.use(VuetifyConfirm, {
    vuetify: vuetify,
    buttonTrueText: 'Sim',
    buttonFalseText: 'Não',
    color: 'red',
    icon: '',
    title: '',
    width: 350,
    property: '$confirm'
});

Vue.component('user-create-edit', require('./components/users/UserCreateEdit.vue').default);
Vue.component('user-data-table', require('./components/users/UserDataTable.vue').default);

const app = new Vue({
    vuetify,
    el: '#app'
});