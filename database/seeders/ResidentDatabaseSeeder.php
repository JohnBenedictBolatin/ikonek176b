<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Resident;
use App\Models\ResidentOffense;
use Carbon\Carbon;

class ResidentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define role IDs (assuming 1 = Resident, 2-9 = Officials)
        $roleMap = [
            'Resident' => 1,
            'Barangay Captain' => 2,
            'Barangay Kagawad' => 5,
            'SK Chairman' => 6,
        ];

        // Residents data
        $residents = [
            // 1. John Benedict Leal Bolatin
            [
                'first_name' => 'John Benedict',
                'middle_name' => 'Leal',
                'last_name' => 'Bolatin',
                'suffix' => null,
                'birthdate' => '2005-12-04',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Improper Waste Disposal (First Offense)', 'severity' => 'Low', 'date' => '2023-06-15'],
                    ['type' => 'Minor Property Damage (Under ₱5,000)', 'severity' => 'Low', 'date' => '2023-08-20'],
                    ['type' => 'Parking Violation', 'severity' => 'Low', 'date' => '2024-01-10'],
                ],
            ],
            // 2. Justin Rave Albay Crespo
            [
                'first_name' => 'Justin Rave',
                'middle_name' => 'Albay',
                'last_name' => 'Crespo',
                'suffix' => null,
                'birthdate' => '2002-01-02',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Parking Violation', 'severity' => 'Low', 'date' => '2023-05-12'],
                    ['type' => 'Minor Boundary Disputes (Resolved amicably)', 'severity' => 'Low', 'date' => '2023-09-18'],
                ],
            ],
            // 3. Harvey Miguel Bonto Del Rosario
            [
                'first_name' => 'Harvey Miguel',
                'middle_name' => 'Bonto',
                'last_name' => 'Del Rosario',
                'suffix' => null,
                'birthdate' => '2001-05-23',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Improper Waste Disposal (Repeated/Second Offense)', 'severity' => 'Medium', 'date' => '2023-07-22'],
                    ['type' => 'Non-payment of Community Dues (Significant Amount/Repeated)', 'severity' => 'Medium', 'date' => '2023-11-05'],
                    ['type' => 'Disruptive Behavior in Community Events', 'severity' => 'Medium', 'date' => '2024-02-14'],
                ],
            ],
            // 4. Josh Dereck Domingo Jocson
            [
                'first_name' => 'Josh Dereck',
                'middle_name' => 'Domingo',
                'last_name' => 'Jocson',
                'suffix' => null,
                'birthdate' => '2000-08-19',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Fraudulent Document Request', 'severity' => 'High', 'date' => '2022-03-10'],
                    ['type' => 'Threatening Behavior or Harassment', 'severity' => 'High', 'date' => '2023-05-25'],
                    ['type' => 'Violation of Community Rules (Severe/Repeated)', 'severity' => 'High', 'date' => '2023-08-30'],
                    ['type' => 'Public Nuisance (Severe/Repeated)', 'severity' => 'High', 'date' => '2024-01-15'],
                ],
            ],
            // 5. Richard Jamisal Delima
            [
                'first_name' => 'Richard',
                'middle_name' => 'Jamisal',
                'last_name' => 'Delima',
                'suffix' => null,
                'birthdate' => '1988-06-22',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Destruction of Public Property', 'severity' => 'High', 'date' => '2023-04-18'],
                    ['type' => 'Misuse of Barangay Services', 'severity' => 'High', 'date' => '2023-10-12'],
                ],
            ],
            // 6. Pauleen Ann Arlando Manibo
            [
                'first_name' => 'Pauleen Ann',
                'middle_name' => 'Arlando',
                'last_name' => 'Manibo',
                'suffix' => null,
                'birthdate' => '1979-09-20',
                'gender' => 'Female',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Parking Violation', 'severity' => 'Low', 'date' => '2023-07-08'],
                    ['type' => 'Pet-Related Violations (Unleashed pets, excessive barking)', 'severity' => 'Low', 'date' => '2023-12-20'],
                ],
            ],
            // 7. Luis Lampitoc Jr.
            [
                'first_name' => 'Luis',
                'middle_name' => null,
                'last_name' => 'Lampitoc',
                'suffix' => 'Jr.',
                'birthdate' => '1995-02-10',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Noise Disturbance (First Offense)', 'severity' => 'Low', 'date' => '2024-03-05'],
                ],
            ],
            // 8. McDave Calunsag III
            [
                'first_name' => 'McDave',
                'middle_name' => null,
                'last_name' => 'Calunsag',
                'suffix' => 'III',
                'birthdate' => '1992-10-07',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Unauthorized Construction (Minor - without permit but not illegal)', 'severity' => 'Medium', 'date' => '2023-09-25'],
                    ['type' => 'Disruptive Behavior in Community Events', 'severity' => 'Medium', 'date' => '2024-01-08'],
                ],
            ],
            // 9. Justin Rhey Rodriguez
            [
                'first_name' => 'Justin Rhey',
                'middle_name' => null,
                'last_name' => 'Rodriguez',
                'suffix' => null,
                'birthdate' => '1968-04-01',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Illegal Construction (Major/Without Permit)', 'severity' => 'High', 'date' => '2022-11-15'],
                    ['type' => 'Property Damage (Over ₱20,000)', 'severity' => 'High', 'date' => '2023-06-22'],
                ],
            ],
            // 10. Jana Ruth Castillo
            [
                'first_name' => 'Jana Ruth',
                'middle_name' => null,
                'last_name' => 'Castillo',
                'suffix' => null,
                'birthdate' => '2006-01-27',
                'gender' => 'Female',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Noise Disturbance (First Offense)', 'severity' => 'Low', 'date' => '2024-02-18'],
                ],
            ],
            // 11. Shiela Quirante Urola
            [
                'first_name' => 'Shiela',
                'middle_name' => 'Quirante',
                'last_name' => 'Urola',
                'suffix' => null,
                'birthdate' => '1975-03-20',
                'gender' => 'Female',
                'role' => 'Resident',
                'offenses' => [],
            ],
            // 12. Rolly Alorado Santos Sr.
            [
                'first_name' => 'Rolly',
                'middle_name' => 'Alorado',
                'last_name' => 'Santos',
                'suffix' => 'Sr.',
                'birthdate' => '1988-11-02',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [],
            ],
            // 13. Theresa Sarido Penates
            [
                'first_name' => 'Theresa',
                'middle_name' => 'Sarido',
                'last_name' => 'Penates',
                'suffix' => null,
                'birthdate' => '1991-12-29',
                'gender' => 'Female',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Noise Disturbance (First Offense)', 'severity' => 'Low', 'date' => '2023-10-30'],
                ],
            ],
            // 14. Rolly Alorado Santos Jr.
            [
                'first_name' => 'Rolly',
                'middle_name' => 'Alorado',
                'last_name' => 'Santos',
                'suffix' => 'Jr.',
                'birthdate' => '2005-04-16',
                'gender' => 'Male',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Minor Property Damage (Under ₱5,000)', 'severity' => 'Low', 'date' => '2023-12-05'],
                ],
            ],
            // 15. Justine Pascual Dela Cruz
            [
                'first_name' => 'Justine',
                'middle_name' => 'Pascual',
                'last_name' => 'Dela Cruz',
                'suffix' => null,
                'birthdate' => '2002-02-03',
                'gender' => 'Female',
                'role' => 'Resident',
                'offenses' => [
                    ['type' => 'Improper Waste Disposal (Repeated/Second Offense)', 'severity' => 'Medium', 'date' => '2023-08-14'],
                ],
            ],
            // 16. Sammy De Guzman Reyes - Barangay Captain
            [
                'first_name' => 'Sammy',
                'middle_name' => 'De Guzman',
                'last_name' => 'Reyes',
                'suffix' => null,
                'birthdate' => '1970-08-01',
                'gender' => 'Male',
                'role' => 'Barangay Captain',
                'offenses' => [],
            ],
            // 17. Danilo Santos Bacolod - Barangay Kagawad
            [
                'first_name' => 'Danilo',
                'middle_name' => 'Santos',
                'last_name' => 'Bacolod',
                'suffix' => null,
                'birthdate' => '1969-10-19',
                'gender' => 'Male',
                'role' => 'Barangay Kagawad',
                'offenses' => [],
            ],
            // 18. Melany Diane Madrigal Mabagos - Barangay Kagawad
            [
                'first_name' => 'Melany Diane',
                'middle_name' => 'Madrigal',
                'last_name' => 'Mabagos',
                'suffix' => null,
                'birthdate' => '1985-02-19',
                'gender' => 'Female',
                'role' => 'Barangay Kagawad',
                'offenses' => [],
            ],
            // 19. Louilyn Damano Bagacay - Barangay Kagawad
            [
                'first_name' => 'Louilyn',
                'middle_name' => 'Damano',
                'last_name' => 'Bagacay',
                'suffix' => null,
                'birthdate' => '1990-01-12',
                'gender' => 'Female',
                'role' => 'Barangay Kagawad',
                'offenses' => [],
            ],
            // 20. Hero Delos Santos - SK Chairman
            [
                'first_name' => 'Hero',
                'middle_name' => null,
                'last_name' => 'Delos Santos',
                'suffix' => null,
                'birthdate' => '2000-09-02',
                'gender' => 'Male',
                'role' => 'SK Chairman',
                'offenses' => [],
            ],
        ];

        // Create residents and their offenses
        foreach ($residents as $residentData) {
            $resident = Resident::create([
                'first_name' => $residentData['first_name'],
                'middle_name' => $residentData['middle_name'],
                'last_name' => $residentData['last_name'],
                'suffix' => $residentData['suffix'],
                'birthdate' => $residentData['birthdate'],
                'gender' => $residentData['gender'],
                'fk_role_id' => $roleMap[$residentData['role']] ?? 1,
            ]);

            // Create offenses for this resident
            foreach ($residentData['offenses'] as $offenseData) {
                ResidentOffense::create([
                    'fk_resident_id' => $resident->resident_id,
                    'offense_type' => $offenseData['type'],
                    'severity_level' => $offenseData['severity'],
                    'offense_date' => $offenseData['date'],
                    'status' => 'Active',
                ]);
            }
        }

        $this->command->info('Resident database seeded successfully with 20 residents and their offenses.');
    }
}
