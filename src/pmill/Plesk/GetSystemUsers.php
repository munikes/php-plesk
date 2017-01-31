<?php
namespace pmill\Plesk;

use pmill\Plesk\Helper\Xml;

class GetSystemUsers extends BaseRequest
{
  /**
   *
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<webspace>
    <get>
        {FILTER}
        <dataset>
			<gen_info/>
			<hosting/>
			<subscriptions/>
		</dataset>
    </get>
</webspace>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'filter' => null,
    ];

    
    /**
     * @param array $config
     * @param array $params
     * @throws ApiRequestException
     */
    public function __construct($config, $params = [])
    {
        $this->default_params['filter'] = new Node('filter');

        if (isset($params['client_id'])) {
            $ownerIdNode = new Node('owner-id', $params['client_id']);
            $params['filter'] = new Node('filter', $ownerIdNode);
        }
        if (isset($params['username'])) {
            $ownerLoginNode = new Node('owner-login', $params['username']);
            $params['filter'] = new Node('filter', $ownerLoginNode);
        }
        if (isset($params['name'])) {
            $nameNode = new Node('name', $params['name']);
            $params['filter'] = new Node('filter', $nameNode);
        }
        if (isset($params['subscription_id'])) {
            $idNode = new Node('id', $params['subscription_id']);
            $params['filter'] = new Node('filter', $idNode);
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

        for ($i = 0; $i < count($xml->webspace->get->result); $i++) {
            $webspace = $xml->webspace->get->result[$i];

            if ($webspace->status == 'error') {
                 throw new ApiRequestException($webspace);
            }

            $result[$i] = [
                'id' => (string)$webspace->id,
                'status' => (string)$webspace->status,
                'name' => (string)$webspace->data->gen_info->name,
            ];
            foreach ($webspace->data->hosting->vrt_hst->children() as $property)
            {
              if ((string)$property->name == 'ftp_login')
              {
                $result[$i]['login'] = (string)$property->value;
              }
              else if((string)$property->name == 'ftp_password')
              {
                $result[$i]['password'] = (string)$property->value;
              }
              else if((string)$property->name == 'shell')
              {
                $result[$i]['shell'] = (string)$property->value;
              }
              else if((string)$property->name == 'ftp_password_type')
              {
                $result[$i]['type'] = (string)$property->value;
              }
            }
        }

        return $result;

    }
}
