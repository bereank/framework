<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class FormFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
        $data = [
            [
                "client_id" => 1,
                "key" => "Name",
                "indexno" => 2,
                "title" => "Enter Customer Name",
                "type" => "Text",
                "mandatory" => true,
                "values" => null,
            ],
            [
                "client_id" => 1,
                "key" => "FormOfIdentity",
                "indexno" => 1,
                "title" => "Enter Form Of Identity",
                "type" => "Dropdown",
                "mandatory" => true,
                "values" => [
                    [
                        "id" => 1,
                        "name" => "National Id",
                    ],
                    [
                        "id" => 2,
                        "name" => "Driving Licence",
                    ],
                    [
                        "id" => 3,
                        "name" => "Passport",
                    ],
                ],
            ],
            [
                "client_id" => 1,
                "key" => "IdentityNo",
                "indexno" => 3,
                "title" => "Enter Customer Identity No",
                "type" => "Text",
                "mandatory" => true,
                "values" => null,
            ],
            [
                "client_id" => 1,
                "key" => "DeliveryPhoto",
                "indexno" => 6,
                "title" => "Take a Picture of Delivery",
                "type" => "Photo",
                "mandatory" => true,
                "values" => null,
            ],
            [
                "client_id" => 1,
                "key" => "InvoicePhoto",
                "indexno" => 6,
                "title" => "Take a Picture of Invoice",
                "type" => "Photo",
                "mandatory" => true,
                "values" => null,
            ],
            [
                "client_id" => 1,
                "key" => "QRCode",
                "indexno" => 8,
                "title" => "Scan the QR Code/Scan na Kutuma",
                "type" => "QRCode",
                "mandatory" => true,
                "values" => null,
            ],
        ];

        DB::table('form_fields')->insert($data);
    }
}
