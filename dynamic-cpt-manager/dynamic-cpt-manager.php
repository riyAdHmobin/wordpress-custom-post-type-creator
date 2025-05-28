<?php
/**
 * Plugin Name: Dynamic CPT Manager with Icons & Taxonomies
 * Description: Dynamically register custom post types with category/tag support and icon selection using Dashicons.
 * Version: 1.6
 * Author: Riyadh Mobin
 */

if (!defined('ABSPATH')) exit;

// Include required files
require_once plugin_dir_path(__FILE__) . 'includes/icons.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'includes/cpt-handlers.php';

// Display admin form and list
function custom_cpt_manager_page() {
    $custom_cpts = get_option('custom_cpt_definitions', []);
    $dashicons = get_available_dashicons();

    // Handle delete and form submissions
    handle_cpt_deletion();

    // Handle edit
    $cpt_to_edit = null;
    if (isset($_GET['edit_cpt'])) {
        $slug_to_edit = sanitize_key($_GET['edit_cpt']);
        $cpt_to_edit = array_filter($custom_cpts, fn($cpt) => $cpt['slug'] === $slug_to_edit);
        $cpt_to_edit = reset($cpt_to_edit);
    }

    handle_cpt_form_submission($cpt_to_edit);
    ?>
    <div class="container">
        <div class="screen">
            <div class="screen-body">
                <div class="screen-body-item left">
                    <div class="app-title">
                        <span>Custom Post Type</span>
                        <span>Generator</span>
                    </div>
                    <div>
                        <h2>Registered CPTs</h2>
                        <table class="widefat">
                            <thead>
                            <tr>
                                <th>Icon</th>
                                <th>Slug</th>
                                <th>Name</th>
                                <th>Number of Posts</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($custom_cpts as $cpt): ?>
                                <?php $post_count = wp_count_posts($cpt['slug'])->publish; ?>
                                <tr>
                                    <td><span class="dashicons <?php echo esc_attr($cpt['icon']); ?>"></span></td>
                                    <td><strong><?php echo esc_html($cpt['slug']); ?></strong></td>
                                    <td><?php echo esc_html($cpt['singular']); ?> / <?php echo esc_html($cpt['plural']); ?></td>
                                    <td><?php echo esc_html($post_count); ?></td>
                                    <td>
                                        <a href="<?php echo wp_nonce_url(admin_url("admin.php?page=custom-cpt-manager&edit_cpt={$cpt['slug']}"), 'edit_cpt_' . $cpt['slug']); ?>" class="button button-small">Edit</a>
                                        <a href="<?php echo wp_nonce_url(admin_url("admin.php?page=custom-cpt-manager&delete_cpt={$cpt['slug']}"), 'delete_cpt_' . $cpt['slug']); ?>" class="button button-small" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="app-contact">CONTACT INFO : +62 81 314 928 595</div>
                </div>
                <div class="screen-body-item">
                    <div class="app-form">
                        <h2><?php echo isset($_GET['edit_cpt']) ? 'Edit Custom Post Type' : 'Add New Custom Post Type'; ?></h2>
                        <form method="post">
                            <?php wp_nonce_field('save_custom_cpt_definitions'); ?>
                            <table class="form-table
