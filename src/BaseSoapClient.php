<?php

namespace Davibennun\Soap;

use Phpro\SoapClient\Client;
use Phpro\SoapClient\Event;
use Phpro\SoapClient\Events;
use Phpro\SoapClient\Type\RequestInterface;
use Phpro\SoapClient\Type\ResultProviderInterface;
use SoapFault;

class BaseSoapClient extends Client{

    protected function call($method, RequestInterface $request)
    {
        $requestEvent = new Event\RequestEvent($this, $method, $request);
        $this->dispatcher->dispatch(Events::REQUEST, $requestEvent);

        try {
            $result = $this->soapClient->$method($request);
            if ($result instanceof ResultProviderInterface) {
                $result = $result->getResult();
            }
        } catch (SoapFault $soapFault) {
            $this->dispatcher->dispatch(Events::FAULT, new Event\FaultEvent($this, $soapFault, $requestEvent));
            throw $soapFault;
        }

        //Commenting this line until I implement handling other return values
        //$this->dispatcher->dispatch(Events::RESPONSE, new Event\ResponseEvent($this, $requestEvent, $result));
        return $result;
    }

}