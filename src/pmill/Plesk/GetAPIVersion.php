<?php
namespace pmill\Plesk;

class GetAPIVersion extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<server>
	<get_protos/>
</server>
</packet>
EOT;

    /**
     * @param $xml
     * @return string
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        $server = $xml->server->get_protos->result;

        if ((string)$server->status === 'error') {
            throw new ApiRequestException($server);
        }

        if ((string)$server->result->status === 'error') {
            throw new ApiRequestException($server->result);
        }

        // Get last version
        $versions = [];
        $versions = $server->protos->proto;
        $last_version = end($versions);

        return $last_version;
    }
}
