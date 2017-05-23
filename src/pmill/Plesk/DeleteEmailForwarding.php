<?php
namespace pmill\Plesk;

class DeleteEmailForwarding extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
    <mail>
        <update>
            <remove>
                <filter>
                    <site-id>{SITE_ID}</site-id>
                    <mailname>
                        <name>{USERNAME}</name>
                        <forwarding>
                          {ADDRESSES}
                        </forwarding>
                    </mailname>
                </filter>
            </remove>
        </update>
    </mail>
</packet>
EOT;

    /**
     * @var int
     */
    public $id;

    /**
     * @var array
     */
    protected $default_params = [
        'site_id' => null,
        'username' => null,
    ];

    /**
     * @param array $config
     * @param array $params
     * @throws ApiRequestException
     */
    public function __construct($config, $params)
    {
        $addresses = [];
        if (isset($params['email'])) {
            if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
                throw new ApiRequestException("Invalid email submitted");
            }

            list($username, $domain) = explode("@", $params['email']);

            $request = new GetSite($config, ['domain' => $domain]);
            $site = $request->process();

            $params['site_id'] = $site['id'];
            $params['username'] = $username;
        }

        if (isset($params['addresses'])) {
            foreach ($params['addresses'] as $address)
            {
                $addresses[] = new Node('address', $address);
            }

            $params['addresses'] = new NodeList($addresses);
        }

        parent::__construct($config, $params);
    }

    /**
     * @param $xml
     * @return bool
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        $result = $xml->mail->update->remove->result;

        if ($result->status == 'error') {
            throw new ApiRequestException($result);
        }

        $this->id = (int)$result->id;
        return true;
    }
}
