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

Vue.filter('date', (value, outputFormat = 'DD/MM/YYYY', inputFormat = 'YYYY-MM-DD') => {
    return moment(value, inputFormat).format(outputFormat);
});

Vue.filter('captalize', (value) => {
    return value.substring(0, 1).toUpperCase() + value.substring(1);
});

Vue.filter('regex', (value) => {
    let splitSearch = value.replace(/\s/g, '%').split('%');
    let regexstring = "";
    
    splitSearch.forEach((item, index) => {
        if (index == 0) { 
        regexstring += `(?=^${item.toLowerCase()}.*$)`;
        
        return true;
        }
        
        regexstring += `(?=^.*${item.toLowerCase()}.*$)`
    });
    
    return new RegExp(regexstring);
});

Vue.component('user-create-edit', require('./components/users/UserCreateEdit.vue').default);
Vue.component('user-data-table', require('./components/users/UserDataTable.vue').default);
Vue.component('space-create-edit', require('./components/spaces/SpaceCreateEdit.vue').default);
Vue.component('space-data-table', require('./components/spaces/SpaceDataTable.vue').default);
Vue.component('category-create-edit', require('./components/categories/CategoryCreateEdit.vue').default);
Vue.component('category-data-table', require('./components/categories/CategoryDataTable.vue').default);
Vue.component('programmation', require('./components/programmations/Programmation.vue').default);
Vue.component('programmation-calendar', require('./components/programmations/ProgrammationCalendar.vue').default);
Vue.component('programmation-create-edit', require('./components/programmations/ProgrammationCreateEdit.vue').default);

const app = new Vue({
    vuetify,
    el: '#app'
});