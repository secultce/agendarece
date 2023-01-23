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
    if (!value) return '';

    return moment(value, inputFormat).format(outputFormat);
});

Vue.filter('captalize', (value) => {
    return value.substring(0, 1).toUpperCase() + value.substring(1);
});

Vue.filter('trim', (value) => {
    return value.trim();
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
Vue.component('programmation-actions', require('./components/programmations/ProgrammationActions.vue').default);
Vue.component('programmation-caption', require('./components/programmations/ProgrammationCaption.vue').default);
Vue.component('programmation-link-list', require('./components/programmations/links/LinkList.vue').default);
Vue.component('programmation-link-create-edit', require('./components/programmations/links/LinkCreateEdit.vue').default);
Vue.component('programmation-note-list', require('./components/programmations/notes/NoteList.vue').default);
Vue.component('programmation-note-create-edit', require('./components/programmations/notes/NoteCreateEdit.vue').default);
Vue.component('programmation-comment-list', require('./components/programmations/comments/CommentList.vue').default);
Vue.component('programmation-comment-create-edit', require('./components/programmations/comments/CommentCreateEdit.vue').default);
Vue.component('programmation-list', require('./components/programmations/ProgrammationList.vue').default);
Vue.component('programmation-report-dialog', require('./components/programmations/ProgrammationReportDialog.vue').default);
Vue.component('log-data-table', require('./components/logs/LogDataTable.vue').default);
Vue.component('schedule-data-table', require('./components/schedules/ScheduleDataTable.vue').default);
Vue.component('schedule-create-edit', require('./components/schedules/ScheduleCreateEdit.vue').default);
Vue.component('custom-holiday-data-table', require('./components/custom-holidays/CustomHolidayDataTable.vue').default);
Vue.component('custom-holiday-create-edit', require('./components/custom-holidays/CustomHolidayCreateEdit.vue').default);
Vue.component('sector-create-edit', require('./components/sectors/SectorCreateEdit.vue').default);
Vue.component('sector-data-table', require('./components/sectors/SectorDataTable.vue').default);

const app = new Vue({
    vuetify,
    el: '#app'
});