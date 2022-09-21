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
  import interactionPlugin from '@fullcalendar/interaction';
  import Color from '../../color';
  import { generateFilter } from 'colorize-filter';

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
          plugins: [ dayGridPlugin, interactionPlugin ],
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
          events: this.events,
          eventDidMount: this.mountHandler,
          eventDragStart: this.dragStartHandler,
          eventDragStop: this.dragStopHandler,
          eventDrop: this.dropHandler
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
    watch: {
      events() {
        this.rerenderSpaceIcons()
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
        let programmation = info.event.extendedProps.programmation;

        this.$refs[`programmation-actions-${programmation.id}`]
          .silentEdit(
            info.event.startStr, 
            endDate
          )
          .then(() => {
            programmation.start_date = info.event.startStr;
            programmation.end_date   = endDate;
          })
          .catch(error => {
            info.revert();
            
            this.$emit('error', error);
          })
        ;
      },
      createSpaceIcons(info) {
        let programmation = info.event.extendedProps.programmation;
        let dayGridComponent = $(info.el).closest('.fc-daygrid-day-events').prev('.fc-daygrid-day-top');
        let iconsComponent = dayGridComponent.find('.programmation-icons');
        let iconFilter = generateFilter(programmation.category.color);
        
        if (!iconsComponent.length) iconsComponent = dayGridComponent.append('<div class="programmation-icons"></div>').find('.programmation-icons');

        _.map(programmation.spaces, 'space').forEach(space => {
          if (iconsComponent.find(`.programmation-${programmation.id}.space-${space.id}`).length > 0) return true;

          iconsComponent.append(`
            <div class="programmation-${programmation.id} space-${space.id}">
              <img src="${space.icon_url}" title="${space.name}" width="100%" height="100%" style="cursor: help; filter: ${iconFilter}">
            </div>
          `);
        });
      },
      rerenderSpaceIcons() {
        $(this.calendar.el).find('.programmation-icons').remove();
        this.calendar.refetchEvents();
      },
      mountHandler(info) {
        this.createSpaceIcons(info);
      },
      dropHandler(info) {
        this.rerenderSpaceIcons();
      },
      dragStartHandler(info) {
        $(this.calendar.el).find('.programmation-icons').find(`.programmation-${info.event.extendedProps.programmation.id}`).hide();
      },
      dragStopHandler(info) {
        $(this.calendar.el).find(`.programmation-icons .programmation-${info.event.extendedProps.programmation.id}`).show();
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