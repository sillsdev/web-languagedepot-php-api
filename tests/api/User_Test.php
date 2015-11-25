<?php
use GuzzleHttp\Client;

require_once (__DIR__ . '/TestConfig.php');

require_once (API_TEST_PATH . '/ApiTestEnvironment.php');

class UserTest extends PHPUnit_Framework_TestCase
{

    public function testUserProjects_UserUnknown_404()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post('/api/user/bogus_user/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false
        ));
        $this->assertEquals('404', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header[0]);
        $result = $response->getBody();
        $result = json_decode($result);
        $this->assertEquals('Unknown user', $result->error);
    }

    public function testUserProjects_UserBadPassword_403()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post('/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'form-data' => array(
                'password' => 'bogus_password'
            )
        ));
        $this->assertEquals('403', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header[0]);
        $result = $response->getBody();
        $result = json_decode($result);
        $this->assertEquals('Bad password', $result->error);
    }

    public function testUserProjects_Ok()
    {
        $client = ApiTestEnvironment::client();

        $response = $client->post('/api/user/test/projects', array(
            'headers' => ApiTestEnvironment::headers(),
            'exceptions' => false,
            'form_params' => array(
                'password' => 'tset23'
            )
        ));
        $this->assertEquals('200', $response->getStatusCode());
        $header = $response->getHeader('Content-Type');
        $this->assertEquals('application/json', $header[0]);
        $result = $response->getBody();
        $result = json_decode($result);

        $expected0 = new \stdclass;
        $expected0->identifier = 'testpal-dictionary';
        $expected0->name = 'Test Palaso';
        $expected1 = new \stdclass;
        $expected1->identifier = 'lwl2';
        $expected1->name = 'Eastern Lawa';
        $expected = array($expected0, $expected1);
        $this->assertEquals($expected, $result);
    }

}