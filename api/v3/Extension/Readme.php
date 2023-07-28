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
  $extroot = $ext['path'] . '/';
  $readme = $extroot . ltrim($params['readme'], '/');

  if (!file_exists($readme)) {
    return [
      'is_error' => 1,
      'error_message' => E::ts('Danger, Will Robinson!')
    ];
  }

  return [
    'is_error' => 0,
    'docroot' => substr(dirname(realpath($readme)), strlen($extroot)) . '/',
    'extroot' => substr($extroot, strlen($docroot)),
    'html' => base64_encode(file_get_contents($readme))
  ];
}
