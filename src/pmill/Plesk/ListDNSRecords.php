<?php
namespace pmill\Plesk;

class ListDNSRecords extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="1.6.7.0">
<dns>
  <get_rec>
    {FILTER}
  </get_rec>
</dns>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'filter' => '<filter/>',
    ];

    /**
     * @param array $config
     * @param array $params
     * @throws ApiRequestException
     */
    public function __construct(array $config, $params = [])
    {
      $this->default_params['filter'] = new Node('filter');

      if (isset($params['domain'])) {
        $request = new GetSite($config, ['domain' => $params['domain']]);
        $info = $request->process();

        $ownerSiteId = new Node('site-id', $info['id']);
        $params['filter'] = new Node('filter', $ownerSiteId);
      }

      parent::__construct($config, $params);
    }

    /**
     * @param $xml
     * @return array
     */
    protected function processResponse($xml)
    {
        $result = [];

        foreach ($xml->dns->get_rec->children() as $node) {
          if (isset($node->id)){
            $result[] = [
                'status' => (string)$node->status,
                'id' => (int)$node->id,
                'type' => (string)$node->data->type,
                'host' => (string)$node->data->host,
                'value' => (string)$node->data->value,
                'opt' => (string)$node->data->opt,
            ];
          }
        }

        return $result;
    }
}
