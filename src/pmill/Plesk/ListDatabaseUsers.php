<?php
namespace pmill\Plesk;

class ListDatabaseUsers extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<database>
   <get-db-users>
      {FILTER}
   </get-db-users>
</database>
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
        if (isset($params['database_id'])) {
            $ownerDBIdNode = new Node('db-id', $params['database_id']);
            $params['filter'] = new Node('filter', $ownerDBIdNode);
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
        $result = [];

        if ($xml->database->{'get-db-users'}->result->status == 'error') {
            throw new ApiRequestException($xml->database->{'get-default-user'}->result);
        }

        $users = $xml->database->{'get-db-users'}->result;
        foreach ($users as $user){
          if (isset($user->id)){
            $result[] = [
              'status'=> (string)$user->status,
              'filter-id'=> (int)$user->{'filter-id'},
              'id'=> (int)$user->id,
              'db-id'=> (int)$user->{'db-id'},
              'login'=> (string)$user->login,
              'acl-host'=> (string)$user->acl->host,
              'allow-access-from-ip'=> (string)$user->{'allow-access-from'}->{'ip-address'},
            ];
          }
        }

        return $result;
    }

}
