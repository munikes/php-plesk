<?php
namespace pmill\Plesk;

class UpdateSubdomain extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
    <subdomain>
        <set>
            <filter>
                {FILTER}
            </filter>
            {PROPERTIES}
        </set>
    </subdomain>
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
        if (isset($params['subdomain'])) {
            $params['filter'] = new Node('name', $params['subdomain']);
        }

        if (isset($params['id'])) {
            $params['filter'] = new Node('id', $params['id']);
        }

        $properties = [];

        foreach (['www_root'] as $key) {
            if (isset($params[$key])) {
                $properties[$key] = $params[$key];
            }
        }

        $params['properties'] = $this->generatePropertyList($properties);

        parent::__construct($config, $params);
    }

    /**
     * @param $xml
     * @return bool
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        $result = $xml->subdomain->set->result;

        if ($result->status == 'error') {
            throw new ApiRequestException($result);
        }

        return true;
    }
}
