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
                        {FORWARDING}
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
        if (isset($params['email'])) {
            if (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
                throw new ApiRequestException("Invalid email submitted");
            }

            list($username, $domain) = explode("@", $params['email']);

            $request = new GetSite($config, ['domain' => $domain]);
            $site = $request->process();

            $params['site_id'] = $site['id'];
            $params['username'] = $username;

            $request = new GetEmailAddress($config, ['domain' => $domain]);
            $email = $request->process();
        }

        $key = array_search($params['forward'], $email['forwarding']);
        unset($email['forwarding'][$key]);
        if (count($email['forwarding']) == 0)
        {
          $request = new DisableEmailForwarding($config, $params);
          return true;
        }
        else
        {


          parent::__construct($config, $params);
        }

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
