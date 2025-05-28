<?php
/**
 * Functions related to Dashicons handling
 */

if (!defined('ABSPATH')) exit;

function get_available_dashicons() {
    return [
        'admin-post', 'admin-media', 'admin-page', 'admin-comments', 'admin-appearance',
        'admin-plugins', 'admin-users', 'admin-tools', 'admin-settings', 'admin-site',
        'admin-generic', 'admin-home', 'admin-links', 'format-gallery', 'format-image',
        'format-quote', 'format-video', 'format-audio', 'welcome-learn-more', 'awards'
    ];
}
