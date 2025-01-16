<?php
/*
Plugin Name: Lock Post
Description: Add a "Lock Post" feature to prevent certain posts from being trashed or deleted.
Version: 1.1
Author: Christine Djerf @skvaller
*/

// Add a "Lock Post" checkbox to the post editor
function add_lock_post_meta_box() {
    add_meta_box('lock_post_meta', 'Lock Post', 'lock_post_meta_callback', 'post', 'side');
}
add_action('add_meta_boxes', 'add_lock_post_meta_box');

function lock_post_meta_callback($post) {
    $is_locked = get_post_meta($post->ID, '_lock_post', true);
    ?>
    <label>
        <input type="checkbox" name="lock_post" <?php checked($is_locked, 'yes'); ?> />
        Lock this post (prevent trash or deletion)
    </label>
    <?php
}

// Save the "Lock Post" metadata
function save_lock_post_meta($post_id) {
    if (isset($_POST['lock_post'])) {
        update_post_meta($post_id, '_lock_post', 'yes');
    } else {
        delete_post_meta($post_id, '_lock_post');
    }
}
add_action('save_post', 'save_lock_post_meta');

// Remove "Trash" option for locked posts
function disable_trash_for_locked_posts($actions, $post) {
    if (get_post_meta($post->ID, '_lock_post', true) === 'yes') {
        unset($actions['trash']); // Remove the Trash option
    }
    return $actions;
}
add_filter('post_row_actions', 'disable_trash_for_locked_posts', 10, 2);

// Prevent locked posts from being moved to Trash programmatically
function prevent_locked_post_trash($post_id) {
    if (get_post_meta($post_id, '_lock_post', true) === 'yes') {
        wp_die('This post is locked and cannot be moved to the Trash.');
    }
}
add_action('wp_trash_post', 'prevent_locked_post_trash');
