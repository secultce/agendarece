<template>
  <full-calendar :options="options" :events="events" ref="calendar">
    <template v-slot:eventContent='item'>
      <b>{{ item.timeText }}</b>
      <i>{{ item.event.title }}</i>
    </template>
  </full-calendar>
</template>

<script>
  import FullCalendar from '@fullcalendar/vue';
  import dayGridPlugin from '@fullcalendar/daygrid';
  import timeGridPlugin from '@fullcalendar/timegrid';
  import interactionPlugin from '@fullcalendar/interaction';
  import isDarkColor from 'is-dark-color';


  export default {
    components: { FullCalendar },
    data: () => ({}),
    props: {
      programmations: [],
      date: '',
      exhibition: '',
    },
    watch: {
      date() {
        this.calendar.gotoDate(this.date);
      },
      exhibition() {
        if (this.exhibition === 'month') {
          this.calendar.changeView('dayGridMonth');

          return;
        }

        this.calendar.changeView('timeGridWeek');
      }
    },
    computed: {
      options() {
        return {
          plugins: [ dayGridPlugin, timeGridPlugin, interactionPlugin ],
          headerToolbar: { left: '', center: '', right: ''},
          initialView: 'dayGridMonth',
          locale: 'pt-br',
          editable: true,
          selectable: true,
          selectMirror: true,
          dayMaxEvents: true,
          weekends: true,
          showNonCurrentDates: false,
          select: this.selectHandler,
          eventClick: this.clickHandler,
          events: this.events,
          // eventAdd: 
          // eventChange:
          // eventRemove:
        }
      },
      events() {
        let events = [];

        this.programmations.forEach(programmation => {
          let isDark = isDarkColor(programmation.category.color);

          if (!programmation.end_date) {
            events.push({
              groupId: 'noEndEvents',
              title: programmation.title,
              startTime: `${programmation.startTime}:00`,
              endTime:  `${programmation.endTime}:00`,
              startRecur: programmation.start_date,
              color: programmation.category.color,
              textColor: isDark ? '#fff' : '#000',
              borderColor: isDark ? programmation.category.color : '#e4e4e4',
              programmation
            });

            return true;
          }

          events.push({
            // allDay: false,
            title: programmation.title,
            start: programmation.start_date,
            end: moment(programmation.end_date).add(1, 'days').format('YYYY-MM-DD'),
            color: programmation.category.color,
            textColor: isDark ? '#fff' : '#000',
            borderColor: isDark ? programmation.category.color : '#e4e4e4',
            programmation,
          });

          console.log();
        });
        console.log(events);
        return events;
      },
      calendar() {
        return this.$refs.calendar.getApi();
      }
    },
    methods: {
      selectHandler(info) {
        this.calendar.unselect();

        this.$emit('select', info);
      },
      clickHandler(info) {
        // if (confirm(`Are you sure you want to delete the event '${clickInfo.event.title}'`)) {
        //   clickInfo.event.remove()
        // }
      }
    }
  }
</script>