<?php
/**
 * Mobile Header
 *
 * @package Salient Child Theme
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function inject_mobile_header() {
    ?>
    <div class="mobile-header-wrapper">
        <!-- Logo container -->
        <div class="mobile-logo-container">
            <a href="<?php echo esc_url(home_url()); ?>">
                <?php 
                // You can replace this with actual logo
                // For now, it's just a placeholder
                ?>
            </a>
        </div>
        
        <!-- Actions container for search and menu -->
        <div class="mobile-actions-container">
            <a class="mobile-search" href="#searchbox">
                <span class="nectar-icon icon-salient-search" aria-hidden="true"></span>
                <span class="screen-reader-text"><?php echo esc_html__('search','salient'); ?></span>
            </a>
            
            <div class="slide-out-widget-area-toggle mobile-icon slide-out-from-right" data-custom-color="false" data-icon-animation="simple-transform">
                <div>
                    <a href="#slide-out-widget-area" role="button" aria-label="<?php echo esc_attr__('Navigation Menu', 'salient'); ?>" aria-expanded="false" class="closed">
                        <span class="screen-reader-text"><?php echo esc_html__('Menu','salient'); ?></span>
                        <span aria-hidden="true">
                            <i class="lines-button x2">
                                <i class="lines"></i>
                            </i>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <script>
    // Ensure mobile header is at the very top
    document.addEventListener('DOMContentLoaded', function() {
        var mobileHeader = document.querySelector('.mobile-header-wrapper');
        if (mobileHeader && window.innerWidth <= 767) {
            document.body.insertBefore(mobileHeader, document.body.firstChild);
        }
    });
    </script>
    <?php
}

// Hook the mobile header to wp_body_open with high priority
add_action('wp_body_open', 'inject_mobile_header', 1);

// Alternative hook if wp_body_open doesn't work
add_action('wp_head', function() {
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.innerWidth <= 767) {
            // Force sticky behavior
            var mobileHeader = document.querySelector('.mobile-header-wrapper');
            if (mobileHeader) {
                mobileHeader.style.position = 'fixed';
                mobileHeader.style.top = '0';
                mobileHeader.style.zIndex = '9999';
                document.body.style.paddingTop = '100px';
            }
        }
    });
    </script>
    <?php
}); 