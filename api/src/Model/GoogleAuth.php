<?php
namespace eu\luige\plagiarism\model;


use GuzzleHttp\Client;

class GoogleAuth extends Model
{
    public function authenticate($email, $token)
    {
        $httpClient = new Client();

        $response = json_decode($httpClient->get($this->config['google']['auth_url'], [
                'access_token' => $token
            ]
        )->getBody()->getContents(), true);


    }
}