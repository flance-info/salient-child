<?php
if (!defined('ABSPATH')) exit;

class NAI_Currency_Widget {
    public function __construct() {
        add_action('vc_before_init', [$this, 'integrate_with_vc']);
        add_shortcode('nai_currency_widget', [$this, 'render_widget']);
        add_action('wp_ajax_nai_get_currency_rates', [$this, 'get_currency_rates']);
        add_action('wp_ajax_nopriv_nai_get_currency_rates', [$this, 'get_currency_rates']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_head', [$this, 'add_ajax_url']);
    }

    public function enqueue_assets() {
        wp_enqueue_style('nai-currency-widget', get_stylesheet_directory_uri() . '/assets/css/nai-currency-widget.css', array(), '1.0.0', 'all');
        wp_enqueue_script('nai-currency-widget', get_stylesheet_directory_uri() . '/assets/js/nai-currency-widget.js', array('jquery'), '1.0.0', true);
    }

    public function add_ajax_url() {
        ?>
        <script type="text/javascript">
        var nai_currency_ajax = {
            ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>'
        };
        </script>
        <?php
    }

    public function integrate_with_vc() {
        vc_map([
            'name' => 'NAI Currency Widget',
            'base' => 'nai_currency_widget',
            'category' => 'NAI Elements',
            'icon' => 'vc_icon-vc-pie',
            'description' => 'Display currency exchange rates from CBU',
            'params' => [
                [
                    'type' => 'textfield',
                    'heading' => 'Title',
                    'param_name' => 'title',
                    'value' => 'Курс валют',
                    'description' => 'Widget title',
                ],
                [
                    'type' => 'dropdown',
                    'heading' => 'Show Flags',
                    'param_name' => 'show_flags',
                    'value' => [
                        'Yes' => 'yes',
                        'No' => 'no'
                    ],
                    'std' => 'yes',
                    'description' => 'Display country flags',
                ],
                [
                    'type' => 'dropdown',
                    'heading' => 'Show Changes',
                    'param_name' => 'show_changes',
                    'value' => [
                        'Yes' => 'yes',
                        'No' => 'no'
                    ],
                    'std' => 'yes',
                    'description' => 'Display rate changes',
                ],
                [
                    'type' => 'textfield',
                    'heading' => 'Refresh Interval (seconds)',
                    'param_name' => 'refresh_interval',
                    'value' => '300',
                    'description' => 'Auto refresh interval in seconds (0 to disable)',
                ],
                [
                    'type' => 'colorpicker',
                    'heading' => 'Background Color',
                    'param_name' => 'bg_color',
                    'description' => 'Widget background color',
                ],
                [
                    'type' => 'colorpicker',
                    'heading' => 'Text Color',
                    'param_name' => 'text_color',
                    'description' => 'Widget text color',
                ],
            ],
        ]);
    }

    public function render_widget($atts) {
        $atts = shortcode_atts([
            'title' => 'Курс валют',
            'show_flags' => 'yes',
            'show_changes' => 'yes',
            'refresh_interval' => '300',
            'bg_color' => '',
            'text_color' => '',
        ], $atts);

        $style = '';
        if (!empty($atts['bg_color'])) {
            $style .= 'background-color: ' . esc_attr($atts['bg_color']) . ';';
        }
        if (!empty($atts['text_color'])) {
            $style .= 'color: ' . esc_attr($atts['text_color']) . ';';
        }

        ob_start();
        ?>
        <div class="nai-currency-widget" style="<?php echo $style; ?>">
            <h3><?php echo esc_html($atts['title']); ?></h3>
            <div class="currency-list" data-refresh="<?php echo esc_attr($atts['refresh_interval']); ?>">
                <div class="currency-loading">Загрузка курсов валют...</div>
            </div>
        </div>

        <style>
            .nai-currency-widget {
    padding: 20px;
    border-radius: 8px;
    margin: 20px 0;
}

.nai-currency-widget h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    font-weight: 600;
}

.currency-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.currency-item {
    display: flex;
    flex-direction: column;
    padding: 15px;
    border: 1px solid #eee;
    border-radius: 8px;
    background: white;
    transition: all 0.3s ease;
}

.currency-item:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.currency-header {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.flag {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background-size: cover;
    display: inline-block;
    margin-right: 10px;
    overflow: hidden;
}

.flag.us {
    background-image: url('https://flagcdn.com/w80/us.png');
    background-position: center;
}

.flag.eu {
    background-image: url('https://flagcdn.com/w80/eu.png');
    background-position: center;
}

.flag.ru {
    background-image: url('https://flagcdn.com/w80/ru.png');
    background-position: center;
}

.flag.uz {
    background-image: url('https://flagcdn.com/w80/uz.png');
    background-position: center;
}

.code {
    font-weight: bold;
    font-size: 16px;
    color: #333;
}

.rate-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.rate {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.change {
    font-size: 14px;
    font-weight: 500;
    padding: 4px 8px;
    border-radius: 4px;
}

.change.up {
    color: #4CAF50;
    background: rgba(76, 175, 80, 0.1);
}

.change.down {
    color: #F44336;
    background: rgba(244, 67, 54, 0.1);
}

.change.neutral {
    color: #9E9E9E;
    background: rgba(158, 158, 158, 0.1);
}

.currency-loading {
    text-align: center;
    padding: 40px;
    color: #666;
    font-style: italic;
}

.currency-error {
    text-align: center;
    padding: 40px;
    color: #F44336;
    background: rgba(244, 67, 54, 0.1);
    border-radius: 8px;
}

@media (max-width: 768px) {
    .currency-list {
        grid-template-columns: 1fr;
    }
} 
            </style>
        <?php
        return ob_get_clean();
    }

    public function get_currency_rates() {
        $response = wp_remote_get('https://cbu.uz/uz/arkhiv-kursov-valyut/json/');
        
        if (is_wp_error($response)) {
            wp_send_json_error('Failed to fetch currency data');
            return;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !is_array($data)) {
            wp_send_json_error('Invalid currency data');
            return;
        }

        // Define currencies to display
        $currencies = [
            'USD' => 'us',
            'EUR' => 'eu', 
            'RUB' => 'ru',
            'UZS' => 'uz'
        ];

        ob_start();
        foreach ($currencies as $code => $flag) {
            $currency_data = null;
            foreach ($data as $currency) {
                if ($currency['Ccy'] == $code) {
                    $currency_data = $currency;
                    break;
                }
            }

            if ($currency_data) {
                $rate = number_format((float)$currency_data['Rate'], 2, ',', ' ');
                $diff = isset($currency_data['Diff']) ? (float)$currency_data['Diff'] : 0;
                
                $change_class = 'neutral';
                $change_text = '0.00';
                
                if ($diff > 0) {
                    $change_class = 'up';
                    $change_text = '+' . number_format($diff, 2, ',', ' ');
                } elseif ($diff < 0) {
                    $change_class = 'down';
                    $change_text = number_format($diff, 2, ',', ' ');
                }
                ?>
                <div class="currency-item">
                    <div class="currency-header">
                        <span class="flag <?php echo esc_attr($flag); ?>"></span>
                        <span class="code"><?php echo esc_html($code); ?></span>
                    </div>
                    <div class="rate-container">
                        <span class="rate"><?php echo esc_html($rate); ?></span>
                        <span class="change <?php echo esc_attr($change_class); ?>"><?php echo esc_html($change_text); ?></span>
                    </div>
                </div>
                <?php
            }
        }
        
        $html = ob_get_clean();

        if (empty($html)) {
            wp_send_json_error('No currency data available');
            return;
        }

        wp_send_json_success(['html' => $html]);
    }
}

new NAI_Currency_Widget(); 