jQuery(document).ready(function($) {
    // Initialize currency widgets
    $('.nai-currency-widget').each(function() {
        var $widget = $(this);
        var $currencyList = $widget.find('.currency-list');
        var refreshInterval = parseInt($currencyList.data('refresh')) || 300;
        
        function loadCurrencyRates() {
            $.ajax({
                url: nai_currency_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'nai_get_currency_rates'
                },
                success: function(response) {
                    if (response.success) {
                        $currencyList.html(response.data.html);
                    } else {
                        $currencyList.html('<div class="currency-error">Ошибка загрузки курсов валют</div>');
                    }
                },
                error: function() {
                    $currencyList.html('<div class="currency-error">Ошибка загрузки курсов валют</div>');
                }
            });
        }

        // Load initial data
        loadCurrencyRates();

        // Auto refresh if interval is set
        if (refreshInterval > 0) {
            setInterval(loadCurrencyRates, refreshInterval * 1000);
        }
    });
}); 