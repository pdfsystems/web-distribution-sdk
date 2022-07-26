<?php

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Pdfsystems\WebDistributionSdk\Client;
use Pdfsystems\WebDistributionSdk\Dtos\ApiKey;
use Pdfsystems\WebDistributionSdk\Dtos\User;

it('can create client', function () {
    $client = new Client();

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
    $client = new Client(handler: HandlerStack::create($mock));

    $user = $client->authenticateWithApiKey('foobar');

    expect($user)->toBeInstanceOf(User::class);
    expect($user->id)->toBe(1);
    expect($user->initials)->toBe('ABC');
    expect($user->name)->toBe('John Smith');
    expect($user->email)->toBe('john@example.com');
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
    $client = new Client(handler: HandlerStack::create($mock));

    $user = $client->authenticateWithCredentials('john@example.com', 'password');

    expect($user)->toBeInstanceOf(User::class);
    expect($user->id)->toBe(1);
    expect($user->initials)->toBe('ABC');
    expect($user->name)->toBe('John Smith');
    expect($user->email)->toBe('john@example.com');
});

it('can create API keys', function () {
    $mockUser = new User(
        id: 1,
        initials: 'ABC',
        name: 'John Smith',
        email: 'john@example.com'
    );
    $mockApiKey = new ApiKey(
        id: 1,
        name: 'API Key',
        token: 'cafebabe'
    );
    $mock = new MockHandler([
        new Response(200),
        new Response(200, ['content-type' => 'application/json'], $mockUser->toJson()),
        new Response(201, ['content-type' => 'application/json'], $mockApiKey->toJson()),
    ]);
    $client = new Client(handler: HandlerStack::create($mock));

    $client->authenticateWithCredentials('john@example.com', 'password');
    $apiKey = $client->createApiKey($mockApiKey);

    expect($apiKey->id)->toBe(1);
    expect($apiKey->name)->toBe('API Key');
    expect($apiKey->token)->toBe('cafebabe');
});
