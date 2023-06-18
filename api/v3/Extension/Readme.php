<?php

require_once 'extreadme.civix.php';
// phpcs:disable
use CRM_Extreadme_ExtensionUtil as E;
// phpcs:enable

function civicrm_api3_extension_readme($params) {
  $ext = $params['ext'] ?? civicrm_api3('Extension', 'getsingle', [
    'key' => $params['key']
  ]);

  $docroot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
  $readme = $params['readme'];

  if (strpos($readme, $docroot) !== 0) {
    $readme = $docroot . $readme;
  }

  if (strpos($readme, $ext['path']) !== 0 || !file_exists($readme)) {
    return [
      'is_error' => 1,
      'error_message' => E::ts('Danger, Will Robinson!')
    ];
  }

  require_once(E::path('src/Parsedown.php'));
  $parsedown = new Parsedown();

  return [
    'is_error' => 0,
    'root' => dirname(substr($readme, strlen($docroot))) . '/',
    'html' => $parsedown->text(file_get_contents($readme))
  ];
}
