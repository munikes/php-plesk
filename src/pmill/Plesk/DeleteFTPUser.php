<?php
namespace pmill\Plesk;

class DeleteFTPUser extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet>
    <ftp-user>
        <del>
            <filter>
                {FILTER}
            </filter>
        </del>
    </ftp-user>
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
        if (isset($params['id'])) {
            $params['filter'] = new Node('id', $params['id']);
        }
        else if (isset($params['name'])) {
            $params['filter'] = new Node('name', $params['name']);
        }
        else if (isset($params['webspace-id'])) {
            $params['filter'] = new Node('webspace-id', $params['webspace-id']);
        }
        else if (isset($params['webspace-name'])) {
            $params['filter'] = new Node('webspace-name', $params['webspace-name']);
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
        $result = $xml->{'ftp-user'}->del->result;

        if ($result->status == 'error') {
            throw new ApiRequestException($result);
        }

        return true;
    }
}
