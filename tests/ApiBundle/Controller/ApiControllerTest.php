<?php

namespace ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();

        $client->request('GET', '/api/posts');
        $data = json_decode($client->getResponse()->getContent());
        $this->assertAttributeInternalType('boolean', 'error', $data);
        $this->assertObjectHasAttribute('data', $data);
        $this->assertObjectHasAttribute('messages', $data);
    }

    public function testPost()
    {
        $client = static::createClient();

        $client->request('POST', '/api/post', array('title' => '', 'body' => ''));
        $data = json_decode($client->getResponse()->getContent());
        $this->assertAttributeEquals(true, 'error', $data);
        $this->assertAttributeEmpty('data', $data);
        $this->assertAttributeNotEmpty('messages', $data);

        $client->request('POST', '/api/post', array('title' => 'T', 'body' => 'Test'));
        $data = json_decode($client->getResponse()->getContent());
        $this->assertAttributeEquals(true, 'error', $data);
        $this->assertAttributeEmpty('data', $data);
        $this->assertAttributeNotEmpty('messages', $data);

        $client->request('POST', '/api/post', array('title' => 'TestTitle', 'body' => 'TestBody'));
        $data = json_decode($client->getResponse()->getContent());
        $this->assertAttributeEquals(false, 'error', $data);
        $this->assertAttributeNotEmpty('data', $data);
        $this->assertAttributeEmpty('messages', $data);
        $this->assertAttributeInternalType('int', 'id', $data->data[0]);
        $this->assertAttributeEquals('TestTitle', 'title', $data->data[0]);
        $this->assertAttributeEquals('TestBody', 'body', $data->data[0]);
    }
}
