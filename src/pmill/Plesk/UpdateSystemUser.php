<?php
namespace pmill\Plesk;

class UpdateSystemUser extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet>
    <webspace>
        <set>
            {FILTER}
          <values>
            {PROPERTIES}
          </values>
        </set>
    </webspace>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'filter' => null,
        'properties' => null,
    ];

    /**
     * @param array $config
     * @param array $params
     * @throws ApiRequestException
     */
    public function __construct(array $config, array $params)
    {
        if (isset($params['subscription_id'])) {
            $idNode = new Node('id', $params['subscription_id']);
            $params['filter'] = new Node('filter', $idNode);
        }

        $properties = [];

        foreach (['shell', 'ftp_password'] as $key) {
            if (isset($params[$key])) {
                $properties[$key] = $params[$key];
            }
        }

        if (count($properties) > 0) {
          $childNode = new Node('vrt_hst', $this->generatePropertyList($properties));
          $params['properties'] = new Node('hosting', $childNode);
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
        $result = $xml->webspace->set->result;

        if ($result->status == 'error') {
            throw new ApiRequestException($result);
        }

        return true;
    }
}
