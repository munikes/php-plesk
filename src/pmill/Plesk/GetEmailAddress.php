<?php
namespace pmill\Plesk;

class GetEmailAddress extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
<mail>
	<get_info>
		<filter>
			<site-id>{SITE_ID}</site-id>
      <name>{USERNAME}</name>
		</filter>
		<mailbox/>
		<forwarding/>
		<autoresponder/>
	</get_info>
</mail>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
      'site_id' => null,
      'username' => null
    ];

    /**
     * @param array $config
     * @param array $params
     * @throws ApiRequestException
     */
    public function __construct($config, $params)
    {
        if (isset($params['domain'])) {
            $request = new GetSite($config, ['domain' => $params['domain']]);
            $info = $request->process();

            $params['site_id'] = $info['id'];
        }

        parent::__construct($config, $params);
    }

    /**
     * @param $xml
     * @return array
     */
    protected function processResponse($xml)
    {

      $email = $xml->mail->get_info->result;

      if ((string)$email->status == 'error') {
        throw new ApiRequestException($email);
                                    }
      if ((string)$email->result->status == 'error') {
        throw new ApiRequestException($email->result);
      }

      $autoresponder = null;
      if (isset($email->mailname->autoresponder->enabled) AND $email->mailname->autoresponder->enabled == true)
      {
        $autoresponder = [
          'subject' => (string)$email->mailname->autoresponder->subject,
          'content_type' => (string)$email->mailname->autoresponder->content_type,
          'charset' => (string)$email->mailname->autoresponder->charset,
          'text' => (string)$email->mailname->autoresponder->text,
        ];
      }

      $forwarding = null;
      if (isset($email->mailname->forwarding->enabled) AND $email->mailname->forwarding->enabled == true)
      {
        $forwarding = (array)$email->mailname->forwarding->address;
      }

      return [
        'status' => (string)$email->status,
        'id' => (int)$email->mailname->id,
        'username' => (string)$email->mailname->name,
        'enabled' => (string)$email->mailname->mailbox->enabled === 'true' ? true: false,
        'password' => (string)$email->mailname->password->value,
        'quota' => (int)$email->mailname->mailbox->quota,
        'autoresponder' => $autoresponder,
        'forwarding' => $forwarding,
      ];

    }
}
