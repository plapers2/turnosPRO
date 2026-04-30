{{-- resources/views/livewire/appointments/calendar/view.blade.php --}}
{{-- Props: $calendarEvents (array) --}}
<div
    x-init="
        $watch('$el.offsetParent', v => { if (v) initCalendar(@js($calendarEvents)) });
    "
    @calendar-events-updated.window="refreshCalendar($event.detail.events)"
>
    <div id="fullcalendar" class="calendar-wrapper"></div>
</div>
