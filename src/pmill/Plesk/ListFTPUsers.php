<?php
namespace pmill\Plesk;

class ListFTPUsers extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
  <ftp-user>
    <get>
      <filter/>
    </get>
  </ftp-user>
</packet>
EOT;

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
