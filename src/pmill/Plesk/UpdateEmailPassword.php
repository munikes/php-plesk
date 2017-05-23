<?php
namespace pmill\Plesk;

class UpdateEmailPassword extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
    <mail>
        <update>
            <set>
                <filter>
                    <site-id>{SITE_ID}</site-id>
                    <mailname>
                        <name>{USERNAME}</name>
                        <password>
                          <value>{PASSWORD}</value>
                        </password>
                    </mailname>
                </filter>
            </set>
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
        'password' => null,
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
            $info = $request->process();

            $params['site_id'] = $info['id'];
            $params['username'] = $username;
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
        $result = $xml->mail->update->set->result;

        if ($result->status == 'error') {
            throw new ApiRequestException($result);
        }

        $this->id = (int)$result->id;
        return true;
    }
}
