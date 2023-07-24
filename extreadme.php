<?php

require_once 'extreadme.civix.php';
// phpcs:disable
use CRM_Extreadme_ExtensionUtil as E;
// phpcs:enable

/**
 * clear and remove a directory
 *
 * @param string $dir Directory to be removed after deleting all files.
 * @return void
 */
function _extreadme_clearDir($dir) {
  if ($dh = opendir($dir)) {
    while (($file = readdir($dh)) !== FALSE) {
      if ($file == '.' || $file == '..') {
        continue;
      }
      $fn = "$dir/$file";
      if (is_dir($fn)) {
        _extreadme_clearDir($fn);
      }
      else {
        unlink($fn);
      }
    }
    closedir($dh);
    rmdir($dir);
  }
}

/**
 * get directory to temp copy remote/core extension to so that images can display
 *
 * @return string
 */
function _extreadme_readmeDir() {
  $readmeDir = rtrim(CRM_Core_Config::singleton()->imageUploadDir, '/') . '/readme/';
  if (!is_dir($readmeDir)) {
    mkdir($readmeDir);
  }
  return $readmeDir;
}

/**
 * Implements hook_civicrm_buildForm().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_buildForm/
 */
function extreadme_civicrm_buildForm($formName, &$form) {

  if ($formName == 'CRM_Admin_Form_Extensions') {
    $key = $form->get_template_vars('key');
    $ext = $form->get_template_vars('extension');

    // download the extension if not local
    if (empty($ext['path']) && !empty($ext['downloadUrl'])) {
      $extDir = _extreadme_readmeDir();
      $zn = $extDir . basename($ext['downloadUrl']);

      if (($buf = file_get_contents($ext['downloadUrl'])) === FALSE || file_put_contents($zn, $buf) === FALSE) {
        return;
      }
      $zip = new ZipArchive();
      if ($zip->open($zn) === TRUE) {
        $ext['path'] = rtrim($extDir . $zip->statIndex(0)['name'], '/');
        $zip->extractTo($extDir);
        $zip->close();
      }
      unlink($zn);
    }
    else {
      // check if it's a core extension
      $docroot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
  
      if (strpos($ext['path'], $docroot) !== 0) {
        $link = _extreadme_readmeDir() . $key;
        if (!is_link($link)) {
          symlink($ext['path'], $link);
        }
        $ext['path'] = $link;
      }
    }

    // allow extension to define per action readme
    $dir = "{$ext['path']}/docs/extlc";
    if (is_dir($dir)) {
      $actions = [
        CRM_Core_Action::ADD => 'install',
        CRM_Core_Action::DELETE => 'uninstall',
        CRM_Core_Action::ENABLE => 'enable',
        CRM_Core_Action::DISABLE => 'disable',
        CRM_Core_Action::UPDATE => 'update'
      ];
      $readme = "$dir/" . ($actions[$form->getAction()] ?? '-unknown-') . '.md';
      if (!file_exists($readme)) {
        unset($readme);
      }
      else {
        $readme = substr($readme, strpos($readme, 'docs/extlc/'));
      }
    }
    // otherwise default to main readme
    if (empty($readme)) {
      $readme = 'README.md';
    }

    try {
      $api = civicrm_api3('Extension', 'readme', [
        'key' => $key,
        'ext' => $ext,
        'readme' => $readme
      ]);
  
      if (!$api['is_error']) {
        // add it to the page (top of content)
        // the alterContent hook will see this and inject additional files
        CRM_Core_Region::instance('page-body')->add([
          'markup' => "<div class=\"ext-readme markdown-body\" data-extkey=\"$key\" data-extroot=\"{$api['extroot']}\" data-docroot=\"{$api['docroot']}\">{$api['html']}</div>",
          'weight' => -100
        ]);
        // and make sure that footer gets placed properly (bottom of content)
        CRM_Core_Region::instance('page-body')->add([
          'markup' => '<div class="clear"></div>'
        ]);
      }
    }
    catch (Exception $e) {}
  }
}

/**
 * Implements hook_civicrm_upgrade().
 * 
 * Clear out any extension(s) downloaded/symlinked (so readme can be displayed) but were not installed.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function extreadme_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  $extDir = _extreadme_readmeDir();

  if ($dh = opendir($extDir)) {
    while (($file = readdir($dh)) !== FALSE) {
      if ($file == '.' || $file == '..') {
        continue;
      }
      $fn = $extDir . $file;
      if (is_link($fn)) {
        unlink($fn);
      }
      else {
        _extreadme_clearDir($fn);
      }
    }
    closedir($dh);
  }

  return _extreadme_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_alterContent().
 * 
 * Allow an extension to add markdown content via markup, in which case...
 * ...we need to inject some code and style.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterContent/
 */
function extreadme_civicrm_alterContent(&$content, $context, $tplName, &$object) {
  if (preg_match('/class\s*=\s*".*?markdown-body.*?"/m', $content)) {
    // at this point, it's too late to use Civi::resouces()
    $content .= '<link type="text/css" rel="stylesheet" href="' .  E::url('css/extreadme.css') . '" />';
    $content .= '<link type="text/css" rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/github-markdown-css/5.2.0/github-markdown.min.css" />';
    $content .= '<script type="text/javascript" src="' .  E::url('js/extreadme.js') . '"></script>';
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function extreadme_civicrm_config(&$config): void {
  _extreadme_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function extreadme_civicrm_install(): void {
  _extreadme_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function extreadme_civicrm_postInstall(): void {
  _extreadme_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function extreadme_civicrm_uninstall(): void {
  _extreadme_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function extreadme_civicrm_enable(): void {
  _extreadme_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function extreadme_civicrm_disable(): void {
  _extreadme_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function extreadme_civicrm_entityTypes(&$entityTypes): void {
  _extreadme_civix_civicrm_entityTypes($entityTypes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function extreadme_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function extreadme_civicrm_navigationMenu(&$menu): void {
//  _extreadme_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _extreadme_civix_navigationMenu($menu);
//}
