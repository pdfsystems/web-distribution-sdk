<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Pdfsystems\WebDistributionSdk\Client;
use Pdfsystems\WebDistributionSdk\Drivers\GuzzleDriver;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Currency;
use Pdfsystems\WebDistributionSdk\Dtos\Line;

it('can load a list of companies', function () {
    $mockCompany = new Company(
        id: 1,
        name: 'Team Designers and Associates',
        currency: new Currency(
            id: 0,
            name: 'United States Dollar',
            code: 'USD',
            symbol: '$'
        ),
        lines: [
            new Line(
                id: 1,
                name: 'Team Designers and Associates',
            ),
        ]
    );
    $mock = new MockHandler([
        new Response(200, ['content-type' => 'application/json'], json_encode([$mockCompany->toArray()])),
    ]);
    $client = new Client(new GuzzleDriver('foobar', handler: HandlerStack::create($mock)));

    $response = $client->companies()->list();

    expect($response)->toBeArray()
        ->and($response[0])->toBeInstanceOf(Company::class)
        ->and($response[0]->id)->toBe(1)
        ->and($response[0]->name)->toBe('Team Designers and Associates');
});
