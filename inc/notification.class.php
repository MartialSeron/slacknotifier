<?php

if (!defined('GLPI_ROOT')) {
  die("Sorry. You can't access directly to this file");
}

/**
 * Undocumented class
 */
class PluginSlacknotifierNotification extends CommonDBTM {

  /**
   * Return all notifications related to group
   *
   * @param Integer $groupid
   * @return Array
   */
  static function getNotifications($groupid) {
    global $DB;
    $query = "SELECT *
              FROM glpi_plugin_slacknotifier_notifications
              WHERE glpi_plugin_slacknotifier_notifications.groups_id = '$groupid'
              AND glpi_plugin_slacknotifier_notifications.is_active = 1";

    $rep = [];
    foreach ($DB->request($query) as $data) {
      $rep[]=$data;
    }
    
    return $rep;
  }
}