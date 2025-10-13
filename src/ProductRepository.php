<?php

namespace Pdfsystems\WebDistributionSdk;

use GuzzleHttp\Exception\BadResponseException;
use Pdfsystems\WebDistributionSdk\Dtos\Company;
use Pdfsystems\WebDistributionSdk\Dtos\FreightResponse;
use Pdfsystems\WebDistributionSdk\Dtos\Product;
use Pdfsystems\WebDistributionSdk\Dtos\ProductTariffs;
use Pdfsystems\WebDistributionSdk\Dtos\Simple\Product as SimpleProduct;
use Pdfsystems\WebDistributionSdk\Exceptions\NotFoundException;
use Pdfsystems\WebDistributionSdk\Exceptions\ResponseException;
use Pdfsystems\WebDistributionSdk\Requests\FreightRequest;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;

class ProductRepository extends AbstractRepository
{
    /**
     * @param Company $company
     * @param callable $callback
     * @param array $options
     * @param int $perPage
     * @return void
     * @throws UnknownProperties
     */
    public function iterate(Company $company, callable $callback, array $options = [], int $perPage = 128): void
    {
        /**
         * TODO: Since the current WD API does not allow both company and line to be specified, if the caller of this
         * function passes in a line_id as part of the $options parameter that is not for the company specified in the
         * $company parameter, they will receive products that do not match the specified company.
         * This scenario should probably be addressed by performing a check before loading any products that the company
         * for the specified line matches the company passed in.
         */

        $requestOptions = [
            'with' => [
                'style.productCategoryCode',
                'style.primaryPrice',
                'company',
                'line',
                'primaryBook',
                'style.sellingUnit',
                'style.millUnit',
            ],
            'count' => $perPage,
            'page' => 1,
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

            $requestOptions['page']++;
        } while (! empty($response));
    }

    /**
     * @throws UnknownProperties
     * @throws ResponseException
     */
    public function find(Company $company, string $itemNumber): Product
    {
        $requestOptions = [
            'company' => $company->id,
            'search' => '#' . $itemNumber,
            'trashed' => 'true',
            'with' => [
                'style.productCategoryCode',
                'style.primaryPrice',
                'style.customFields',
                'company',
                'customFields',
                'discontinueCode',
                'line',
                'primaryBook',
                'style.sellingUnit',
                'style.millUnit',
                'style.vendor',
            ],
        ];
        $response = $this->client->getJson('api/item', $requestOptions);
        if (count($response) > 0) {
            return new Product($response[0]);
        } else {
            throw new NotFoundException();
        }
    }

    /**
     * @throws UnknownProperties
     * @throws ResponseException
     */
    public function findById(int $id): Product
    {
        $requestOptions = [
            'with' => [
                'style.productCategoryCode',
                'style.primaryPrice',
                'style.customFields',
                'company',
                'customFields',
                'discontinueCode',
                'line',
                'primaryBook',
                'style.sellingUnit',
                'style.millUnit',
                'style.vendor',
            ],
        ];

        try {
            return new Product(
                $this->client->getJson("api/item/$id", $requestOptions)
            );
        } catch (BadResponseException) {
            throw new NotFoundException();
        }
    }

    /**
     * @throws UnknownProperties
     */
    public function findSimple(Company $company, string $itemNumber): SimpleProduct
    {
        try {
            return $this->client->getDto('api/simple/item/lookup', SimpleProduct::class, [
                'sku' => $itemNumber,
            ], [
                'X-Company-ID' => $company->id,
            ]);
        } catch (ResponseException $e) {
            if ($e->getCode() === 404) {
                throw new NotFoundException("Product $itemNumber not found", $e->getCode(), $e);
            } else {
                throw $e;
            }
        }
    }

    /**
     * @throws UnknownProperties
     */
    public function update(Product $product): Product
    {
        $this->client->putJson('api/style/' . $product->style_id, [
            'name' => $product->style_name,
            'content' => $product->content,
            'width' => $product->width,
            'repeat' => $product->repeat,
            'custom_fields' => $product->custom_fields_style,
        ]);
        $this->client->putJson('api/item/' . $product->id, [
            'item_number' => $product->item_number,
            'color_name' => $product->color_name,
            'warehouse_location' => $product->warehouse_location,
            'sample_warehouse_location' => $product->warehouse_location_sample,
            'custom_fields' => $product->custom_fields_item,
            'date_discontinued' => $product->discontinued_date,
        ]);
    }

    /**
     * @throws UnknownProperties
     */
    public function freight(Product|int $product, FreightRequest $request): FreightResponse
    {
        $request->validate();

        if ($product instanceof Product) {
            $product = $product->id;
        }

        $query = [
            'postal_code' => $request->postalCode,
            'quantity' => $request->quantity,
            'country' => $request->country,
        ];

        return new FreightResponse(
            $this->client->getJson("api/item/$product/freight", $query)
        );
    }

    /**
     * @throws UnknownProperties
     */
    public function tariffs(Product|int $product): ProductTariffs
    {
        if ($product instanceof Product) {
            $product = $product->id;
        }

        return new ProductTariffs(
            $this->client->getJson("api/item/$product/tariffs")
        );
    }
}
