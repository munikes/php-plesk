<?php
namespace pmill\Plesk;

class GetDatabaseServer extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<db_server>
   <get-local>
      <filter>
          <type>{TYPE}</type>
      </filter>
   </get-local>
</db_server>
</packet>
EOT;

    /**
     *
     * @var array
     */
    protected $default_params = [
        'type' => null,
    ];

    /**
     * @param $xml
     * @return bool
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        if ($xml->db_server->{'get-local'}->result->status == 'error') {
            throw new ApiRequestException($xml->db_server->{'get-local'}->result);
        }

        $db_server = $xml->db_server->{'get-local'}->result;
        return [
             'id' => (int)$db_server->id,
             'status' => (string)$db_server->status,
             'type' => (string)$db_server->type,
        ];
    }

}
