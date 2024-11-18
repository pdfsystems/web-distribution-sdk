# Changelog

All notable changes to `web-distribution-sdk` will be documented in this file.

## 2.23.1 - 2024-11-18

Update rpungello/sdk-client

## 2.23.0 - 2024-11-18

Update rpungello/sdk-client

## 2.22.0 - 2024-11-18

Add resale certificate features

- Files
- Approval status

## 2.21.0 - 2024-11-08

Added simple item API

## 2.20.0 - 2024-10-30

- Added customer iterator
- Load shipping info for customers

## 2.19.4 - 2024-10-29

Added sample transaction numbers

## 2.19.3 - 2024-10-29

Assign ship to country & state IDs for new sample transactions

## 2.19.2 - 2024-10-29

Load default sample types with default lines

## 2.19.0 - 2024-10-29

Load default lines for companies

## 2.18.0 - 2024-10-29

Add sample transactions

## 2.17.0 - 2024-10-28

- Added country repository
- Added list function for states

## 2.16.0 - 2024-10-24

Added function to list custom fields for a company

## 2.15.0 - 2024-10-24

Added type casting for custom field values on toArray()

## 2.14.0 - 2024-10-24

Switch to using an interface for HasCustomFields

## 2.13.5 - 2024-10-24

Added helper function for setting custom fields

## 2.13.4 - 2024-10-24

Add support for updating product custom fields

## 2.13.3 - 2024-10-23

Added custom field options for drop-down menus

## 2.13.2 - 2024-10-23

Add trait for custom field resources, which allows product custom fields to be pulled from both item and styles in WD

## 2.13.1 - 2024-10-23

Load custom fields when loading customers by customer number

## 2.13.0 - 2024-10-23

Added read-only custom fields for products

## 2.12.0 - 2024-10-23

Added support for customer custom fields

## 2.11.1 - 2024-09-27

Added formatted address property

## 2.11.0 - 2024-09-27

- Added ship to creation
- Added customer search

## 2.10.0 - 2024-09-27

Added master and national rep info

## 2.9.0 - 2024-09-20

- Add customer repository
- Add employee repository
- Add resale certificates
- Add state repository

## 2.8.0 - 2024-09-12

Added function to refresh OT data for a WD transaction

## 2.7.0 - 2024-09-11

Add functions for loading forms for transactions

## 2.6.0 - 2024-09-10

Add functions to load reps by ID or company and rep code

## 2.5.2 - 2024-08-06

Allow reports to be created for other WD users

## 2.5.1 - 2024-08-05

Add user to transactions

## 2.5.0 - 2024-08-05

Add the ability to create reports

## 2.4.0 - 2024-08-05

Add custom fields to transactions

## 2.3.1 - 2024-08-05

Add reps as transaction fields

## 2.3.0 - 2024-08-05

Add some transaction + rep fields

## 2.2.2 - 2024-08-05

Improve transaction relation handling

## 2.2.1 - 2024-08-05

Fix version

## 2.2.0 - 2024-08-05

- Added support for listing lines & reps
- Added transaction iteration

## 2.1.2 - 2024-06-26

Fix version in composer.json

## 2.1.1 - 2024-06-26

Minor improvements

## 2.1.0 - 2024-06-26

Add vendor info to products

## 2.0.0 - 2024-03-26

Migrate to using rpungello/sdk-client

## 1.20.0 - 2024-03-07

Add order portal URIs

## 1.19.0 - 2024-03-06

Added function for generating payment URIs for transactions

## 1.18.0 - 2024-03-04

Added OT export flag to inventory DTOs

## 1.17.0 - 2024-01-31

Added support for project users

## 1.16.3 - 2024-01-18

Allow null color names with inventory

## 1.16.2 - 2023-09-26

Added date received to inventory

## 1.16.1 - 2023-09-26

Added warehouse info to inventory

## 1.16.0 - 2023-09-25

Added ability to load products by ID directly

## 1.15.0 - 2023-09-07

Added ability to allocate to multiple pieces

## 1.14.0 - 2023-08-03

- Added transaction holds

## 1.13.1 - 2023-08-03

- Added ability to unallocate/allocate pieces by IDs alone (without needing a DTO)

## 1.13.0 - 2023-08-02

- Added ability to load order info
- Added ability to unallocate orders
- Added ability to allocate orders to a single piece

## 1.12.0 - 2023-05-02

- Added discontinued information to products
- Added deletion info to products

## 1.11.0 - 2023-04-25

- Added the ability to update products

## 1.10.1 - 2023-03-24

- Allow user ID to be overridden when receiving samples

## 1.10.0 - 2023-03-24

- Added ability to load users for a company

## 1.9.1 - 2023-03-23

- Added warehouse locations to products

## 1.9.0 - 2023-03-23

- Added ability to get sample inventory on hand
- Added ability to receive sample inventory

## 1.8.0 - 2023-03-22

- Added ability to update pieces

## 1.7.1 - 2023-03-22

- Added units of measure to products

## 1.7.0 - 2023-03-22

- Added book information to products

## 1.6.0 - 2023-03-01

- Added ability to load inventory for a given product
- Added ability to load incoming purchase orders for a given product
- Added ability to load a company by ID

## 1.5.0 - 2023-02-24

Added inventory

## 1.4.2 - 2022-11-30

- Handle situation where a freight rate is null

## 1.4.1 - 2022-11-17

Use float for weight and packing charge

## 1.3.0 - 2022-11-16

Add freight calculations

## 1.2.1 - 2022-07-26

Fix issue with product paging

## 1.2.0 - 2022-07-26

Add ability to filter products by line

## 1.1.0 - 2022-07-26

Add ability to create and view API keys

## 1.0.1 - 2022-07-26

Add MapFrom for style fields

## 1.0.0 - 2022-07-26

Initial release
