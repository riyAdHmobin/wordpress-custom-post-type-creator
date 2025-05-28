<?php
/**
 * Admin menu registration and related functions
 */

if (!defined('ABSPATH')) exit;

function register_cpt_manager_menu() {
    add_menu_page(
        'Custom CPT Manager',
        'Custom CPTs',
        'manage_options',
        'custom-cpt-manager',
        'custom_cpt_manager_page',
        'dashicons-welcome-widgets-menus'
    );
}
add_action('admin_menu', 'register_cpt_manager_menu');
