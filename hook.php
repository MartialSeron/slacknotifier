<?php

function plugin_slacknotifier_install() {
  global $DB;
  //get version
  $plugin = new Plugin();
  $found = $plugin->find("name = 'slacknotifier'");
  $plugin_slacknotifier = array_shift($found);
  //init migration
  $migration = new Migration($plugin_slacknotifier['version']);

  if (!TableExists('glpi_plugin_slacknotifier_notifications')) {
    $query = "CREATE TABLE `glpi_plugin_slacknotifier_notifications` (
      `id`          int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name`        varchar(45) NOT NULL,
      `is_active`   tinyint(1) unsigned NOT NULL DEFAULT '0',
      `webhook`     varchar(250) CHARACTER SET utf8 NOT NULL,
      `channel`     varchar(21) DEFAULT NULL,
      `username`    varchar(21) DEFAULT NULL,
      `groups_id`   varchar(45) DEFAULT NULL,
      `proxy`       VARCHAR(250) CHARACTER SET utf8 NULL 
      `proxyauth`   VARCHAR(250) CHARACTER SET utf8 NULL 
      PRIMARY KEY (`id`),
      KEY `is_active` (`is_active`),
      KEY `groups_id` (`groups_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
    $DB->queryOrDie($query, $DB->error());
  }

  $migration->executeMigration();

  return true;
}

function plugin_slacknotifier_uninstall() {
  global $DB;
  //Delete plugin's table
  $tables = [
    'glpi_plugin_slacknotifier_notifications',
  ];
  foreach ($tables as $table) {
    $DB->query("DROP TABLE IF EXISTS `$table`");
  }
  return true;
}

function plugin_slacknotifier_pre_item_add_group_ticket($item) {
  if ($item instanceof Group_Ticket && $item->fields['type'] == CommonITILActor::ASSIGN) {
    return PluginSlacknotifierTicket::processAfterAddAssignGroup($item);
  }
  return $item;
}