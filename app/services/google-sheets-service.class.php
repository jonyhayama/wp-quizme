<?php

namespace wpQuizme\services;

use \Google;

class googleSheetsService {
  public function buildClient() {
    $client = new Google\Client();
    $client->setApplicationName('Google Sheets API PHP Quickstart');
    $client->setScopes(Google\Service\Sheets::SPREADSHEETS);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    return $client;
  }
  public function getClient() {
    $settings = get_option('quizme_settings');
    $credentials = json_decode($settings['credentials'], true);
    if (!$credentials) {
      return false;
    }

    $client = $this->buildClient();
    $client->setAuthConfig($credentials);

    $accessToken = $settings['access_token'] ?? null;
    if (!$accessToken) {
      return false;
    }
    
    $client->setAccessToken($accessToken);
    if ($client->isAccessTokenExpired()) {
      if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $settings['access_token'] = json_encode($client->getAccessToken());
        update_option('quizme_settings', $settings);
      } else {
        return false;
      }
    }
    return $client;
  }

  public function getService() {
    $client = $this->getClient();
    $service = new Google\Service\Sheets($client);

    return $service;
  }
}
