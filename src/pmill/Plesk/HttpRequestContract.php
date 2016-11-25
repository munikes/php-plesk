<?php
namespace pmill\Plesk;

interface HttpRequestContract
{
    /**
     * CurlHttpRequest constructor.
     * @param $host
     * @param $port
     */
    public function __construct($host, $port);

    /**
     * @param $username
     * @param $password
     */
    public function setCredentials($username, $password);

    /**
     * @param $body
     * @return bool|string
     * @throws ApiRequestException
     */
    public function sendRequest($body);
}
