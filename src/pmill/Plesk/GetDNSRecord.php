<?php
namespace pmill\Plesk;

class GetDNSRecord extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="1.6.7.0">
<dns>
  <get_rec>
    <filter>
      <id>{ID}</id>
    </filter>
	</get_rec>
</dns>
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'id' => null,
    ];

    /**
     * @param array $config
     * @param array $params
     * @throws ApiRequestException
     */
    public function __construct(array $config, $params = [])
    {
      if (isset($params['domain'])){
        $request = new ListDNSRecords($config);
        $records = $request->process();
        foreach($records as $record){
          if($record['host'] == $params['domain'] and $record['type'] == $params['type']){
            $params['id'] = $record['id'];
          }else{
            $msg = 'There is not a DNS record with type: '. $params['type'].' ,and this name: '.$params['domain'];
            throw new ApiRequestException($msg);
          }
        }

      }

      parent::__construct($config, $params);
    }

    /**
     * @param $xml
     * @return array
     * @throws ApiRequestException
     */
    protected function processResponse($xml)
    {
        $record = $xml->dns->get_rec->result;

        if ((string)$record->status == 'error') {
            throw new ApiRequestException($record);
        }
        if ((string)$record->result->status == 'error') {
            throw new ApiRequestException($record->result);
        }

        return [
            'id' => (int)$record->id,
            'status' => (string)$record->status,
            'site-id' => (int)$record->data->{'site-id'},
            'type' => (string)$record->data->type,
            'host' => (string)$record->data->host,
            'value' => (string)$record->data->value,
            'opt' => (string)$record->data->opt,
        ];
    }
}
