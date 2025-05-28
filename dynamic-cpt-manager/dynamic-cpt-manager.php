<?php
/**
 * Plugin Name: Dynamic CPT Manager with Icons & Taxonomies
 * Description: Dynamically register custom post types with category/tag support and icon selection using Dashicons.
 * Version: 1.6
 * Author: Riyadh Mobin
 */

// 1. Admin menu
add_action('admin_menu', function () {
    add_menu_page(
        'Custom CPT Manager',
        'Custom CPTs',
        'manage_options',
        'custom-cpt-manager',
        'custom_cpt_manager_page',
        'dashicons-welcome-widgets-menus'
    );
});

// 2. Display admin form and list
function custom_cpt_manager_page() {
    $custom_cpts = get_option('custom_cpt_definitions', []);
    $dashicons = [
        'admin-post', 'admin-media', 'admin-page', 'admin-comments', 'admin-appearance',
        'admin-plugins', 'admin-users', 'admin-tools', 'admin-settings', 'admin-site',
        'admin-generic', 'admin-home', 'admin-links', 'format-gallery', 'format-image',
        'format-quote', 'format-video', 'format-audio', 'welcome-learn-more', 'awards'
    ];

    // Handle delete
    if (isset($_GET['delete_cpt']) && check_admin_referer('delete_cpt_' . $_GET['delete_cpt'])) {
        $slug_to_delete = sanitize_key($_GET['delete_cpt']);
        $custom_cpts = array_filter($custom_cpts, fn($cpt) => $cpt['slug'] !== $slug_to_delete);
        update_option('custom_cpt_definitions', $custom_cpts);
        wp_redirect(admin_url('admin.php?page=custom-cpt-manager'));
        exit;
    }

    // Handle edit
    $cpt_to_edit = null;
    if (isset($_GET['edit_cpt'])) {
        $slug_to_edit = sanitize_key($_GET['edit_cpt']);
        $cpt_to_edit = array_filter($custom_cpts, fn($cpt) => $cpt['slug'] === $slug_to_edit);
        $cpt_to_edit = reset($cpt_to_edit); // Get the first (and only) matching CPT
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_type'], $_POST['singular'], $_POST['plural'], $_POST['icon'])) {
        if (isset($_GET['edit_cpt'])) {
            // Edit mode: Update the CPT
            $slug_to_edit = sanitize_key($_GET['edit_cpt']);
            foreach ($custom_cpts as &$cpt) {
                if ($cpt['slug'] === $slug_to_edit) {
                    $cpt['singular'] = sanitize_text_field($_POST['singular']);
                    $cpt['plural'] = sanitize_text_field($_POST['plural']);
                    $cpt['icon'] = sanitize_text_field($_POST['icon']);
                    break;
                }
            }
            update_option('custom_cpt_definitions', $custom_cpts);
        } else {
            // Add new CPT
            $custom_cpts[] = [
                'slug' => sanitize_key($_POST['post_type']),
                'singular' => sanitize_text_field($_POST['singular']),
                'plural' => sanitize_text_field($_POST['plural']),
                'icon' => sanitize_text_field($_POST['icon']),
            ];
            update_option('custom_cpt_definitions', $custom_cpts);
        }

        wp_redirect(admin_url('admin.php?page=custom-cpt-manager'));
        exit;
    }

    ?>

    <style>
        *, *:before, *:after {
            box-sizing: border-box;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        body {
            /*background: linear-gradient(to right, #ea1d6f 0%, #eb466b 100%);*/
            font-size: 12px;
        }

        body, button, input {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            letter-spacing: 1.4px;
        }

        .background {
            display: flex;
            min-height: 100vh;
        }

        .container {
            flex: 0 1 700px;
            margin: auto;
            padding-top: 20px;
            padding-right: 20px;
        }

        .screen {
            position: relative;
            background: #3e3e3e;
            border-radius: 15px;
        }

        .screen:after {
            content: '';
            display: block;
            position: absolute;
            top: 0;
            left: 20px;
            right: 20px;
            bottom: 0;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .4);
            z-index: -1;
        }

        .screen-header {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background: #4d4d4f;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .screen-header-left {
            margin-right: auto;
        }

        .screen-header-button {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 3px;
            border-radius: 8px;
            background: white;
        }

        .screen-header-button.close {
            background: #ed1c6f;
        }

        .screen-header-button.maximize {
            background: #e8e925;
        }

        .screen-header-button.minimize {
            background: #74c54f;
        }

        .screen-header-right {
            display: flex;
        }

        .screen-header-ellipsis {
            width: 3px;
            height: 3px;
            margin-left: 2px;
            border-radius: 8px;
            background: #999;
        }

        .screen-body {
            display: flex;
        }

        .screen-body-item {
            flex: 1;
            padding: 50px;
        }

        .screen-body-item.left {
            display: flex;
            flex-direction: column;
        }

        .app-title {
            display: flex;
            flex-direction: column;
            position: relative;
            color: #ea1d6f;
            font-size: 26px;
        }

        .app-title:after {
            content: '';
            display: block;
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 25px;
            height: 4px;
            background: #ea1d6f;
        }

        .app-contact {
            margin-top: auto;
            font-size: 8px;
            color: #888;
        }

        .app-form-group {
            margin-bottom: 15px;
        }

        .app-form-group.message {
            margin-top: 40px;
        }

        .app-form-group.buttons {
            margin-bottom: 0;
            text-align: right;
        }

        .app-form-control {
            width: 100%;
            padding: 10px 0;
            background: none;
            border: none;
            border-bottom: 1px solid #666;
            color: #ddd;
            font-size: 14px;
            /*text-transform: uppercase;*/
            outline: none;
            transition: border-color .2s;
        }

        .app-form-control::placeholder {
            color: #666;
        }

        .app-form-control:focus {
            border-bottom-color: #ddd;
        }

        .app-form-button {
            background: none;
            border: none;
            color: #ea1d6f;
            font-size: 14px;
            cursor: pointer;
            outline: none;
        }

        .app-form-button:hover {
            color: #b9134f;
        }

        .credits {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 20px;
            color: #ffa4bd;
            font-family: 'Roboto Condensed', sans-serif;
            font-size: 16px;
            font-weight: normal;
        }

        .credits-link {
            display: flex;
            align-items: center;
            color: #fff;
            font-weight: bold;
            text-decoration: none;
        }

        .dribbble {
            width: 20px;
            height: 20px;
            margin: 0 5px;
        }

        @media screen and (max-width: 520px) {
            .screen-body {
                flex-direction: column;
            }

            .screen-body-item.left {
                margin-bottom: 30px;
            }

            .app-title {
                flex-direction: row;
            }

            .app-title span {
                margin-right: 12px;
            }

            .app-title:after {
                display: none;
            }
        }

        @media screen and (max-width: 600px) {
            .screen-body {
                padding: 40px;
            }

            .screen-body-item {
                padding: 0;
            }
        }

    </style>

    <div class="">
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
                                    <?php
                                    // Get the number of posts for this CPT
                                    $post_count = wp_count_posts($cpt['slug'])->publish;
                                    ?>
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
                            <h2>Create Custom Post Types</h2>
                            <?php if (isset($_GET['edit_cpt'])): ?>
                                <h2>Edit Custom Post Type</h2>
                            <?php else: ?>
                                <h2>Add New Custom Post Type</h2>
                            <?php endif; ?>

                            <form method="post">
                                <?php wp_nonce_field('save_custom_cpt_definitions'); ?>
                                <table class="form-table">
                                    <tr>
                                        <th><label for="post_type">Post Type Slug</label></th>
                                        <td><input class="app-form-control" name="post_type" id="post_type" value="<?php echo esc_attr($cpt_to_edit['slug'] ?? ''); ?>" required <?php echo isset($_GET['edit_cpt']) ? 'readonly' : ''; ?>></td>
                                    </tr>
                                    <tr>
                                        <th><label for="singular">Singular Name</label></th>
                                        <td><input class="app-form-control" name="singular" id="singular" value="<?php echo esc_attr($cpt_to_edit['singular'] ?? ''); ?>" required></td>
                                    </tr>
                                    <tr>
                                        <th><label for="plural">Plural Name</label></th>
                                        <td><input class="app-form-control" name="plural" id="plural" value="<?php echo esc_attr($cpt_to_edit['plural'] ?? ''); ?>" required></td>
                                    </tr>
                                    <tr>
                                        <th><label for="icon">Menu Icon</label></th>
                                        <td>
                                            <select name="icon" id="icon" style="
            font-family: 'Dashicons', sans-serif;
            font-size: 16px;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #444;
            background-color: #1e1e1e;
            color: #fff;
            width: 100%;
            max-width: 300px;
        ">
                                                <?php foreach ($dashicons as $icon): ?>
                                                    <option value="dashicons-<?php echo esc_attr($icon); ?>" <?php selected($cpt_to_edit['icon'] ?? '', "dashicons-{$icon}"); ?>>
                                                        <?php echo esc_html($icon); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div id="icon-preview" style="
            margin-top: 12px;
            font-size: 28px;
            color: #ea1d6f;
            background: #2a2a2a;
            padding: 10px;
            display: inline-block;
            border-radius: 6px;
        ">
                                                <span class="dashicons <?php echo esc_attr($cpt_to_edit['icon'] ?? $dashicons[0]); ?>"></span>
                                            </div>
                                        </td>
                                    </tr>

                                    <script>
                                        document.addEventListener('DOMContentLoaded', function () {
                                            const select = document.getElementById('icon');
                                            const previewIcon = document.querySelector('#icon-preview .dashicons');
                                            select.addEventListener('change', function () {
                                                previewIcon.className = 'dashicons ' + this.value;
                                            });
                                        });
                                    </script>

                                </table>
                                <p><input type="submit" class="button button-primary" value="<?php echo isset($_GET['edit_cpt']) ? 'Update Custom Post Type' : 'Add Custom Post Type'; ?>"></p>
                            </form>
                            <div class="app-form-group buttons">
                                <button class="app-form-button">CANCEL</button>
                                <button class="app-form-button">SEND</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="credits">
                Developed by<a class="credits-link" href="https://dribbble.com/shots/2666271-Contact" target="_blank">Riyadh Mobin</a>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.getElementById('icon');
            const preview = document.getElementById('icon-preview').querySelector('span');
            select.addEventListener('change', function () {
                preview.className = 'dashicons ' + this.value;
            });
        });
    </script>
    <?php
}

