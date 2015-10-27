<?php
namespace Infrastructure\Google;

use Assert\Assertion;

class Configurator
{

    private $serviceEmail;
    private $key;

    public function __construct($serviceEmail, $pathToP12)
    {
        Assertion::email($serviceEmail);
        Assertion::readable($pathToP12);
        Assertion::file($pathToP12);

        $this->serviceEmail = $serviceEmail;
        $this->key = file_get_contents($pathToP12);
    }

    public function configure(\Google_Client $client)
    {
        $cred = new \Google_Auth_AssertionCredentials(
            $this->serviceEmail,
            array(\Google_Service_Analytics::ANALYTICS_READONLY),
            $this->key
        );
        $client->setAssertionCredentials($cred);

        if ($client->getAuth()->isAccessTokenExpired()) {
            $client->getAuth()->refreshTokenWithAssertion($cred);
        }
    }

}
