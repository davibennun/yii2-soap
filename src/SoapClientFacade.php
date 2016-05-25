<?php


namespace Davibennun\Soap;

class SoapClientFacade extends SoapClientBaseFacade
{

    public function getClientName()
    {
        return BaseSoapClient::class;
    }

}