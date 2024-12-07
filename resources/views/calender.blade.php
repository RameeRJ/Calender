<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

  <script type='importmap'>
    {
      "imports": {
        "@fullcalendar/core": "https://cdn.skypack.dev/@fullcalendar/core@6.1.15",
        "@fullcalendar/daygrid": "https://cdn.skypack.dev/@fullcalendar/daygrid@6.1.15",
        "@fullcalendar/interaction": "https://cdn.skypack.dev/@fullcalendar/interaction@6.1.15"
      }
    }
  </script>
  <script type='module'>
    import { Calendar } from '@fullcalendar/core';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import interactionPlugin from '@fullcalendar/interaction';

    document.addEventListener('DOMContentLoaded', function () {
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const calendarEl = document.getElementById('calendar');
  const calendar = new Calendar(calendarEl, {
    plugins: [dayGridPlugin, interactionPlugin],
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    editable: true, // Allow drag and drop
    selectable: true, // Allow selection
    events: '/events', // Load events dynamically from backend

    // Add event on date click
    dateClick: function (info) {
      const title = prompt('Enter event title:');
      if (title) {
        fetch('/events', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token
          },
          body: JSON.stringify({
            title: title,
            start: info.dateStr,
            end: info.dateStr,
          }),
        })
        .then(response => response.json())
        .then(event => {
          calendar.addEvent(event); // Add to the calendar
          alert('Event added successfully!');
        })
        .catch(error => {
          console.error('Error adding event:', error);
          alert('Failed to add event.');
        });
      }
    },

    // Update event on drag/drop
    eventDrop: function (info) {
      const event = info.event;
      fetch(`/events/${event.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({
          title: event.title,
          start: event.startStr,
          end: event.endStr,
        }),
      })
      .then(response => response.json())
      .then(() => {
        alert('Event updated successfully!');
      })
      .catch(error => {
        console.error('Error updating event:', error);
        alert('Failed to update event.');
        info.revert(); // Revert changes on failure
      });
    },

    // Delete event on click
    eventClick: function (info) {
      if (confirm('Do you want to delete this event?')) {
        fetch(`/events/${info.event.id}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': token,
          },
        })
        .then(response => response.json())
        .then(() => {
          info.event.remove(); // Remove from calendar
          alert('Event deleted successfully!');
        })
        .catch(error => {
          console.error('Error deleting event:', error);
          alert('Failed to delete event.');
        });
      }
    },
  });

  calendar.render();
});

  </script>
</head>
<body>
  <div id="calendar"></div>
</body>
</html>