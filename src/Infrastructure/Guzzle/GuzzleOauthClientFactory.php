<?php


namespace Infrastructure\Guzzle;


use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Sainsburys\Guzzle\Oauth2\GrantType\PasswordCredentials;
use Sainsburys\Guzzle\Oauth2\GrantType\RefreshToken;
use Sainsburys\Guzzle\Oauth2\Middleware\OAuthMiddleware;

class GuzzleOauthClientFactory
{

    public static function factor($base_uri, array $config)
    {
        $handlerStack = HandlerStack::create();
        $client = new Client(['handler'=> $handlerStack, 'base_uri' => $base_uri, 'auth' => 'oauth2']);

        /*$config = [
            PasswordCredentials::CONFIG_USERNAME => $username,
            PasswordCredentials::CONFIG_PASSWORD => $password,
            PasswordCredentials::CONFIG_CLIENT_ID => $clientId,
            PasswordCredentials::CONFIG_CLIENT_SECRET => $clientSecret,
            PasswordCredentials::CONFIG_TOKEN_URL => $tokenUrl,
            'scope' => null,
        ];*/

        $token = new PasswordCredentials($client, $config);
        $refreshToken = new RefreshToken($client, $config);
        $middleware = new OAuthMiddleware($client, $token, $refreshToken);

        $handlerStack->push($middleware->onBefore());
        $handlerStack->push($middleware->onFailure(5));

        return $client;
    }

}