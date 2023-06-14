<?php

namespace Tests\Services;

use App\Services\CSVReader;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CSVReaderTest extends TestCase
{


    /**
     * @return array[]
     * @created 14-06-2023
     */
    public static function csvProvider(): array
    {
        return [
            ['csv/products-8.csv', 'output/product.json'],
            //Add here other csv data and expected json
        ];
    }

    /**
     * @throws \Exception
     * @dataProvider csvProvider
     */
    public function test_csv_reader_has_same_output($csvPath , $jsonPath)
    {

        $csvReader = new CSVReader($csvPath);

        $json = Storage::get($jsonPath);

        $csvJson = $csvReader->toJson();

        $jsonArray = json_decode($json, true);
        $csvArray = json_decode($csvJson, true);


        self::assertEquals($jsonArray, $csvArray);
    }
}
