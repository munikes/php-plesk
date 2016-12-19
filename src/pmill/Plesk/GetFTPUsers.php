<?php
namespace pmill\Plesk;

class GetFTPUsers extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet>
  <ftp-user>
    <get>
      <filter>
        {FILTER}
      </filter>
    </get>
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
     * GetUser constructor.
     * @param array $config
     * @param array $params
     */
    public function __construct(array $config, $params)
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
     * @return array
     */
    protected function processResponse($xml)
    {
        $result = [];

        for ($i = 0; $i < count($xml->{'ftp-user'}->get->result); $i++) {
            $ftpuser = $xml->{'ftp-user'}->get->result[$i];

            if ($ftpuser->status == 'error') {
                 throw new ApiRequestException($ftpuser);
            }

            $result[$i] = [
                'id' => (string)$ftpuser->id,
                'status' => (string)$ftpuser->status,
                'name' => (string)$ftpuser->name,
                'home' => (string)$ftpuser->home,
                'webspace-id' => (int)$ftpuser->{'webspace-id'},
                'quota' => (float)$ftpuser->quota,
                'permission-read' => (bool)$ftpuser->permissions->read,
                'permission-write' => (bool)$ftpuser->permissions->write,
            ];
        }

        return $result;

    }
}
