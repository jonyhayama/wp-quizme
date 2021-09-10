<?php

namespace wpQuizme\model;

class quiz {
  public function __construct() {
    add_action('init', [$this, 'register_post_type'], 0);
  }

  public function register_post_type() {
    $labels = array(
      'name'                  => _x('Quizzes', 'Post Type General Name', 'wp-quizme'),
      'singular_name'         => _x('Quiz', 'Post Type Singular Name', 'wp-quizme'),
      'menu_name'             => __('Quizzes', 'wp-quizme'),
      'name_admin_bar'        => __('Quiz', 'wp-quizme'),
      'archives'              => __('Quiz Archives', 'wp-quizme'),
      'attributes'            => __('Quiz Attributes', 'wp-quizme'),
      'parent_item_colon'     => __('Parent Quiz:', 'wp-quizme'),
      'all_items'             => __('All Quizzes', 'wp-quizme'),
      'add_new_item'          => __('Add New Quiz', 'wp-quizme'),
      'add_new'               => __('Add New', 'wp-quizme'),
      'new_item'              => __('New Quiz', 'wp-quizme'),
      'edit_item'             => __('Edit Quiz', 'wp-quizme'),
      'update_item'           => __('Update Quiz', 'wp-quizme'),
      'view_item'             => __('View Quiz', 'wp-quizme'),
      'view_items'            => __('View Quizzes', 'wp-quizme'),
      'search_items'          => __('Search Quiz', 'wp-quizme'),
      'not_found'             => __('Not found', 'wp-quizme'),
      'not_found_in_trash'    => __('Not found in Trash', 'wp-quizme'),
      'featured_image'        => __('Featured Image', 'wp-quizme'),
      'set_featured_image'    => __('Set featured image', 'wp-quizme'),
      'remove_featured_image' => __('Remove featured image', 'wp-quizme'),
      'use_featured_image'    => __('Use as featured image', 'wp-quizme'),
      'insert_into_item'      => __('Insert into item', 'wp-quizme'),
      'uploaded_to_this_item' => __('Uploaded to this item', 'wp-quizme'),
      'items_list'            => __('Quizzes list', 'wp-quizme'),
      'items_list_navigation' => __('Quizzes list navigation', 'wp-quizme'),
      'filter_items_list'     => __('Filter items list', 'wp-quizme'),
    );
    $args = array(
      'label'                 => __('Quiz', 'wp-quizme'),
      'description'           => __('Quizzes', 'wp-quizme'),
      'labels'                => $labels,
      'supports'              => array('title'),
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => false,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'rewrite'               => false,
      'capability_type'       => 'post',
    );

    register_post_type('quiz', $args);
  }
}
