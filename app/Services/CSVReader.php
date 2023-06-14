<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SplFileObject;

class CSVReader
{

    protected string $filePath;

    /**
     * @throws \Exception
     */
    public function __construct(string $path)
    {
        if (!Storage::exists($path)) {
            throw new \Exception("File does not exist!");
        }

        $this->filePath = Storage::path($path);

        $content = file_get_contents($this->filePath);
        $encoding = mb_detect_encoding($content, 'UTF-8', true);


        if ($encoding === false) {
            throw new \Exception("UTF-8 encoding required on csv file");
        }
    }

    /**
     * @return string
     * @created 12-06-2023
     */
    public function toJson(): string
    {
        $file = fopen($this->filePath, 'r');

        // Initialize the product array
        $products = [];

        // Read each line of the CSV file
        while (($line = fgetcsv($file)) !== false) {


            $isProduct = $line[0] === 'Product' && $line[1] === 'Product';
            $isVariantAttribute = $line[1] === 'Variant' && $line[2] === 'Attribute';
            $isVariantProduct = $line[1] === 'Variant' && $line[2] === 'Product';
            $isImage = $line[1] === 'Image';


            if ($isProduct) {

                // Create a new product array
                $product = [
                    'id' => $line[3],
                    'mpn' => $line[6],
                    'sku' => $line[7],
                    'name' => $line[4],
                    'slug' => $line[5],
                    'price' => $line[11],
                    'stock' => $line[14],
                    'active' => true,
                    'images' => [],
                    'barcode' => $line[8],
                    'on_sale' => false,
                    'children' => [],
                    'meta_title' => $line[16],
                    'product_category_id' => $line[10]
                ];

                // Add the product to the products array
                $products[] = $product;

            }

            if ($isImage) {
                // Get the last added product
                $product = end($products);

                // Add the image to the last product's images array
                $image = [
                    'id' => '',
                    'alt' => $line[4],
                    'source' => $line[18]
                ];

                // Add the image to the product's images array
                $product['images'][] = $image;

                // Update the product in the products array
                $products[key($products)] = $product;
            }

            if ($isVariantProduct) {
                // Get the last added product
                $product = end($products);

                // Create a new variant array
                $variant = [
                    'sku' => $line[7],
                    'slug' => $line[5],
                    'price' => $line[11],
                    'stock' => $line[14],
                    'on_sale' => false,
                    'attributes' => [],
                    'sale_price' => '',
                    'track_stock' => false
                ];

                // Add the variant to the last product's children array
                $product['children'][] = $variant;

                // Update the product in the products array
                $products[key($products)] = $product;
            }

            if ($isVariantAttribute) {
                // Get the last added product
                $product = end($products);
                $lastChild = end($product['children']);

                if ($lastChild === false) {
                    $lastChild = [];
                    $key = 0;
                } else {
                    $key = key($product['children']);
                }

                $attributes = $lastChild["attributes"] ?? [];

                // Assign the attribute value based on the attribute key
                if ($line[4] === 'Size') {
                    $attributes = [
                            'Size' => $line[19]
                        ] + $attributes;
                } elseif ($line[4] === 'Colour') {
                    $attributes['Colour'] = $line[19];
                }

                $lastChild["attributes"] = $attributes;
                $product['children'][$key] = $lastChild;

                $products[key($products)] = $product;
            }
        }

        // Close the CSV file
        fclose($file);


        return json_encode($products, JSON_PRETTY_PRINT);
    }
}
