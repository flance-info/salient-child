jQuery(document).ready(function($) {
    // Translation object (replace with localized values if needed)
    var naiCalendarI18n = window.naiCalendarI18n || {
        months: [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ],
        days: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
    };

    function renderCalendar($container, year, month) {
        const monthNames = naiCalendarI18n.months;
        const daysShort = naiCalendarI18n.days;
        const today = new Date();
        const isCurrentMonth = (today.getFullYear() === year && today.getMonth() === month);

        // Set month label
        $container.find('.month-label').text(monthNames[month] + ', ' + year);

        // First day of month (0=Sun, 1=Mon, ...)
        const firstDay = new Date(year, month, 1);
        let startDay = firstDay.getDay();
        startDay = startDay === 0 ? 6 : startDay - 1; // Make Monday first

        // Days in month
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        // Days in previous month
        const daysInPrevMonth = new Date(year, month, 0).getDate();

        // Build grid
        let html = '';
        for (let d = 0; d < 7; d++) {
            html += `<div class="day-header">${daysShort[d]}</div>`;
        }
        // Previous month's days
        for (let i = 0; i < startDay; i++) {
            html += `<div class="day-cell different-month">${daysInPrevMonth - startDay + i + 1}</div>`;
        }
        // Current month's days
        for (let d = 1; d <= daysInMonth; d++) {
            let classes = 'day-cell';
            if (isCurrentMonth && d === today.getDate()) classes += ' today';
            html += `<div class="${classes}">${d}</div>`;
        }
        // Next month's days
        let totalCells = startDay + daysInMonth;
        let nextDays = (7 - (totalCells % 7)) % 7;
        for (let i = 1; i <= nextDays; i++) {
            html += `<div class="day-cell different-month">${i}</div>`;
        }
        $container.find('.calendar-grid').html(html);
    }

    $('.nai-calendar-widget').each(function() {
        const $container = $(this);
        let date = new Date();
        let year = date.getFullYear();
        let month = date.getMonth();

        function update() {
            renderCalendar($container, year, month);
        }
        update();

        $container.on('click', '.month-nav', function() {
            if ($(this).data('dir') === 'prev') {
                month--;
                if (month < 0) { month = 11; year--; }
            } else {
                month++;
                if (month > 11) { month = 0; year++; }
            }
            update();
        });
    });
}); 