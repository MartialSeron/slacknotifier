<?php

if (!defined('GLPI_ROOT')) {
  die("Sorry. You can't access directly to this file");
}

class PluginSlacknotifierTicket {
  /**
   * Undocumented function
   *
   * @param CommonDBTM $item
   * @return void
   */
  static function processAfterAddAssignGroup(Group_Ticket $item) {
    if (isset($item->fields['type']) && $item->fields['type'] == CommonITILActor::ASSIGN) {
      $ticket   = new Ticket();
      $group    = new Group();
      $entity   = new Entity();
      $category = new ITILCategory();

      $ticket->getFromDB($item->fields['tickets_id']);
      $group->getFromDB($item->fields['groups_id']);
      $entity->getFromDB($ticket->fields['entities_id']);
      $category->getFromDB($ticket->fields['itilcategories_id']);

      $notifications = PluginSlacknotifierNotification::getNotifications($group->fields['id']);

      foreach($notifications as $notification) {
        Toolbox::logDebug($notification);
        $webhook = new PluginSlacknotifierWebHook();
        $webhook->setConfig($notification);
        Toolbox::logDebug($webhook);

        $status = Ticket::getAllStatusArray();

        if ($webhook->isValid()) {
          $webhook->formatFromArray(array(
            'ticketId'             => $ticket->getID(),
            'ticketTitle'          => $ticket->fields['name'],
            'ticketContent'        => $ticket->fields['content'],
            'ticketPriority'       => Ticket::getPriorityName($ticket->fields['priority']),
            'ticketAssigmentGroup' => $group->fields['name'],
            'ticketEntity'         => $entity->fields['name'],
            'ticketCategory'       => $category->fields['completename'],
            'ticketStatus'         => $status[$ticket->fields['status']],
            'ticketType'           => Ticket::getTicketTypeName($ticket->fields['type']),
          ));
          $webhook->send();
        }
      }
    }
  }
}