<?php

/**
 * Slack Notifier Plugin Setup
 *
 * @author     Martial Séron <martial.seron@gmail.com>
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 */

define('SLACKNOTIFIER_VERSION', '1.0.0');

/**
 * Init the hooks of the plugins - Needed
 *
 * @return void
 */
function plugin_init_slacknotifier() {
   global $PLUGIN_HOOKS, $DB;

  $PLUGIN_HOOKS['csrf_compliant']['slacknotifier'] = true;

  Plugin::registerClass('PluginSlacknotifierTicket');
  Plugin::registerClass('PluginSlacknotifierNotification');
  Plugin::registerClass('PluginSlacknotifierWebHook');

  //some code here, like call to Plugin::registerClass(), populating PLUGIN_HOOKS, ...
  $plugin = new Plugin();
  if (isset($_SESSION['glpiID'])
    && $plugin->isInstalled('slacknotifier')
    && $plugin->isActivated('slacknotifier')) {

    $PLUGIN_HOOKS['pre_item_add']['slacknotifier'] = [
      'Group_Ticket' => 'plugin_slacknotifier_pre_item_add_group_ticket',
    ];

  }
  return true;
}

/**
 * Get the name and the version of the plugin - Needed
 *
 * @return array
 */
function plugin_version_slacknotifier() {
   return [
      'name'           => 'Slack Notifier',
      'version'        => SLACKNOTIFIER_VERSION,
      'author'         => 'Martial Séron',
      'license'        => 'GLPv3',
      'homepage'       => 'https://github.com/MartialSeron/slacknotifier',
      'requirements'   => [
         'glpi'   => [
            'min' => '0.90'
         ]
      ]
   ];
}

/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 *
 * @return boolean
 */
function plugin_slacknotifier_check_prerequisites() {
   //do what the checks you want
   return true;
}

/**
 * Check configuration process for plugin : need to return true if succeeded
 * Can display a message only if failure and $verbose is true
 *
 * @param boolean $verbose Enable verbosity. Default to false
 *
 * @return boolean
 */
function plugin_slacknotifier_check_config($verbose = false) {
  if (true) { // Your configuration check
    return true;
  }

  if ($verbose) {
    echo "Installed, but not configured";
  }
  return false;
}