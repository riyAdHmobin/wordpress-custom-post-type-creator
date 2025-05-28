<?php
/**
 * Functions for handling CPT operations
 */

if (!defined('ABSPATH')) exit;

// Handle CPT deletion
function handle_cpt_deletion() {
    if (isset($_GET['delete_cpt']) && check_admin_referer('delete_cpt_' . $_GET['delete_cpt'])) {
        $custom_cpts = get_option('custom_cpt_definitions', []);
        $slug_to_delete = sanitize_key($_GET['delete_cpt']);
        $custom_cpts = array_filter($custom_cpts, fn($cpt) => $cpt['slug'] !== $slug_to_delete);
        update_option('custom_cpt_definitions', $custom_cpts);
        wp_redirect(admin_url('admin.php?page=custom-cpt-manager'));
        exit;
    }
}

// Handle CPT form submission
function handle_cpt_form_submission($cpt_to_edit = null) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_type'], $_POST['singular'], $_POST['plural'], $_POST['icon'])) {
        $custom_cpts = get_option('custom_cpt_definitions', []);

        if (isset($_GET['edit_cpt'])) {
            $slug_to_edit = sanitize_key($_GET['edit_cpt']);
            foreach ($custom_cpts as &$cpt) {
                if ($cpt['slug'] === $slug_to_edit) {
                    $cpt['singular'] = sanitize_text_field($_POST['singular']);
                    $cpt['plural'] = sanitize_text_field($_POST['plural']);
                    $cpt['icon'] = sanitize_text_field($_POST['icon']);
                    break;
                }
            }
        } else {
            $custom_cpts[] = [
                'slug' => sanitize_key($_POST['post_type']),
                'singular' => sanitize_text_field($_POST['singular']),
                'plural' => sanitize_text_field($_POST['plural']),
                'icon' => sanitize_text_field($_POST['icon']),
            ];
        }

        update_option('custom_cpt_definitions', $custom_cpts);
        wp_redirect(admin_url('admin.php?page=custom-cpt-manager'));
        exit;
    }
}

// Register CPTs & Taxonomies
function register_custom_post_types_and_taxonomies() {
    $custom_cpts = get_option('custom_cpt_definitions', []);

    foreach ($custom_cpts as $cpt) {
        $slug = $cpt['slug'];
        $singular = $cpt['singular'];
        $plural = $cpt['plural'];
        $icon = $cpt['icon'] ?? 'dashicons-admin-post';

        register_post_type($slug, [
            'labels' => [
                'name' => $plural,
                'singular_name' => $singular,
                'add_new_item' => 'Add New ' . $singular,
                'edit_item' => 'Edit ' . $singular,
                'new_item' => 'New ' . $singular,
                'view_item' => 'View ' . $singular,
                'search_items' => 'Search ' . $plural,
                'not_found' => 'No ' . $plural . ' found',
                'menu_name' => $plural
            ],
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'menu_icon' => $icon,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'taxonomies' => ["{$slug}_category", "{$slug}_tag"],
        ]);

        register_taxonomy("{$slug}_category", $slug, [
            'labels' => [
                'name' => $singular . ' Categories',
                'singular_name' => $singular . ' Category',
                'menu_name' => $singular . ' Categories',
            ],
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'rewrite' => ['slug' => "{$slug}-category"],
        ]);

        register_taxonomy("{$slug}_tag", $slug, [
            'labels' => [
                'name' => $singular . ' Tags',
                'singular_name' => $singular . ' Tag',
                'menu_name' => $singular . ' Tags',
            ],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'rewrite' => ['slug' => "{$slug}-tag"],
        ]);
    }
}
add_action('init', 'register_custom_post_types_and_taxonomies');
