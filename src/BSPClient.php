<?php 
namespace songlipeng2003\sf\bsp;

use GuzzleHttp\Client;

class BSPClient
{
    public $accessCode;
    public $checkWord;

    public $url;
    private $_client;

    public $debug = false;

    public function BSPClient($url=null, $accessCode=null, $checkWord=null)
    {
        $this->accessCode = $accessCode;
        $this->checkWord = $checkWord;
        $this->url = $url;
    }

    public function api($service, $data)
    {
        $request = [
            '@attributes' => [
                'service' => $service,
                'lang' => 'zh-CN',
            ],
            'Head' => $this->accessCode,
            'Body' => $data
        ];

        $xml = Array2XML::createXML('Request', $request);
        $xml = $xml->saveXML();

        if($this->debug){
            echo 'request:' . $xml;
        }

        $verifyCode = base64_encode(md5($xml . $this->checkWord, true));

        $client = $this->getClient();
        $response = $client->post($this->url, ['form_params' => ['xml' => (string)$xml, 'verifyCode' => $verifyCode]]);
        $code = $response->getStatusCode();
        if($code==200){
            $stringBody = (string) $response->getBody();

            if($this->debug){
                echo 'response:' . $stringBody;
            }

            $result = simplexml_load_string($stringBody);
            $result = json_encode($result);
            $result = json_decode($result, true);

            return $result;
        }else{
            $reason = $response->getReasonPhrase();
        }
    }

    public function getClient()
    {
        if(!$this->_client){
            $this->_client = new Client([]);
        }

        return $this->_client;
    }
}