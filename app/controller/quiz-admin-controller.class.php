<?php

namespace wpQuizme\controller;

class quizAdminController {
  public function __construct() {
    add_action('add_meta_boxes', [$this, 'create_metabox']);
    add_action('save_post', [$this, 'save_metabox'], 1, 2);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
  }

  public function create_metabox() {
    add_meta_box(
      'quizme_quiz_metabox',
      'Quiz data',
      [$this, 'render_metabox'],
      'quiz',
      'normal',
      'default'
    );
  }

  public function save_metabox($post_id, $post) {
    if (!isset($_POST['quizme_form_metabox_process'])) {
      return;
    }
    if (!wp_verify_nonce($_POST['quizme_form_metabox_process'], 'quizme_form_metabox_nonce')) {
      return $post->ID;
    }
    if (!current_user_can('edit_post', $post->ID)) {
      return $post->ID;
    }

    $json = $_POST['quizme-json'];
    update_post_meta( $post->ID, 'quizme_json', $json );
  }

  public function render_metabox() {
    global $post;
    wp_nonce_field('quizme_form_metabox_nonce', 'quizme_form_metabox_process');
    $val = get_post_meta($post->ID, 'quizme_json', true);
    echo '<div>';
    echo '<strong>Shortcode:</strong>';
    if ($post->ID) {
      echo '<pre>[quizme id="' . $post->ID . '"]</pre>';
    } else {
      echo 'Save your quiz to generate the shortcode.';
    }
    echo '</div>';
    echo '<div>';
    echo '<strong>Config (JSON):</strong>';
    echo '<textarea id="quizme-json" name="quizme-json">' . esc_textarea($val) . '</textarea>';
    echo '</div>';
    echo '
    <script>
    jQuery(document).ready(function($) {
      wp.codeEditor.initialize($("#quizme-json"), cm_settings);
    })
    </script>
    <style>
    .CodeMirror {
      border: 1px solid #eee;
      height: auto;
    }
    </style>
    ';
  }

  public function enqueue_scripts($hook) {
    $is_quiz_page = in_array($hook, ['post-new.php', 'post.php']) && get_post_type() == 'quiz';
    if (!$is_quiz_page) {
      return;
    }

    $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'application/json'));
    wp_localize_script('jquery', 'cm_settings', $cm_settings);

    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
  }
}
