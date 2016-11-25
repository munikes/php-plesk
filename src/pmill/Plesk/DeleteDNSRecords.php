<?php
namespace pmill\Plesk;

class DeleteDNSRecords extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet>
  <dns>
    <del_rec>
      {FILTER}
    </del_rec>
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
    public function __construct(array $config, $params)
    {
      $this->default_params['filter'] = new Node('filter');

      if (isset($params['site-id'])) {
        $ownerSiteId = new Node('site-id', $params['site-id']);
        $params['filter'] = new Node('filter', $ownerSiteId);
      }else if(isset($params['id'])){
        $ownerId = new Node('id', $params['id']);
        $params['filter'] = new Node('filter', $ownerId);
      }

      parent::__construct($config, $params);
    }

    /**
     * @param $xml
     * @return bool
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        $result = $xml->dns->{'del_rec'}->result;

        if ($result->status == 'error') {
            throw new ApiRequestException($result);
        }

        return true;

    }
}
