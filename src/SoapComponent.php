<?php


namespace Davibennun\Soap;


use Phpro\SoapClient\ClientBuilder;
use Phpro\SoapClient\ClientFactory;
use Phpro\SoapClient\Soap\ClassMap\ClassMap;

class SoapComponent {

    public $clients = [];

    /**
     * @param string $clientName
     * @param string $fullWsdlPath
     * @param array $classMaps
     * @param array $options
     * @return $this
     */
    public function register($clientName, $fullWsdlPath, $classMaps=[], $options=[])
    {
        $clientInfo = [
            'clientName'=> $clientName,
            'wsdl'=> $fullWsdlPath,
            'options'=> $options,
            'classMaps'=>$classMaps
        ];
        $this->clients[] = $clientInfo;

        return $this;
    }

    /**
     * Adds a class map to a client
     *
     * @param string $clientName Client name
     * @param string $type SOAP Type to be mapped to class
     * @param string $class Class to bo mapped to type
     * @return $this
     */
    public function addClassMap($clientName, $type, $class)
    {
        $info = $this->get($clientName);
        $info['classMaps'][] = [$type, $class];
        $this->set($info);
        return $this;
    }

    /**
     * Builds the Soap Client class
     * @param string $clientName soap client name
     * @param bool $rebuild force rebuild
     * @return \Phpro\SoapClient\ClientInterface
     */
    public function build($clientName, $rebuild=false)
    {
        //check if client was already built
        if(isset($this->get($clientName)['theClient']) && !$rebuild){
            return $this->get($clientName)['theClient'];
        }

        $clientInfo = $this->get($clientName);
        $clientFactory = new ClientFactory($clientInfo['clientName']);
        $clientBuilder = new ClientBuilder($clientFactory, $clientInfo['wsdl'], $clientInfo['options']);
        foreach($clientInfo['classMaps'] as $classMap)
        {
            $clientBuilder->addClassMap(new ClassMap($classMap[0], $classMap[1]));
        }

        $clientInfo['factory'] = $clientFactory;
        $clientInfo['builder'] = $clientBuilder;
        $clientInfo['theClient'] = $clientBuilder->build();

        $this->set($clientInfo);

        return $clientInfo['theClient'];
    }

    /**
     * Soap client info getter
     * @param string $clientName soap client name
     * @return mixed
     */
    public function get($clientName){
        return current(array_filter($this->clients, function($clientInfo) use ($clientName){
            return $clientInfo['clientName'] == $clientName;
        }));
    }

    /**
     * Soap client info setter
     * @param string $clientInfo client info arrat
     */
    public function set($clientInfo)
    {
        //Remove $cilentInfo from $this->clients (in case it exsits)
        $clientsClean = array_filter($this->clients, function ($client) use ($clientInfo) {
            return $client['clientName'] != $clientInfo['clientName'];
        });

        $clientsClean[] = $clientInfo;

        $this->clients = $clientsClean;
    }

}