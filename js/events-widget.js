jQuery(document).ready(function($) {
    const eventsWidget = $('.nai-events-widget');
    
    // Handle year selection
    eventsWidget.on('click', '.nai-events-year-options a', function(e) {
        e.preventDefault();
        const year = $(this).data('year');
        const tab = $('.nai-events-tab.active').data('tab');
        loadEvents(year, tab);
    });

    // Handle tab switching
    eventsWidget.on('click', '.nai-events-tab', function(e) {
        e.preventDefault();
        const tab = $(this).data('tab');
        const year = $('.nai-events-year').text();
        loadEvents(year, tab);
    });

    function loadEvents(year, tab) {
        const eventsList = $('.nai-events-list');
        const pagination = $('.nai-events-pagination');
        
        // Show loading state
        eventsList.addClass('loading');
        
        // Make AJAX request
        $.ajax({
            url: nai_events.ajax_url,
            type: 'POST',
            data: {
                action: 'load_events',
                year: year,
                tab: tab,
                nonce: nai_events.nonce
            },
            success: function(response) {
                if (response.success) {
                    // Update events list
                    eventsList.html(response.data.html);
                    
                    // Update pagination if exists
                    if (response.data.pagination) {
                        pagination.html(response.data.pagination).show();
                    } else {
                        pagination.hide();
                    }
                    
                    // Update active states
                    $('.nai-events-year').text(year);
                    $('.nai-events-year-options a').removeClass('active')
                        .filter('[data-year="' + year + '"]').addClass('active');
                    $('.nai-events-tab').removeClass('active')
                        .filter('[data-tab="' + tab + '"]').addClass('active');
                }
            },
            complete: function() {
                eventsList.removeClass('loading');
            }
        });
    }
}); 