<template>
  <div id="programmation-calendar-component">
    <full-calendar :options="options" :events="events" ref="calendar">
      <template v-slot:eventContent='item'>
        <programmation-actions
          v-if="actionsIsActive"
          :ref="`programmation-actions-${item.event.extendedProps.programmation.id}`"
          :event="item.event"
          v-on:success="actionSuccessHandler"
          v-on:error="actionErrorHandler"
        ></programmation-actions>
        <div class="programmation-spaces">
          <template v-for="programmationSpace in item.event.extendedProps.programmation.spaces">
            <span v-bind:class="item.event.extendedProps.isDark ? 'dark' : ''" :key="programmationSpace.id">{{ programmationSpace.space.name }}</span>
          </template>
        </div>
        <p class="programmation-title">{{ item.event.title }}</p>
        <p class="programmation-description">{{ item.event.extendedProps.programmation.description }}</p>
      </template>
    </full-calendar>
  </div>
</template>

<script>
  import FullCalendar from '@fullcalendar/vue';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import timeGridPlugin from '@fullcalendar/timegrid';
  import interactionPlugin from '@fullcalendar/interaction';
  import Color from '../../color';

  export default {
    components: { FullCalendar },
    data: () => ({}),
    props: {
      programmations: [],
      date: '',
      authUser: {}
    },
    watch: {
      date() {
        this.calendar.gotoDate(this.date);
      }
    },
    computed: {
      actionsIsActive() {
        return ['administrator', 'scheduler'].indexOf(this.authUser.role.tag) !== -1;
      },
      options() {
        return {
          plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin ],
          headerToolbar: { left: '', center: '', right: ''},
          initialView: 'dayGridMonth',
          locale: 'pt-br',
          editable: this.actionsIsActive,
          selectable: this.actionsIsActive,
          selectMirror: this.actionsIsActive,
          dayMaxEvents: false,
          weekends: true,
          showNonCurrentDates: false,
          select: this.selectHandler,
          eventClick: this.clickHandler,
          eventOverlap: this.overlapHandler,
          eventChange: this.changeHandler,
          events: this.events
        }
      },
      events() {
        let events = [];

        this.programmations.forEach(programmation => {
          let isDark = Color.isDark(programmation.category.color);
          let endDate = (() => {
            if (!programmation.end_date) return `${moment(this.date).endOf('month').add(1, 'days').format('YYYY-MM-DD')}T${programmation.end_time}`;

            return moment(`${programmation.end_date}T${programmation.end_time}`).add(1, 'days').format('YYYY-MM-DD hh:mm:ss');
          })();

          events.push({
            allDay: true,
            title: programmation.title,
            start: `${programmation.start_date}T${programmation.start_time}`,
            end: endDate,
            backgroundColor: programmation.category.color,
            textColor: isDark ? '#fff' : '#000',
            borderColor: isDark ? programmation.category.color : '#e4e4e4',
            programmation,
            isDark: isDark
          });
        });

        return events;
      },
      calendar() {
        return this.$refs.calendar.getApi();
      }
    },
    methods: {
      overlapHandler(stillEvent, movingEvent) {
        if (!movingEvent.extendedProps.programmation.end_date) {
          this.$emit('error', 'Programações sem data de término devem ser atualizadas no formulário');

          return false;
        }

        let spaceIndex = stillEvent.extendedProps.programmation.spaces.findIndex(stillSpace => {
          return movingEvent.extendedProps.programmation.spaces.findIndex(movingSpace => movingSpace.space_id === stillSpace.space_id) !== -1;
        });

        if (spaceIndex === -1) return true;

        let format = 'hh:mm:ss';
        let movingStartTime = moment(movingEvent.extendedProps.programmation.start_time, format);
        let movingEndTime = moment(movingEvent.extendedProps.programmation.end_time, format);
        let stillStartTime = moment(stillEvent.extendedProps.programmation.start_time, format);
        let stillEndTime = moment(stillEvent.extendedProps.programmation.end_time, format);

        return !movingStartTime.isBetween(stillStartTime, stillEndTime, null, '[]') || !movingEndTime.isBetween(stillStartTime, stillEndTime, null, '[]');
      },
      selectHandler(info) {
        this.calendar.unselect();

        this.$emit('select', info);
      },
      clickHandler(info) {
        this.$refs[`programmation-actions-${info.event.extendedProps.programmation.id}`].showEditDialog();
      },
      changeHandler(info) {
        let endDate = moment(info.event.endStr).subtract(1, 'days').format('YYYY-MM-DD');

        this.$refs[`programmation-actions-${info.event.extendedProps.programmation.id}`]
          .silentEdit(
            info.event.startStr, 
            endDate
          )
          .then(() => {
            info.event.extendedProps.programmation.start_date = info.event.startStr;
            info.event.extendedProps.programmation.end_date   = endDate;
          })
          .catch(error => {
            info.revert();
            
            this.$emit('error', error);
          })
        ;
      },
      actionSuccessHandler($event) {
        this.$emit('success', $event);
      },
      actionErrorHandler($event) {
        this.$emit('error', $event);
      }
    }
  }
</script>