<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ChatApiTest extends TestCase
{
    private $apiUrl = 'http://localhost:8080';

    public function testCreateGroup()
    {
        $data = [
            'name' => 'Test Group'
        ];

        $response = $this->postRequest('/groups', $data);

        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey('id', $response['data']);
        $this->assertEquals('Test Group', $response['data']['name']);
    }

    public function testJoinGroup()
    {
        $groupId = 1;
        $data = [
            'userId' => 1
        ];

        $response = $this->postRequest("/groups/$groupId/join", $data);

        $this->assertEquals(200, $response['statusCode']);
        $this->assertEquals($groupId, $response['data']['groupId']);
        $this->assertEquals(1, $response['data']['userId']);
    }

    public function testSendMessage()
    {
        $groupId = 1;
        $data = [
            'userId' => 1,
            'message' => 'Hello, world!'
        ];

        $response = $this->postRequest("/groups/$groupId/messages", $data);

        $this->assertEquals(200, $response['statusCode']);
        $this->assertArrayHasKey('id', $response['data']);
        $this->assertEquals($groupId, $response['data']['groupId']);
        $this->assertEquals(1, $response['data']['userId']);
        $this->assertEquals('Hello, world!', $response['data']['message']);
    }

    public function testGetMessages()
    {
        $groupId = 1;

        $response = $this->getRequest("/groups/$groupId/messages");

        $this->assertEquals(200, $response['statusCode']);
        $this->assertIsArray($response['data']);
    }

    private function postRequest($url, $data)
    {
        $curl = curl_init($this->apiUrl . $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [
            'statusCode' => $statusCode,
            'data' => json_decode($response, true)
        ];
    }

    private function getRequest($url)
    {
        $curl = curl_init($this->apiUrl . $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        return [
            'statusCode' => $statusCode,
            'data' => json_decode($response, true)
        ];
    }
}
