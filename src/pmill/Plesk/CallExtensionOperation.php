<?php
namespace pmill\Plesk;
use pmill\Plesk\Helper\Xml;
class CallExtensionOperation extends BaseRequest
{
  /**
   * @var string
   */
  public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<extension>
    <call>
      {EXTENSION_OPERATION}
    </call>
</extension>
</packet>
EOT;

  /**
   * @var array
   */
  protected $default_params = [
    'extension_operation' => null,
  ];

  /**
   * @param array $config
   * @param array $params
   * @throws ApiRequestException
   */
  public function __construct($config, $params = [])
  {
    $operation_params = [];

    // Create operation params
    foreach ($params['operation_params'] as $key => $value)
    {
      $operation_params[] = new Node($key, $value);
    }

    // Create node with operation
    $operation = [ new Node($params['operation'], new NodeList($operation_params)) ];

    // Create node name of extension
    $extension_operation = [ new Node($params['name'], new NodeList($operation)) ];

    $params = [
      'extension_operation' => new NodeList($extension_operation),
    ];

    parent::__construct($config, $params);
  }

  /**
   * @param $xml
   * @return bool
   * @throws ApiRequestException
   */
  protected function processResponse($xml)
  {
    if ($xml->extension->call->result->status == 'ok')
    {
      return true;
    }
    else
    {
      throw new ApiRequestException($xml->extension->call->result);
    }
  }
}
