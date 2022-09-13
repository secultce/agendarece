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
Vue.component('space-create-edit', require('./components/spaces/SpaceCreateEdit.vue').default);
Vue.component('space-data-table', require('./components/spaces/SpaceDataTable.vue').default);
Vue.component('category-create-edit', require('./components/categories/CategoryCreateEdit.vue').default);
Vue.component('category-data-table', require('./components/categories/CategoryDataTable.vue').default);

const app = new Vue({
    vuetify,
    el: '#app'
});