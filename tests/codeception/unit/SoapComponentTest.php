<?php

use Davibennun\Soap\SoapComponent;
use yii\codeception\TestCase;

class SoapComponentTest extends TestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function setUp()
    {
        parent::setup();

        $this->sampleConfig = [
            'clientName' => 'Phpro\SoapClient\Client',
            'wsdl' => Yii::getAlias('@tests').'/codeception/unit/dummy.wsdl',
            'options' => ['cache_wsdl' => WSDL_CACHE_NONE],
            'classMaps' => [['Model', '\yii\db\ActiveRecord']]
        ];
        $this->soap = new SoapComponent();
    }

    public function testItBehavesAsAYii2Module()
    {
        $config = [];
        $config['class'] = SoapComponent::class;
        $config['clients'] = [$this->sampleConfig];
        Yii::$app->set('soap',$config);
        $this->assertEquals($this->sampleConfig, Yii::$app->soap->clients[0]);
    }

    public function testItRegisters()
    {
        extract($this->sampleConfig);
        $this->soap->register($clientName, $wsdl, $classMaps, $options);

        $this->assertEquals($this->sampleConfig, $this->soap->clients[0]);
    }

    public function testItGetsClient()
    {
        $this->registerClient();
        $this->assertEquals($this->sampleConfig, $this->soap->get($this->sampleConfig['clientName']));
    }

    public function testItSetsClient()
    {
        $this->registerClient();
        $newConfig = $this->sampleConfig;
        $newConfig['wsdl'] = 'newFile';
        $this->soap->set($newConfig);
        $this->assertEquals($this->soap->get($newConfig['clientName']), $newConfig);
        $this->assertCount(1, $this->soap->clients);
    }

    public function testItAddsAClassMap()
    {
        $this->soap->register('ClientName', 'wsdl');
        $this->soap->addClassMap('ClientName', 'User', 'UserClass');

        $this->assertEquals(['User', 'UserClass'], $this->soap->clients[0]['classMaps'][0]);
    }

    public function testItBuilds()
    {
        $this->registerClient();
        $this->soap->build($this->sampleConfig['clientName']);

        $this->assertArrayHasKeys(['factory','builder','theClient'],$this->soap->clients[0]);

        $this->assertInstanceOf('Phpro\SoapClient\ClientInterface', $this->soap->clients[0]['theClient']);
    }

    public function registerClient($config=null)
    {
        extract($this->sampleConfig ? $this->sampleConfig : $config);
        $this->soap->register($clientName, $wsdl, $classMaps, $options);
    }

    public function assertArrayHasKeys($keys, $array)
    {
        foreach($keys as $key) {
            $this->assertArrayHasKey($key, $array);
        }
    }




}