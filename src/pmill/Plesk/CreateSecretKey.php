<?php
namespace pmill\Plesk;

class CreateSecretKey extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<secret_key>
   <create>
      <ip_address>{IP_ADDRESS}</ip_address>
      {DESCRIPTION}
   </create>
</secret_key>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'ip_address' => null,
    ];

    /**
     * @var string
     */
    public $key;

    /**
     * @param array $config
     * @param array $params
     */
    public function __construct(array $config, array $params = [])
    {
        if (isset($params['description'])) {
            $params['description'] = new Node('description', $params['description']);
        } else {
            $params['description'] = '';
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
        if ($xml->{'secret_key'}->create->result->status == 'error') {
            throw new ApiRequestException($xml->{'secret_key'}->create->result);
        }

        $this->key = (string)$xml->{'secret_key'}->create->result->key;
        return true;
    }
}
