<?php
namespace pmill\Plesk;

class CreateDNSRecord extends BaseRequest
{
  /*
   * *
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="1.6.7.0">
  <dns>
    <add_rec>
      <site-id>{SITE_ID}</site-id>
      <type>{TYPE}</type>
      <host>{HOST}</host>
      <value>{VALUE}</value>
    </add_rec>
  </dns>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'site_id' => null,
        'type' => null,
        'host' => null,
        'value' => null
    ];

    /**
     * @param $xml
     * @return bool
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
      if ((string)$xml->dns->{'add_rec'}->result->status == 'error') {
            throw new ApiRequestException($xml->dns->{'add_rec'}->result);
        }

        $this->id = (int)$xml->dns->{'add_rec'}->result->id;
        return true;
    }

}
