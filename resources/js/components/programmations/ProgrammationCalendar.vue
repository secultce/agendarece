<template>
  <div id="programmation-calendar-component">
    <full-calendar :options="options" :events="events" ref="calendar">
      <template v-slot:eventContent='item'>
        <template v-if="!item.event.extendedProps.holiday">
          <programmation-actions
            :ref="`programmation-actions-${item.event.extendedProps.programmation.id}`"
            :event="item.event"
            :auth-user="authUser"
            :active="actionsIsActive"
            :schedule="schedule"
            :sector="sector"
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
          <ul v-if="item.event.extendedProps.programmation.accessibilities" class="programmation-accessibilities">
            <li v-for="programmationAccessibility in item.event.extendedProps.programmation.accessibilities">
              <v-tooltip left>
                <template v-slot:activator="{ on, attrs }">
                  <img v-if="programmationAccessibility === 5" v-bind="attrs" v-on="on" src="../../../images/acessibility/5.png" alt="Libras" width="40px">
                </template>
                <span v-if="programmationAccessibility === 5">Evento acessível em Libras</span>
              </v-tooltip>
            </li>
          </ul>
        </template>
        <template v-else>
          <holiday-content
            v-if="item.event.extendedProps.data.id && item.event.extendedProps.data.body"
            :holiday="item.event.extendedProps.data"
          ></holiday-content>

          <p v-else class="holiday-title">{{ item.event.title }}</p>
        </template>
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
    data: () => ({
      broadcastingNoteChange: false,
      broadcastingLinkChange: false,
      broadcastingCommentChange: false
    }),
    props: {
      programmations: [],
      date: '',
      authUser: {},
      schedule: null,
      holidays: [],
      sector: null
    },
    computed: {
      actionsIsActive() {
        if (!this.schedule) return false;
        if (this.authUser.role.tag === 'administrator') return true;
        if (['scheduler', 'responsible'].indexOf(this.authUser.role.tag) !== -1 && this.authUser.sector.id === this.schedule.sector_id) return true;

        return false;
      },
      editCreateOrRemove() {
        if (!this.schedule) return true;

        return this.authUser.role.tag === 'administrator' || 
          this.schedule.user_id === this.authUser.id || 
          this.schedule.users.findIndex(user => user.id === this.authUser.id) !== -1
        ;
      },
      options() {
        return {
          plugins: [ dayGridPlugin, interactionPlugin ],
          headerToolbar: { left: '', center: '', right: ''},
          initialView: window.isMobile() ? 'dayGridDay' : 'dayGridMonth',
          locale: 'pt-br',
          height: "auto",
          firstDay: 1,
          selectable: this.actionsIsActive && this.editCreateOrRemove,
          selectMirror: this.actionsIsActive && this.editCreateOrRemove,
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
          eventDrop: this.dropHandler,
          eventOrder: [this.sortHolidays, 'start'],
          views: {
            dayGridMonth: {
              dayHeaderFormat: {
                weekday: 'long'
              }
            },
            dayGridDay: {
              duration: { days: 2 },
            }
          }
        }
      },
      events() {
        let events = [];

        this.programmations.forEach(programmation => {
          let isDark = Color.isDark(programmation.category.color);
          let calendarDate = moment(this.date);
          let programmationStart = moment(programmation.start_date);
          let event = {
            id: programmation.id,
            editable: this.editCreateOrRemove,
            allDay: true,
            holiday: false,
            slotEventOverlap: false,
            title: programmation.title,
            backgroundColor: programmation.category.color,
            textColor: isDark ? 'var(--white-color)' : 'var(--black-color)',
            borderColor: 'rgba(0, 0, 0, 0)',
            programmation,
            isDark: isDark
          };
          
          if (!programmation.end_date) {
            if (programmation.loop_days.length && programmation.loop_days.length < 7) {
              let monthLastDay = parseInt(calendarDate.endOf('month').format('D'));
              let startDay     = calendarDate.diff(programmationStart, 'months') > 0  ? 1 : parseInt(programmationStart.format('D'));

              for (let day = startDay; day <= monthLastDay; day++) {
                let endDate = moment(`${calendarDate.format('YYYY-MM')}-${day}`, 'YYYY-MM-D');
                let eventClone   = Object.assign({}, event);

                if (programmation.loop_days.indexOf(endDate.day()) === -1) continue;

                eventClone.start = `${endDate.format('YYYY-MM-DD')}T${programmation.start_time}`;
                eventClone.end   = `${endDate.add(1, 'days').format('YYYY-MM-DD')}T${programmation.end_time}`;

                events.push(eventClone);
              }

              return true;
            } else {
              event.start = `${programmation.start_date}T${programmation.start_time}`;
              event.end   = `${calendarDate.endOf('month').add(1, 'days').format('YYYY-MM-DD')}T${programmation.end_time}`;
            }
          } else {
            event.start = `${programmation.start_date}T${programmation.start_time}`;
            event.end   = moment(`${programmation.end_date}T${programmation.end_time}`).add(1, 'days').format('YYYY-MM-DD hh:mm:ss');
          }

          events.push(event);
        });

        this.holidays.forEach(holiday => {
          events.push({
            editable: false,
            allDay: true,
            holiday: true,
            custom: holiday.custom,
            optional: holiday.optional,
            slotEventOverlap: false,
            data: holiday,
            title: holiday.name,
            start: holiday.start_at,
            end: holiday.end_at,
            backgroundColor: "#fff",
            textColor: '#000',
            borderColor: '#a0a0a0',
            className: `holiday ${holiday.id ? 'custom' : ''} ${holiday.body ? 'has-body' : ''}`,
          });
        });

        return events;
      },
      calendar() {
        return this.$refs.calendar.getApi();
      }
    },
    watch: {
      date() {
        this.calendar.gotoDate(this.date);
      },
      programmations() {
        this.listenProgrammationChannels();
      }
    },
    methods: {
      sortHolidays(event) {
        return event.holiday ? -1 : 0;
      },
      overlapHandler(stillEvent, movingEvent) {
        if (!movingEvent.extendedProps.programmation.end_date) {
          this.$emit('error', 'Programações sem data de término devem ser atualizadas no formulário');

          return false;
        }

        if (stillEvent.extendedProps.holiday) return true;

        let spaceIndex = stillEvent.extendedProps.programmation.spaces.findIndex(stillSpace => {
          return movingEvent.extendedProps.programmation.spaces.findIndex(movingSpace => movingSpace.space_id === stillSpace.space_id) !== -1;
        });

        if (spaceIndex === -1) return true;

        let format = 'hh:mm:ss';
        let movingStartTime = moment(movingEvent.extendedProps.programmation.start_time, format);
        let movingEndTime = moment(movingEvent.extendedProps.programmation.end_time, format);
        let stillStartTime = moment(stillEvent.extendedProps.programmation.start_time, format);
        let stillEndTime = moment(stillEvent.extendedProps.programmation.end_time, format);

        return (!movingStartTime.isBetween(stillStartTime, stillEndTime, null, '[]') || !movingEndTime.isBetween(stillStartTime, stillEndTime, null, '[]')) &&
          (!stillStartTime.isBetween(movingStartTime, movingEndTime, null, '[]') || !stillEndTime.isBetween(movingStartTime, movingEndTime, null, '[]'))
        ;
      },
      selectHandler(info) {
        this.calendar.unselect();

        this.$emit('select', info);
      },
      clickHandler(info) {
        if (info.event.extendedProps.holiday) return;
        if (!info.event.startEditable) {
          this.$refs[`programmation-actions-${info.event.extendedProps.programmation.id}`].showViewOnlyDialog();

          return;
        }

        this.$refs[`programmation-actions-${info.event.extendedProps.programmation.id}`].showEditDialog();
      },
      changeHandler(info) {
        if (this.broadcastingNoteChange || this.broadcastingLinkChange || this.broadcastingCommentChange) {
          this.broadcastingNoteChange = false;
          this.broadcastingLinkChange = false
          this.broadcastingCommentChange = false;

          return;
        }

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
      changeDayColorForHolidays(info) {
        if (!info.event.extendedProps.holiday || info.event.extendedProps.custom || info.event.extendedProps.optional) return;

        let dayGridComponent = $(info.el).closest('.fc-daygrid-day-events').prev('.fc-daygrid-day-top');

        dayGridComponent.find('a').css({backgroundColor: "var(--red-color)", color: "var(--white-color)"});
      },
      createSpaceIcons(info) {
        if (info.event.extendedProps.holiday || !this.schedule?.calendar_icons) return;

        let programmation = info.event.extendedProps.programmation;
        let dayGridComponent = $(info.el).closest('.fc-daygrid-day-events').prev('.fc-daygrid-day-top');
        let iconsComponent = dayGridComponent.find('.programmation-icons');
        let iconFilter = generateFilter(programmation.category.color !== '#ffffff' && programmation.category.color !== 'rgb(255, 255, 255)' ? programmation.category.color : getComputedStyle(document.documentElement).getPropertyValue('--black-color'));

        if (!iconsComponent.length) iconsComponent = dayGridComponent.append('<div class="programmation-icons"></div>').find('.programmation-icons');

        _.map(programmation.spaces, 'space').forEach(space => {
          if (iconsComponent.find(`.programmation-${programmation.id}.space-${space.id}`).length > 0) return true;

          iconsComponent.append(`
            <div class="programmation-${programmation.id} space-${space.id}">
              <img src="${space.icon_url}" title="${space.name}" width="100%" height="100%" style="cursor: help; filter: ${iconFilter}">
            </div>
          `);
        });

        iconsComponent.on("wheel", function (e) {
          e.preventDefault();

          if (e.originalEvent.wheelDelta > 0 || e.originalEvent.detail < 0) iconsComponent.scrollLeft(iconsComponent.scrollLeft() - 100);
          else iconsComponent.scrollLeft(iconsComponent.scrollLeft() + 100);
        });
      },
      mountHandler(info) {
        this.createSpaceIcons(info);
        this.changeDayColorForHolidays(info);

        $(window).on('scroll', function (event) {
          let element    = $('.fc-scroller');
          let docViewTop = $(window).scrollTop();
          let elemTop    = element.offset().top;
  
          if (docViewTop <= elemTop) {
            if (element.find('.fc-col-header').hasClass('sticky')) element.find('.fc-col-header').removeClass('sticky');

            return;
          }

          if (!element.find('.fc-col-header').hasClass('sticky')) element.find('.fc-col-header').addClass('sticky');
        });
      },
      dropHandler(info) {
        $(this.calendar.el).find(`.programmation-icons .programmation-${info.event.extendedProps.programmation.id}`).remove();
      },
      dragStartHandler(info) {
        $(this.calendar.el).find(`.programmation-icons .programmation-${info.event.extendedProps.programmation.id}`).hide();
      },
      dragStopHandler(info) {
        $(this.calendar.el).find(`.programmation-icons .programmation-${info.event.extendedProps.programmation.id}`).show();
      },
      actionSuccessHandler($event) {
        if ($event instanceof String) {
          this.$emit('success', $event);

          return;
        }

        if ($event.removed) this.dropHandler($event);

        this.$emit('success', $event.message);
      },
      actionErrorHandler($event) {
        this.$emit('error', $event);
      },
      listenProgrammationChannels() {
        if (['administrator', 'scheduler'].indexOf(this.authUser.role.tag) === -1 || !this.programmations.length) return;

        this.programmations.forEach(programmation => {
          window.Echo.private(`programmation.${programmation.id}.comment`).listen("ProgrammationCommentChanged", (event) => {
            this.broadcastingCommentChange = true;
            this.calendar.getEventById(programmation.id).setExtendedProp('programmation', event.programmation);
          });

          Echo.private(`programmation.${programmation.id}.note`).listen("ProgrammationNoteChanged", (event) => {
            this.broadcastingNoteChange = true;
            this.calendar.getEventById(programmation.id).setExtendedProp('programmation', event.programmation);
          });

          window.Echo.private(`programmation.${programmation.id}.link`).listen("ProgrammationLinkChanged", (event) => {
            this.broadcastingLinkChange = true;
            this.calendar.getEventById(programmation.id).setExtendedProp('programmation', event.programmation);
          });
        });
      }
    }
  }
</script>