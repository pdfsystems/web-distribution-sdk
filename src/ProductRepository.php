<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\GuzzleException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\Product;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ProductRepository extends AbstractRepository
{
    /**
     * @param Company $company
     * @param callable $callback
     * @param array $options
     * @param int $perPage
     * @return void
     * @throws GuzzleException
     * @throws UnknownProperties
     */
    public function iterate(Company $company, callable $callback, array $options = [], int $perPage = 128): void
    {
        $page = 1;
        $requestOptions = [
            'with' => [
                'style.productCategoryCode',
                'style.primaryPrice',
                'company',
                'line',
            ],
            'count' => $perPage,
            'page' => $page++,
        ];

        if (! empty($options['line_id'])) {
            $requestOptions['line'] = $options['line_id'];
        } else {
            $requestOptions['company'] = $company->id;
        }

        do {
            $response = $this->client->getJson('api/item', $requestOptions);

            foreach ($response as $product) {
                $callback(new Product($product));
            }
        } while (! empty($response));
    }
}
