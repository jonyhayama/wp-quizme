<?php

namespace wpQuizme\controller;

use \Google;

class settingsController {
  public function __construct() {
    add_action('admin_menu', [$this, 'add_submenu_page']);
    add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

    add_action('wp_ajax_quizme/authorize-credentials', [$this, 'authorize_credentials_ajax']);
  }

  public function add_submenu_page() {
    $parent_slug = 'edit.php?post_type=quiz';
    $page_title = 'Settings';
    $menu_title = 'Settings';
    $capability = 'manage_options';
    $menu_slug = 'quizme-settings';
    $function = [$this, 'render'];
    $position = 5;
    add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function, $position );
  }

  public function authorize_credentials_ajax() {
    $settings = get_option('quizme_settings');

    $credentials = json_decode($settings['credentials'], true);
    $accessToken = $settings['access_token'] ?? null;

    if(!$credentials) {
      echo "Set credentials!";
      exit;
    }

    if($accessToken) {
      echo "Already authorized";
      exit;
    }

    $client = wp_quizme('googleSheetsService')->buildClient();
    $client->setAuthConfig($credentials);

    if(!isset($_GET['code'])){
      $authUrl = $client->createAuthUrl();
      wp_redirect($authUrl);
    } else {
      $authCode = $_GET['code'];
      $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
      if (array_key_exists('error', $accessToken)) {
        throw new \Exception(join(', ', $accessToken));
      }

      $client->setAccessToken($accessToken);

      $settings['access_token'] = json_encode($client->getAccessToken());
      update_option('quizme_settings', $settings);
      echo 'success!';
    }

    exit;
  }

  public function render() {
    $this->save();
    $settings = get_option('quizme_settings');
    $accessToken = $settings['access_token'] ?? null;
    ?>
    <h2>Quizme Settings</h2>
    <form method="post">
      <?php if ($accessToken) { ?>
        <div>
          <button type="button" id="update-credentials">Update Google Credentials</button>
        </div>
      <?php } else if($settings['credentials']) { 
        $auth_url = add_query_arg(['action' => 'quizme/authorize-credentials'], admin_url('admin-ajax.php'));
        ?>
        <div>
          <a href="<?php echo $auth_url; ?>">Authorize Credentials</a>
        </div>
      <?php } ?>
      
      <div id="quizme-credential">
        <label>Credentials.json</label>
        <div>
          <textarea name="settings[credentials]"><?php echo $settings['credentials'] ?? ''; ?></textarea>
        </div>

        <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
      </div>

      <input name="update-credentials" type="hidden" value="<?php echo ($accessToken) ? '0' : '1'; ?>" />
    </form>
    <script>
      jQuery(document).ready(function($) {
        wp.codeEditor.initialize($('[name="settings[credentials]"]'), cm_settings);
        <?php if ($accessToken) { ?>
          $('#quizme-credential').hide();
        <?php } ?>

        $('#update-credentials').on('click', function(){
          $('[name="update-credentials"]').val('1');
          $('#quizme-credential').show();
          $('#update-credentials').hide();
        });
      })
      </script>
      <style>
      .CodeMirror {
        border: 1px solid #eee;
        height: auto;
      }
    </style>
    <?php
  }

  public function save(){
    if ($_SERVER['REQUEST_METHOD'] != 'POST' || !isset($_POST['settings'])) {
      return;
    }

    $settings = get_option('quizme_settings');
    if ($_POST['update-credentials'] === '1') {
      $settings['credentials'] = stripslashes($_POST['settings']['credentials'] ?? '');
  
      if ($settings['credentials']) {
        if (!json_decode($settings['credentials'])){
          echo 'Credentials must be a valid JSON';
          return;
        }
      } else {
        $settings['access_token'] = '';
      }
    }

    if (update_option('quizme_settings', $settings)) {
      echo 'settings saved!';
    } else {
      echo 'error while saving settings!';
    }
  }

  public function enqueue_scripts($hook) {
    if ($hook != 'quiz_page_quizme-settings') {
      return;
    }

    $cm_settings['codeEditor'] = wp_enqueue_code_editor(array('type' => 'application/json'));
    wp_localize_script('jquery', 'cm_settings', $cm_settings);

    wp_enqueue_script('wp-theme-plugin-editor');
    wp_enqueue_style('wp-codemirror');
  }
}