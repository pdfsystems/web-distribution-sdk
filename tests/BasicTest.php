<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Pdfsystems\WebDistributionSdk\Client;
use Pdfsystems\WebDistributionSdk\Dtos\ApiKey;
use Pdfsystems\WebDistributionSdk\Dtos\User;

it('can create client', function () {
    $client = new Client(['token' => 'foobar']);

    expect($client)->toBeInstanceOf(Client::class);
});

it('can authenticate via api keys', function () {
    $mockUser = new User(
        id: 1,
        initials: 'ABC',
        name: 'John Smith',
        email: 'john@example.com'
    );
    $mock = new MockHandler([
        new Response(200, ['content-type' => 'application/json'], $mockUser->toJson()),
    ]);
    $client = new Client(['token' => 'foobar'], handler: HandlerStack::create($mock));

    $user = $client->getAuthenticatedUser();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->id)->toBe(1)
        ->and($user->initials)->toBe('ABC')
        ->and($user->name)->toBe('John Smith')
        ->and($user->email)->toBe('john@example.com');
});

it('can authenticate via email/password', function () {
    $mockUser = new User(
        id: 1,
        initials: 'ABC',
        name: 'John Smith',
        email: 'john@example.com'
    );
    $mock = new MockHandler([
        new Response(200),
        new Response(200, ['content-type' => 'application/json'], $mockUser->toJson()),
    ]);
    $client = new Client(['email' => 'john@pdfsystems.com', 'password' => 'password'], handler: HandlerStack::create($mock));

    $user = $client->getAuthenticatedUser();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->id)->toBe(1)
        ->and($user->initials)->toBe('ABC')
        ->and($user->name)->toBe('John Smith')
        ->and($user->email)->toBe('john@example.com');
});

it('can create API keys', function () {
    $mockApiKey = new ApiKey(
        id: 1,
        name: 'API Key',
        token: 'cafebabe'
    );
    $mock = new MockHandler([
        new Response(200),
        new Response(201, ['content-type' => 'application/json'], $mockApiKey->toJson()),
    ]);
    $client = new Client(['email' => 'john@example.com', 'password' => 'password'], handler: HandlerStack::create($mock));
    $apiKey = $client->createApiKey($mockApiKey);

    expect($apiKey->id)->toBe(1)
        ->and($apiKey->name)->toBe('API Key')
        ->and($apiKey->token)->toBe('cafebabe');
});
