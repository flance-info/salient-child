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
        <?php
                $mobile_logo = get_theme_mod('mobile_logo');
              
            
                if ($mobile_logo) {
                    echo '<a href="' . esc_url(home_url()) . '">';
                    echo '<img src="' . esc_url($mobile_logo) . '" alt="' . esc_attr(get_bloginfo('name')) . '" class="mobile-logo-img" />';
                    echo '</a>';
                } elseif (function_exists('the_custom_logo') && has_custom_logo()) {
                    echo '<a href="' . esc_url(home_url()) . '">';
                    the_custom_logo();
                    echo '</a>';
                }else{
                ?>
        <div class="mobile-logo-container">
            <a href="<?php echo esc_url(home_url()); ?>">
                    <span class="site-title"><?php echo get_bloginfo('name'); ?></span>
                
            </a>
        </div>
        <?php
                }
        ?>
        
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
    <div class="mobile-search-overlay" id="mobile-search-overlay">
        <div class="mobile-search-box">
        <div class="search-bar-wrapper">
						<div id="search-2" class="widget widget_search">
							<form role="search" method="get" class="search-form" action="https://nai.test/">
								<input type="text" class="search-field" placeholder="Search..." value="" name="s" title="Search for:">
								<button type="submit" class="search-widget-btn">
									<span class="normal icon-salient-search" aria-hidden="true"></span>
									<span class="text">Search</span>
								</button>
							</form>
						</div>
					</div>
          
            <div  class="mobile-search-close" id="mobile-search-close">&times;</button>     
      
       
       
        </div>

    </div>
    <script>
    // Ensure mobile header is at the very top
    document.addEventListener('DOMContentLoaded', function() {
        var mobileHeader = document.querySelector('.mobile-header-wrapper');
        if (mobileHeader && window.innerWidth <= 767) {
            document.body.insertBefore(mobileHeader, document.body.firstChild);
        }

        // Mobile search overlay toggle
        var searchBtn = document.querySelector('.mobile-search');
        var overlay = document.getElementById('mobile-search-overlay');
        var closeBtn = document.getElementById('mobile-search-close');
        var input = document.getElementById('mobile-search-input');
        if (searchBtn && overlay && closeBtn) {
            searchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                overlay.classList.add('active');
                setTimeout(function() {
                    input && input.focus();
                }, 100);
            });
            closeBtn.addEventListener('click', function() {
                overlay.classList.remove('active');
            });
        }
    });
    </script>
    <?php
}

// Hook the mobile header to wp_body_open with high priority
add_action('header_menu', 'inject_mobile_header', 1);

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