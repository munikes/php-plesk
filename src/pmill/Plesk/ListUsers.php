<?php
namespace pmill\Plesk;

class ListUsers extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="1.6.7.0">
<user>
    <get>
        <filter>
            <all/>
        </filter>
        <dataset>
            <gen-info/>
            <roles/>
        </dataset>
    </get>
</user>
</packet>
EOT;

    /**
     * @param $xml
     * @return array
     */
    protected function processResponse($xml)
    {
        $result = [];

        for ($i = 0; $i < count($xml->user->get->result); $i++) {
            $user = $xml->user->get->result[$i];

            $result[] = [
                'id' => (int)$user->id,
                'filter-id' => (int)$user->filter_id,
                'status' => (string)$user->status,
                'login' => (string)$user->data->gen_info->login,
                'name' => (string)$user->data->gen_info->name,
                'owner-guid' => (string)$user->data->gen_info->owner_guid,
                'status' => (string)$user->data->gen_info->status,
                'guid' => (string)$user->data->gen_info->guid,
                'is-built-in' => (int)$user->data->gen_info->is_built_in,
                'subcription-domain-id' => (int)$user->data->gen_info->subcription_domain_id,
                'email' => (string)$user->data->gen_info->email,
                'contact-info' => [
                    'company' => (string)$user->data->gen_info->contact_info->company,
                    'phone' => (string)$user->data->gen_info->contact_info->phone,
                    'fax' => (string)$user->data->gen_info->contact_info->fax,
                    'address' => (string)$user->data->gen_info->contact_info->address,
                    'city' => (string)$user->data->gen_info->contact_info->city,
                    'state' => (string)$user->data->gen_info->contact_info->state,
                    'zip' => (string)$user->data->gen_info->contact_info->zip,
                    'country' => (string)$user->data->gen_info->contact_info->country,
                    'im' => (string)$user->data->gen_info->contact_info->im,
                    'imtype' => (string)$user->data->gen_info->contact_info->imtype,
                    'comment' => (string)$user->data->gen_info->contact_info->comment,
                    'locale' => (string)$user->data->gen_info->contact_info->locale,
                ],
                'role' => (string)$user->data->roles->name,
            ];
        }

        return $result;
    }
}
