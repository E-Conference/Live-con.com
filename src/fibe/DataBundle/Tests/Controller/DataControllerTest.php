<?php

namespace fibe\DataBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DataControllerTest extends WebTestCase
{
    public function testPersonlist()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/persons');
    }

    public function testPerson()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/person/{id}');
    }

}
