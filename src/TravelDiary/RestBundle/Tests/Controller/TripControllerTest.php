<?php

namespace TravelDiary\RestBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TripControllerTest
 */
class TripControllerTest extends WebTestCase
{
    public function createAuthenticatedClient()
    {
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/v1/login_check',
            array(
                '_username' => '',
                '_password' => '',
            )
        );

        $data = json_decode($client->getResponse()->getContent(), true);

        $client = static::createClient();
        $client->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $data['token']));

        return $client;
    }

    public function testGetTrips()
    {
        $client = $this->createAuthenticatedClient();
        $client->request('GET', '/api/v1/trips');

        $data = json_decode($client->getResponse()->getContent(), true);
    }
}