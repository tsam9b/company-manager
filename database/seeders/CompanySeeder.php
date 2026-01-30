<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::query()->delete();

        $companies = [
            ['name' => 'Acme Corp', 'email' => 'info@acme.test', 'logo' => 'logos/acme.png', 'website' => 'https://acme.test'],
            ['name' => 'Globex LLC', 'email' => 'contact@globex.test', 'logo' => 'logos/globex.png', 'website' => 'https://globex.test'],
            ['name' => 'Initech', 'email' => 'hello@initech.test', 'logo' => 'logos/initech.png', 'website' => 'https://initech.test'],
            ['name' => 'Umbrella Group', 'email' => 'contact@umbrella.test', 'logo' => 'logos/umbrella.png', 'website' => 'https://umbrella.test'],
            ['name' => 'Stark Industries', 'email' => 'info@stark.test', 'logo' => 'logos/stark.png', 'website' => 'https://stark.test'],
            ['name' => 'Wayne Enterprises', 'email' => 'contact@wayne.test', 'logo' => 'logos/wayne.png', 'website' => 'https://wayne.test'],
            ['name' => 'Wonka Factory', 'email' => 'hello@wonka.test', 'logo' => 'logos/wonka.png', 'website' => 'https://wonka.test'],
            ['name' => 'Soylent Co', 'email' => 'info@soylent.test', 'logo' => 'logos/soylent.png', 'website' => 'https://soylent.test'],
            ['name' => 'Cyberdyne Systems', 'email' => 'contact@cyberdyne.test', 'logo' => 'logos/cyberdyne.png', 'website' => 'https://cyberdyne.test'],
            ['name' => 'Tyrell Corp', 'email' => 'hello@tyrell.test', 'logo' => 'logos/tyrell.png', 'website' => 'https://tyrell.test'],
            ['name' => 'Gringotts Bank', 'email' => 'info@gringotts.test', 'logo' => 'logos/gringotts.png', 'website' => 'https://gringotts.test'],
            ['name' => 'Oscorp', 'email' => 'contact@oscorp.test', 'logo' => 'logos/oscorp.png', 'website' => 'https://oscorp.test'],
            ['name' => 'Vehement Capital', 'email' => 'hello@vehement.test', 'logo' => 'logos/vehement.png', 'website' => 'https://vehement.test'],
            ['name' => 'Daily Planet', 'email' => 'info@dailyplanet.test', 'logo' => 'logos/dailyplanet.png', 'website' => 'https://dailyplanet.test'],
            ['name' => 'Hooli', 'email' => 'contact@hooli.test', 'logo' => 'logos/hooli.png', 'website' => 'https://hooli.test'],
            ['name' => 'Vandelay Industries', 'email' => 'hello@vandelay.test', 'logo' => 'logos/vandelay.png', 'website' => 'https://vandelay.test'],
            ['name' => 'Pied Piper', 'email' => 'info@piedpiper.test', 'logo' => 'logos/piedpiper.png', 'website' => 'https://piedpiper.test'],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
