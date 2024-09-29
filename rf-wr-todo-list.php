<?php
/*
Plugin Name: RF WR Todo List
Description: Frontend todo list for WordPress.
Version: 1.0.0
Contributors: rakifsul
Author: RAKIFSUL
Author URI: https://rakifsul.taplink.ws
License: GPLv2 or later
Text Domain: rf-wr-plugin
*/

function create_todo_post_type() {
    $labels = array(
        'name' => 'Todos',
        'singular_name' => 'Todo',
        'menu_name' => 'Todos',
        'name_admin_bar' => 'Todo',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Todo',
        'new_item' => 'New Todo',
        'edit_item' => 'Edit Todo',
        'view_item' => 'View Todo',
        'all_items' => 'All Todos',
        'search_items' => 'Search Todos',
        'not_found' => 'No todos found.',
        'not_found_in_trash' => 'No todos found in Trash.',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'has_archive' => true,
        'supports' => array( 'title', 'custom-fields' ),
        'rewrite' => array( 'slug' => 'todos' ),
    );

    register_post_type('todo', $args);
}
add_action('init', 'create_todo_post_type');

function todo_list_shortcode() {
	ob_start();
    ?>

    <table>
        <tr>
            <th>Name</th>
            <th>Description</th>
        </tr>
    <?php
    $args = array(
		'post_type' => 'todo',
		'posts_per_page' => 10,
	);
	$todo_query = new WP_Query($args);
	if ($todo_query->have_posts()) :
		while ($todo_query->have_posts()) : $todo_query->the_post();
            ?>
            
            <tr>
                <td><?= get_the_title() ?></td>
                <td><?= get_post_meta(get_the_ID(), 'description', true) ?></td>
            </tr>
            
            <?php
		endwhile;
		wp_reset_postdata();
	else :
		echo 'No todos available.';
	endif;
    ?>
    </table>
    <?php
	return ob_get_clean();
}
add_shortcode('todo_list', 'todo_list_shortcode');

function todo_submission_form_shortcode() {
    ob_start();
    ?>
    <!-- Form HTML di sini -->
    <form method="post" action="">
        <p><label for="title">Todo Title:</label></p>
        <p><input type="text" id="title" name="title" style="width:100%" required></p>
        
        <p><label for="description">Todo Description:</label></p>
        <p><textarea id="description" name="description" style="width:100%" rows="10" required></textarea></p>
        
        <p><button type="submit" name="submit_todo">Submit Todo</button></p>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('todo_submission_form', 'todo_submission_form_shortcode');

function handle_todo_submission() {
    if (isset($_POST['submit_todo'])) {
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);

        // Insert the car as a new post
        $new_todo = array(
            'post_title'   => $title,
            'post_status'  => 'publish',
            'post_type'    => 'todo'
        );
        $post_id = wp_insert_post($new_todo);

        // Add custom field for price
        if ($post_id) {
            update_post_meta($post_id, 'description', $description);
        }
    }
}
add_action('init', 'handle_todo_submission');