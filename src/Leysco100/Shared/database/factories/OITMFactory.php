<?php

namespace Leysco\LS100SharedPackage\Database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Leysco\LS100SharedPackage\Models\Domains\InventoryAndProduction\Models\OITM;

class OITMFactory extends Factory
{

    protected $model = OITM::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        return [
            'ItemCode' => $this->faker->randomNumber(5),
            'ItemName' => $this->faker->word(),
        ];

    }
}
