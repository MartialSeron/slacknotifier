<?php

if (!defined('GLPI_ROOT')) {
  die("Sorry. You can't access directly to this file");
}

class PluginSlacknotifierWebHook {

  private $url;
  private $payload;
  private $proxy;
  private $proxyauth;

  public function __construct() {
    $this->payload   = array();
    $this->url       = '';
    $this->proxy     = '';
    $this->proxyauth = '';
  }

  /**
   * Undocumented function
   *
   * @param [type] $config
   * @return void
   */
  public function setConfig($config) {
    $this->payload['channel']  = (isset($config['channel'])) ? $config['channel'] : '#general';
    $this->payload['username'] = (isset($config['username'])) ? $config['username'] : 'glpibot';
    $this->url                 = $config['webhook'];
    $this->proxy               = $config['proxy'];
    $this->proxyauth           = $config['proxyauth'];
  }

  /**
   * Undocumented function
   *
   * @param [type] $arr
   * @return void
   */
  public function formatFromArray($values) {
    $ticketId             = isset($values['ticketId']) ? $values['ticketId'] : '';
    $ticketTitle          = isset($values['ticketTitle']) ? $values['ticketTitle'] : '';
    $ticketAssigmentGroup = isset($values['ticketAssigmentGroup']) ? $values['ticketAssigmentGroup'] : '';
    $ticketContent        = isset($values['ticketContent']) ? $values['ticketContent'] : '';
    $ticketEntity         = isset($values['ticketEntity']) ? $values['ticketEntity'] : '';
    $ticketPriority       = isset($values['ticketPriority']) ? $values['ticketPriority'] : '';
    $ticketCategory       = isset($values['ticketCategory']) ? $values['ticketCategory'] : '';
    $ticketStatus         = isset($values['ticketStatus']) ? $values['ticketStatus'] : '';
    $ticketType           = isset($values['ticketType']) ? $values['ticketType'] : '';

    $conf = Config::getConfigurationValues('core');

    Toolbox::logDebug($conf['url_base']);

    $this->payload['attachments'] = array(
      array(
        'fallback'   => sprintf(__("New ticket assign to %s", "slacknotifier"), $ticketAssigmentGroup),
        'pretext'    => sprintf(__("New ticket assign to *%s*", "slacknotifier"), $ticketAssigmentGroup),
        'color'      => '#c31625',
        'title'      => trim(sprintf(__("Ticket %s", "slacknotifier"), $ticketId) . ($ticketTitle !== '' ? ': ' : '') . $ticketTitle),
        'title_link' => sprintf('%s/index.php?redirect=ticket_%s_Ticket$1&noAUTO=1', $conf['url_base'], $ticketId),
        'text'       => $ticketContent,
        'fields'     => array(
          array(
            'title' => __("Priority"),
            'value' => $ticketPriority,
            'short' => 'true',
          ),
          array(
            'title' => __("Entity"),
            'value' => $ticketEntity,
            'short' => 'true',
          ),
          array(
            'title' => __("Status"),
            'value' => $ticketStatus,
            'short' => 'true',
          ),
          array(
            'title' => __("Type"),
            'value' => $ticketType,
            'short' => 'true',
          ),
          array(
            'title' => __("Category"),
            'value' => $ticketCategory,
            'short' => 'false',
          ),
        ),
        'footer' => __("Slack Notifier Plugin for GLPI", "slacknotifier"),
        'ts'     => time(),
      ),
    );
  }

  public function isValid() {
    // TODO
    return true;
  }

  public function send() {
    $data = json_encode($this->payload);

    Toolbox::logDebug($this->payload);

    $curl = curl_init($this->url);
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl, CURLOPT_HEADER, 1);

    if ($this->proxy !== '') {
      curl_setopt($curl, CURLOPT_PROXY, $this->proxy);
      if ($this->proxyauth !== '') {
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, $this->proxyauth);
      }
    }

    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2); 
    curl_setopt($curl, CURLOPT_TIMEOUT, 5); //timeout in seconds


    $result = curl_exec($curl) or die(curl_error($curl));
  }

}