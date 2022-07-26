<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Pdfsystems\WebDistributionSdk\Client;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Currency;
use Pdfsystems\WebDistributionSdk\Dtos\Line;
use Pdfsystems\WebDistributionSdk\Dtos\User;

it('can load a list of companies', function () {
    $mockUser = new User(
        id: 1,
        initials: 'ABC',
        name: 'John Smith',
        email: 'john@example.com'
    );

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
        new Response(200, ['content-type' => 'application/json'], $mockUser->toJson()),
        new Response(200, ['content-type' => 'application/json'], json_encode([$mockCompany->toArray()])),
    ]);
    $client = new Client(handler: HandlerStack::create($mock));
    $client->authenticateWithApiKey('foobar');

    $response = $client->companies()->list();

    expect($response)->toBeArray();
    expect($response[0])->toBeInstanceOf(Company::class);
    expect($response[0]->id)->toBe(1);
    expect($response[0]->name)->toBe('Team Designers and Associates');
});