// 3. Save CPTs
add_action('admin_init', function () {
    if (
        isset($_POST['post_type'], $_POST['singular'], $_POST['plural'], $_POST['icon']) &&
        check_admin_referer('save_custom_cpt_definitions')
    ) {
        $custom_cpts = get_option('custom_cpt_definitions', []);
        if (isset($_GET['edit_cpt'])) {
            // Edit mode: Update the CPT
            $slug_to_edit = sanitize_key($_GET['edit_cpt']);
            foreach ($custom_cpts as &$cpt) {
                if ($cpt['slug'] === $slug_to_edit) {
                    $cpt['singular'] = sanitize_text_field($_POST['singular']);
                    $cpt['plural'] = sanitize_text_field($_POST['plural']);
                    $cpt['icon'] = sanitize_text_field($_POST['icon']);
                    break;
                }
            }
            update_option('custom_cpt_definitions', $custom_cpts);
        } else {
            // Add new CPT
            $custom_cpts[] = [
                'slug' => sanitize_key($_POST['post_type']),
                'singular' => sanitize_text_field($_POST['singular']),
                'plural' => sanitize_text_field($_POST['plural']),
                'icon' => sanitize_text_field($_POST['icon']),
            ];
            update_option('custom_cpt_definitions', $custom_cpts);
        }

        wp_redirect(admin_url('admin.php?page=custom-cpt-manager'));
        exit;
    }
});

// 4. Register CPTs & Taxonomies
add_action('init', function () {
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
            'taxonomies' => [ "{$slug}_category", "{$slug}_tag" ],
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
});
