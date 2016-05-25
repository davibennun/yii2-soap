<?php


namespace Davibennun\Soap;


abstract class SoapClientBaseFacade {

    /**
     * The underlying faker instance.
     *
     * @var \Phpro\SoapClient\Client
     */
    private static $instance;

    /**
     * Get the underlying faker instance.
     *
     * We'll always cache the instance and reuse it.
     *
     * @return \Phpro\SoapClient\Client
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = \Yii::$app->soap->build((new static)->getClientName());
        }
        return self::$instance;
    }

    /**
     * Reset the underlying faker instance.
     *
     * @return \Phpro\SoapClient\Client
     */
    public static function reset()
    {
        self::$instance = null;
        return self::instance();
    }

    /**
     * Return the client name for Soap Component.
     * Should be implemented by child class.
     *
     * @return string $clientName Client Name for SOAP Component
     */
    abstract public function getClientName();

    /**
     * Handle dynamic, static calls to the object.
     *
     * @codeCoverageIgnore
     *
     * @param string $method The method name.
     * @param array $arguments The arguments.
     *
     * @return mixed
     */
    public static function __callStatic($method, $arguments)
    {
        switch (count($arguments)) {
            case 0:
                return self::instance()->$method();
            case 1:
                return self::instance()->$method($arguments[0]);
            case 2:
                return self::instance()->$method($arguments[0], $arguments[1]);
            case 3:
                return self::instance()->$method($arguments[0], $arguments[1], $arguments[2]);
            default:
                return call_user_func_array([self::instance(), $method], $arguments);
        }
    }

}