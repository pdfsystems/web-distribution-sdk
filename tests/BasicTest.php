<?php

use Pdfsystems\WebDistributionSdk\Client;

it('can create client', function () {
    $client = new Client();

    expect($client)->toBeInstanceOf(Client::class);
});
