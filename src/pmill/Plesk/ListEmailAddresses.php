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
      {USERNAME}
		</filter>
		<mailbox/>
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
        if (!empty($params['username']))
        {
          $params['username'] = new Node('name', $params['username']);
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
            $result[] = [
                'status' => (string)$node->status,
                'id' => (int)$node->mailname->id,
                'username' => (string)$node->mailname->name,
                'enabled' => (bool)$node->mailname->mailbox->enabled,
                'quota' => (int)$node->mailname->mailbox->quota,
                'password' => (string)$node->mailname->password->value,
            ];
        }

        return $result;
    }
}
