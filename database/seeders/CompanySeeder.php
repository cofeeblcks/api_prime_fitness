<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\LinkType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Copy all content of dir resources/images/logo to public/storage/companies/logos
        $logoDir = resource_path('images/logos');
        $logoFiles = File::allFiles($logoDir);
        foreach ($logoFiles as $logoFile) {
            $logoName = $logoFile->getFilename();
            $logoPath = $logoFile->getRealPath();
            $logoContent = File::get($logoPath);
            Storage::disk(config('filesystems.default'))->put('companies/logos/' . $logoName, $logoContent);
        }

        $company = Company::create([
            'name' => 'Prime Fitness',
            'slogan' => 'Tu gimnasio en línea',
            'logo' => 'companies/logos/imagologo.svg',
            'address' => 'Calle 16 29-22, Molinos bajos, Floridablanca, Santander',
            'description' => 'Somos un gimnasio en línea que ofrece una amplia gama de servicios para mejorar tu salud y bienestar.',
        ]);

        $company->links()->create([
            'username' => 'primefitness',
            'link' => 'https://www.facebook.com/primefitness',
            'link_type_id' => LinkType::where('name', 'Facebook')->first()->id,
        ]);

        $company->emails()->create([
            'email' => 'info@primefitness.com',
        ]);

        $company->phones()->create([
            'phone' => '3178546923',
        ]);

        $company->services()->create([
            'description' => 'Ofrecemos una amplia gama de servicios para mejorar tu salud y bienestar.',
        ]);

        $company->services()->create([
            'description' => 'Planes de entrenamiento personalizados para cada cliente.',
        ]);

        $company->services()->create([
            'description' => 'Asesoría nutricional para mejorar tu salud y bienestar.',
        ]);

        $company->coordinates()->create([
            'latitude' => 7.0622088,
            'longitude' => -73.0973668,
        ]);
    }
}
