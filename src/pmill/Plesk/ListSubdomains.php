<?php
namespace pmill\Plesk;

use pmill\Plesk\Helper\Xml;

class ListSubdomains extends BaseRequest
{
    /**
     * @var string
     */
    public $xml_packet = <<<EOT
<?xml version="1.0"?>
<packet version="{VERSION}">
    <subdomain>
        <get>
            {FILTER}
        </get>
    </subdomain> 
</packet>
EOT;

    /**
     * @var array
     */
    protected $default_params = [
        'filter' => '<filter/>',
    ];

    /**
     * ListSubdomains constructor.
     * @param array $config
     * @param array $params
     */
    public function __construct(array $config, array $params = [])
    {
        $this->default_params['filter'] = new Node('filter');

        if (isset($params['domain'])) {
            $ownerParentNameNode = new Node('parent-name', $params['domain']);
            $params['filter'] = new Node('filter', $ownerParentNameNode);
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

        foreach ($xml->subdomain->get->result as $node) {
          if (isset($node->id)){
            $result[] = [
                'id' => (int)$node->id,
                'status' => (string)$node->status,
                'parent' => (string)$node->data->parent,
                'name' => (string)$node->data->name,
                'php' => (string)Xml::findProperty($node->data, 'php') === 'true' ? true: false,
                'php_handler_id' => (string)Xml::findProperty($node->data, 'php_handler_id'),
                'www_root' => (string)Xml::findProperty($node->data, 'www_root'),
            ];
          }
        }

        return $result;
    }
}
