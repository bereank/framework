<?php

namespace Leysco100\Gpm\Console;


use Illuminate\Console\Command;
use Leysco100\Shared\Models\Gpm\Models\FormFieldType;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;


class InsertFormFieldTypes extends Command
{
    use TenantAware;
    // command definition: php artisan insert:form-field-types
    protected $signature = 'insert:form-field-types {--tenant=*}';

    protected $description = 'Insert the FormFieldTypes data into the database';

    public function handle()
    {
        $data = [
            [
                "id" => 1,
                "Name" => "Text"
            ],
            [
                "id" => 2,
                "Name" => "Photo"
            ],
            [
                "id" => 3,
                "Name" => "Phone"
            ],
            [
                "id" => 4,
                "Name" => "Dropdown"
            ],
            [
                "id" => 5,
                "Name" => "QRCode"
            ]
        ];

        foreach ($data as $item) {
            FormFieldType::updateOrCreate(['id' => $item['id']], $item);
        }

        $this->info('FormFieldTypes data inserted successfully.');
    }
}
