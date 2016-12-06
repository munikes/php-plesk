<?php
namespace pmill\Plesk;

class CreateFTPUser extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="1.6.5.0">
<ftp-user>
    <add>
        <name>{NAME}</name>
        <password>{PASSWORD}</password>
        <webspace-id>{WEBSPACE-ID}</webspace-id>
        <home/>
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
