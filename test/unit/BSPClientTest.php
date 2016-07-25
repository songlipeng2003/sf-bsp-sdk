<?php 
use songlipeng2003\sf\bsp\BSPClient;
use PHPUnit\Framework\TestCase;

class BSPClientTest extends PHPUnit_Framework_TestCase
{
    protected $client;

    public function setUp()
    {
        $client = new BSPClient();
        $client->url = 'http://218.17.248.244:11080/bsp-oisp/sfexpressService';
        $client->accessCode = 'BSPdevelop';
        $client->checkWord = 'j8DzkIFgmlomPt0aLuwU';
        $client->debug = true;

        $this->client = $client;
    }

    public function testOrderService()
    {
        $request = [
            'Order' => [
                '@attributes' => [
                    'orderid' => rand(100000, 999999),
                    'j_company' => '华米科技',
                    'j_contact' => '宋利鹏',
                    'j_tel' => '13855106511',
                    'j_country' => '中国',
                    'j_province' => '安徽',
                    'j_city' => '合肥',
                    'j_county' => '高新区',
                    'j_address' => '创新产业基地H8栋',
                    'd_company' => '北京华米科技',
                    'd_contact' => '测试',
                    'd_tel' => '电话号码',
                    'd_province' => '北京',
                    'd_city' => '北京',
                    'd_county' => '海淀区',
                    'd_address' => '上地软件园',
                ],
                'Cargo' => [
                    '@attributes' => [
                        'name' => '小米手环',
                        'count' => 1,
                    ]
                ]
            ]
        ];
        $result = $this->client->api('OrderService', $request);
        $this->assertEquals($result['Head'], 'OK');
        $this->assertArrayHasKey('OrderResponse', $result['Body']);
        $this->assertArrayHasKey('@attributes', $result['Body']['OrderResponse']);

        return $result['Body']['OrderResponse']['@attributes']['mailno'];
    }

    public function testRouteService()
    {
        $mailno = $this->testOrderService();

        $request = [
            'RouteRequest' =>[
                '@attributes' => [
                    'tracking_number' => $mailno
                ]
            ]
        ];

        $result = $this->client->api('RouteService', $request);

        $this->assertEquals($result['Head'], 'OK');
    }
}