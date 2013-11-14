<?php

namespace fibe\Bundle\WWWConfBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentationControllerTest extends WebTestCase
{
    public function testDocumentation()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/documentation');
    }

}
