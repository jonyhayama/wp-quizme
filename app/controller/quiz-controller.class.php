<?php

namespace wpQuizme\controller;

use \Google;

class quizController {
  public function __construct() {
    add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
    add_action('wp_ajax_quizme', [$this, 'ajax']);
    add_action('wp_ajax_nopriv_quizme', [$this, 'ajax']);
    add_shortcode('quizme', [$this, 'shortcode']);
  }

  public function register_scripts() {
    $file = '/js/quizme/chunk-vendors.js';
    wp_register_script('wp-quizme-script-vendors', WP_QUIZME_ASSETS_URL . $file, [], filemtime(WP_QUIZME_ASSETS_PATH . $file), true);

    $file = '/js/quizme/app.js';
    wp_register_script('wp-quizme-script', WP_QUIZME_ASSETS_URL . $file, ['wp-quizme-script-vendors'], filemtime(WP_QUIZME_ASSETS_PATH . $file), true);
  }

  public function ajax() {
    header('Content-type: application/json');

    switch ($_SERVER['REQUEST_METHOD']) {
      case 'GET':
        $this->get_quiz_ajax();
        break;
      case 'POST':
        $this->post_quiz_ajax();
        break;
    }
  }

  public function get_quiz_ajax() {
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

    // $quiz_id = (int) $_GET['id'];
    // $quiz_config = json_decode(get_post_meta($quiz_id, 'quizme_json', true));
    // $options = json_decode(get_post_meta($quiz_id, 'quizme_options_json', true));
    // $service = wp_quizme('googleSheetsService')->getService();

    // $spreadsheetId = $options->spreadsheetId;
    // $range = $options->range;
    // $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    // $values = $response->getValues();

    // if (empty($values)) {
    //   echo "No data found.\n";
    // } else {
    //   echo "Name, Major:\n";
    //   foreach ($values as $row) {
    //     // Print columns A and E, which correspond to indices 0 and 4.
    //     printf("%s, %s\n", $row[0], $row[4]);
    //   }
    // }

    // echo 'HERE';

    // $values = [
    //   ["a", "b", "C", "D", "E"]
    // ];
    // $body = new Google\Service\Sheets\ValueRange([
    //   'values' => $values
    // ]);
    // $params = [
    //   'valueInputOption' => "RAW"
    // ];
    // // $result = $service->spreadsheets_values->append($spreadsheetId, $range, $body, $params);
    // // var_dump($result);

    // exit;
    $post_id = (int) $_GET['id'];
    $quiz_json = get_post_meta($post_id, 'quizme_json', true);

    echo $quiz_json;
    exit;
  }

  public function post_quiz_ajax() {
    $json = file_get_contents('php://input');
    $data = json_decode($json);
    $score = (int) $data->score;

    $post_id = (int) $_GET['id'];
    $quiz_options = json_decode(get_post_meta($post_id, 'quizme_options_json', true));
    $redirectTo = end($quiz_options->redirectTo);
    foreach ($quiz_options->redirectTo as $r) {
      if ($r->score < $score) {
        $redirectTo = $r;
        break;
      }
    }

    $redirectTo = get_permalink($redirectTo->pageId);

    echo json_encode([
      'redirectTo' => $redirectTo,
      'originalData' => $data
    ]);
    exit;
  }

  public function shortcode($atts) {
    $atts = shortcode_atts([
      'id' => ""
    ], $atts);

    $query_args = [
      'action' => 'quizme',
      'id' => (int) $atts['id']
    ];
    $quiz_url = add_query_arg($query_args, admin_url('admin-ajax.php'));

    wp_enqueue_script('wp-quizme-script');
    return '<div id="quizzme" data-quiz-url="' . $quiz_url . '"></div>';
  }
}
