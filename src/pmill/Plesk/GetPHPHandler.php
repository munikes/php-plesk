<?php
namespace pmill\Plesk;

class GetPHPHandler extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<php-handler>
	<get>
		<filter>
			<id>{ID}</id>
		</filter>
	</get>
</php-handler>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'id' => 'fpm',
    ];

    /**
     * @param $xml
     * @return array
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        $php_handler = $xml->{'php-handler'}->get->result;

        if ((string)$php_handler->status == 'error') {
            throw new ApiRequestException($php_handler);
        }
        if ((string)$php_handler->result->status == 'error') {
            throw new ApiRequestException($php_handler->result);
        }

        return [
            'id' => (string)$php_handler->id,
            'status' => (string)$php_handler->status,
            'display_name' => (string)$php_handler->{'display-name'},
            'full_version' => (string)$php_handler->{'full-version'},
            'version' => (string)$php_handler->version,
            'type' => (string)$php_handler->type,
            'path' => (string)$php_handler->path,
            'clipath' =>(string)$php_handler->clipath,
            'phpini' => (string)$php_handler->phpini,
            'custom' => (string)$php_handler->custom === 'true' ? true: false,
            'handler_status' => (string)$php_handler->{'handler-status'}
        ];
    }

}
