<template>
  <div id="programmation-calendar-component">
    <full-calendar :options="options" :events="events" ref="calendar">
      <template v-slot:eventContent='item'>
        <programmation-actions :event="item.event"></programmation-actions>
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

  export default {
    components: { FullCalendar },
    data: () => ({}),
    props: {
      programmations: [],
      date: '',
    },
    watch: {
      date() {
        this.calendar.gotoDate(this.date);
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
          let isDark = this.isDarkColor(programmation.category.color);

          events.push({
            allDay: true,
            title: programmation.title,
            start: `${programmation.start_date}T${programmation.start_time}`,
            end: programmation.end_date ? `${programmation.end_date}T${programmation.end_time}` : `${moment(this.date).endOf('month').format('YYYY-MM-DD')}T${programmation.end_time}`,
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
      isDarkColor(color) {
        let r, g, b, hsp;

        if (color.match(/^rgb/)) {
          color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/);
          
          r = color[1];
          g = color[2];
          b = color[3];
        } else {
          color = +("0x" + color.slice(1).replace( 
          color.length < 5 && /./g, '$&$&'));

          r = color >> 16;
          g = color >> 8 & 255;
          b = color & 255;
        }

        hsp = Math.sqrt(0.299 * (r * r) + 0.587 * (g * g) + 0.114 * (b * b));

        if (hsp > 180) return false;

        return true;
      },
      overlapHandler(stillEvent, movingEvent) {
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
        // Open dialog for edit
      },
      changeHandler(info) {
        // Save event async
      }
    }
  }
</script>