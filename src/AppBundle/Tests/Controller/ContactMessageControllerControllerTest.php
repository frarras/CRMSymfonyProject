<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactMessageControllerControllerTest extends WebTestCase
{
    public function testContactmail()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '@Route("/contact/mail")');
    }

}
