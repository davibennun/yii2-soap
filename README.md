# Yii2 Soap Client

An elengant aproach to consuming SOAP API within Yii2.

[ - Incomplete documentation - ]

### Usage
First install this package via composer. Then config an extension like this
```
'soap' => [
          'class' => Davibennun\Soap\SoapComponent::class,
          'clients' => [
              [
                'client' => Davibennun\Soap\BaseSoapClient::class
                'wsdl' => 'path_to_your_wsdl_no_yii2_alias_allowed_yet',
                'options' => ['cache_wsdl' => WSDL_CACHE_NONE],
                'classMaps' => [['User', app\models\User::class]]
              ]
          ]
```

Now you are good to go:
```
        use Davibennun\Soap\SoapClientFacade as Client;
        ...
        public function actionViewUser() {
            $user = Client::getUserById('1');
            $this->render( 'view', ['user' => $user] );
        }
```

[ - Incomplete documentation - ]