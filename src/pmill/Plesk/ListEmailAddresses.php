<?php
namespace pmill\Plesk;

class ListEmailAddresses extends BaseRequest
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
        $result = [];

        foreach ($xml->mail->get_info->children() as $node) {

          if (isset($node->mailname))
          {
              $autoresponder = null;
              if ($node->mailname->autoresponder->enabled == true)
              {
                $autoresponder = [
                  'subject' => (string)$node->mailname->autoresponder->subject,
                  'content_type' => (string)$node->mailname->autoresponder->content_type,
                  'charset' => (string)$node->mailname->autoresponder->charset,
                  'text' => (string)$node->mailname->autoresponder->text,
                ];
              }

              $forwarding = null;
              if ($node->mailname->forwarding->enabled == true)
              {
                $forwarding = (array)$node->mailname->forwarding->address;
              }

              $result[] = [
                  'status' => (string)$node->status,
                  'id' => (int)$node->mailname->id,
                  'username' => (string)$node->mailname->name,
                  'enabled' => (string)$node->mailname->mailbox->enabled === 'true' ? true: false,
                  'password' => (string)$node->mailname->password->value,
                  'quota' => (int)$node->mailname->mailbox->quota,
                  'autoresponder' => $autoresponder,
                  'forwarding' => $forwarding,
                ];
            }
        }

        return $result;
    }
}
