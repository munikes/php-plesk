<?php
namespace pmill\Plesk;

class CreateFTPUser extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet>
<ftp-user>
    <add>
        <name>{NAME}</name>
        <password>{PASSWORD}</password>
        <home/>
        <webspace-id>{WEBSPACE-ID}</webspace-id>
    </add>
</ftp-user>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'name' => null,
        'password' => null,
        'webspace-id' => null,
    ];

    /**
     * @param $xml
     * @return bool
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        if ($xml->{'ftp-user'}->add->result->status == 'error') {
            throw new ApiRequestException($xml->{'ftp-user'}->add->result);
        }

        $this->id = (int)$xml->{'ftp-user'}->add->result->id;
        return true;
    }

}
