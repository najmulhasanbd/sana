<?php

namespace Database\Seeders;

use App\Models\District;
use App\Models\Division;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            [
                'name' => 'Dhaka',
                'districts' => [
                    [
                        'name' => 'Dhaka',
                        'zones' => [
                            ['name' => 'Dhanmondi'],
                            ['name' => 'Mirpur'],
                            ['name' => 'Uttara'],
                            ['name' => 'Gulshan'],
                            ['name' => 'Savar'],
                            ['name' => 'Keraniganj'],
                            ['name' => 'Dhamrai'],
                        ],
                    ],
                    [
                        'name' => 'Gazipur',
                        'zones' => [
                            ['name' => 'Gazipur Sadar'],
                            ['name' => 'Kaliakair'],
                            ['name' => 'Kapasia'],
                            ['name' => 'Sreepur'],
                            ['name' => 'Kaliganj'],
                            ['name' => 'Tongi'],
                        ],
                    ],
                    ['name' => 'Narayanganj', 'zones' => [
                        ['name' => 'Narayanganj Sadar'],
                        ['name' => 'Bandar'],
                        ['name' => 'Rupganj'],
                        ['name' => 'Sonargaon'],
                        ['name' => 'Araihazar'],
                    ]],
                    ['name' => 'Narsingdi', 'zones' => [
                        ['name' => 'Narsingdi Sadar'],
                        ['name' => 'Palash'],
                        ['name' => 'Belabo'],
                        ['name' => 'Monohardi'],
                        ['name' => 'Shibpur'],
                        ['name' => 'Raipura'],
                    ]],
                    ['name' => 'Tangail', 'zones' => [
                        ['name' => 'Tangail Sadar'],
                        ['name' => 'Mirzapur'],
                        ['name' => 'Madhupur'],
                        ['name' => 'Ghatail'],
                        ['name' => 'Gopalpur'],
                        ['name' => 'Sakhipur'],
                    ]],
                    ['name' => 'Kishoreganj', 'zones' => [
                        ['name' => 'Kishoreganj Sadar'],
                        ['name' => 'Bhairab'],
                        ['name' => 'Bajitpur'],
                        ['name' => 'Katiadi'],
                    ]],
                    ['name' => 'Manikganj', 'zones' => [
                        ['name' => 'Manikganj Sadar'],
                        ['name' => 'Singair'],
                    ]],
                    ['name' => 'Munshiganj', 'zones' => [
                        ['name' => 'Munshiganj Sadar'],
                        ['name' => 'Srinagar'],
                    ]],
                    ['name' => 'Faridpur', 'zones' => [
                        ['name' => 'Faridpur Sadar'],
                        ['name' => 'Boalmari'],
                    ]],
                    ['name' => 'Gopalganj', 'zones' => [
                        ['name' => 'Gopalganj Sadar'],
                        ['name' => 'Kotalipara'],
                    ]],
                    ['name' => 'Madaripur', 'zones' => [
                        ['name' => 'Madaripur Sadar'],
                        ['name' => 'Shibchar'],
                    ]],
                    ['name' => 'Rajbari', 'zones' => [
                        ['name' => 'Rajbari Sadar'],
                        ['name' => 'Pangsha'],
                    ]],
                    ['name' => 'Shariatpur', 'zones' => [
                        ['name' => 'Shariatpur Sadar'],
                        ['name' => 'Naria'],
                    ]],
                ],
            ],
            [
                'name' => 'Mymensingh',
                'districts' => [
                    [
                        'name' => 'Mymensingh',
                        'zones' => [
                            ['name' => 'Mymensingh Sadar'],
                            ['name' => 'Trishal'],
                            ['name' => 'Bhaluka'],
                            ['name' => 'Muktagacha'],
                            ['name' => 'Phulpur'],
                            ['name' => 'Haluaghat'],
                            ['name' => 'Gafargaon'],
                            ['name' => 'Ishwarganj'],
                            ['name' => 'Nandail'],
                            ['name' => 'Gauripur'],
                            ['name' => 'Tarakanda'],
                            ['name' => 'Dhobaura'],
                        ],
                    ],
                    [
                        'name' => 'Jamalpur',
                        'zones' => [
                            ['name' => 'Jamalpur Sadar'],
                            ['name' => 'Melandaha'],
                            ['name' => 'Islampur'],
                            ['name' => 'Dewanganj'],
                            ['name' => 'Sarishabari'],
                            ['name' => 'Madarganj'],
                            ['name' => 'Bakshiganj'],
                        ],
                    ],
                    [
                        'name' => 'Netrokona',
                        'zones' => [
                            ['name' => 'Netrokona Sadar'],
                            ['name' => 'Kendua'],
                            ['name' => 'Mohanganj'],
                            ['name' => 'Durgapur'],
                            ['name' => 'Kalmakanda'],
                            ['name' => 'Barhatta'],
                        ],
                    ],
                    [
                        'name' => 'Sherpur',
                        'zones' => [
                            ['name' => 'Sherpur Sadar'],
                            ['name' => 'Nakla'],
                            ['name' => 'Nalitabari'],
                            ['name' => 'Jhenaigati'],
                            ['name' => 'Sreebardi'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'Chattogram',
                'districts' => [
                    ['name' => 'Chattogram', 'zones' => [
                        ['name' => 'Patiya'],
                        ['name' => 'Hathazari'],
                        ['name' => 'Sitakunda'],
                        ['name' => 'Mirsharai'],
                        ['name' => 'Raozan'],
                        ['name' => 'Anwara'],
                    ]],
                    ['name' => 'Cox\'s Bazar', 'zones' => [
                        ['name' => 'Cox\'s Bazar Sadar'],
                        ['name' => 'Chakaria'],
                        ['name' => 'Teknaf'],
                    ]],
                    ['name' => 'Cumilla', 'zones' => [
                        ['name' => 'Cumilla Adarsha Sadar'],
                        ['name' => 'Daudkandi'],
                        ['name' => 'Chandina'],
                        ['name' => 'Debidwar'],
                        ['name' => 'Muradnagar'],
                    ]],
                    ['name' => 'Brahmanbaria', 'zones' => [
                        ['name' => 'Brahmanbaria Sadar'],
                        ['name' => 'Ashuganj'],
                        ['name' => 'Kasba'],
                    ]],
                    ['name' => 'Chandpur', 'zones' => [
                        ['name' => 'Chandpur Sadar'],
                        ['name' => 'Hajiganj'],
                    ]],
                    ['name' => 'Noakhali', 'zones' => [
                        ['name' => 'Noakhali Sadar'],
                        ['name' => 'Begumganj'],
                    ]],
                    ['name' => 'Feni', 'zones' => [
                        ['name' => 'Feni Sadar'],
                        ['name' => 'Daganbhuiyan'],
                    ]],
                    ['name' => 'Lakshmipur', 'zones' => [
                        ['name' => 'Lakshmipur Sadar'],
                        ['name' => 'Raipur'],
                    ]],
                    ['name' => 'Khagrachhari', 'zones' => [
                        ['name' => 'Khagrachhari Sadar'],
                    ]],
                    ['name' => 'Rangamati', 'zones' => [
                        ['name' => 'Rangamati Sadar'],
                    ]],
                    ['name' => 'Bandarban', 'zones' => [
                        ['name' => 'Bandarban Sadar'],
                    ]],
                ],
            ],
            [
                'name' => 'Rajshahi',
                'districts' => [
                    ['name' => 'Rajshahi', 'zones' => [
                        ['name' => 'Rajshahi Sadar'],
                        ['name' => 'Bagmara'],
                        ['name' => 'Paba'],
                    ]],
                    ['name' => 'Bogura', 'zones' => [
                        ['name' => 'Bogura Sadar'],
                        ['name' => 'Sherpur'],
                        ['name' => 'Shibganj'],
                    ]],
                    ['name' => 'Pabna', 'zones' => [
                        ['name' => 'Pabna Sadar'],
                        ['name' => 'Ishwardi'],
                    ]],
                    ['name' => 'Sirajganj', 'zones' => [
                        ['name' => 'Sirajganj Sadar'],
                        ['name' => 'Shahjadpur'],
                        ['name' => 'Ullapara'],
                    ]],
                    ['name' => 'Naogaon', 'zones' => [
                        ['name' => 'Naogaon Sadar'],
                        ['name' => 'Patnitala'],
                    ]],
                    ['name' => 'Natore', 'zones' => [
                        ['name' => 'Natore Sadar'],
                        ['name' => 'Singra'],
                    ]],
                    ['name' => 'Chapainawabganj', 'zones' => [
                        ['name' => 'Chapainawabganj Sadar'],
                        ['name' => 'Shibganj'],
                    ]],
                    ['name' => 'Joypurhat', 'zones' => [
                        ['name' => 'Joypurhat Sadar'],
                        ['name' => 'Panchbibi'],
                    ]],
                ],
            ],
            [
                'name' => 'Khulna',
                'districts' => [
                    ['name' => 'Khulna', 'zones' => [
                        ['name' => 'Khulna Sadar'],
                        ['name' => 'Dumuria'],
                        ['name' => 'Paikgachha'],
                    ]],
                    ['name' => 'Jashore', 'zones' => [
                        ['name' => 'Jashore Sadar'],
                        ['name' => 'Jhikargachha'],
                        ['name' => 'Keshabpur'],
                    ]],
                    ['name' => 'Satkhira', 'zones' => [
                        ['name' => 'Satkhira Sadar'],
                        ['name' => 'Kaliganj'],
                    ]],
                    ['name' => 'Kushtia', 'zones' => [
                        ['name' => 'Kushtia Sadar'],
                        ['name' => 'Kumarkhali'],
                    ]],
                    ['name' => 'Bagerhat', 'zones' => [
                        ['name' => 'Bagerhat Sadar'],
                        ['name' => 'Mongla'],
                    ]],
                    ['name' => 'Jhenaidah', 'zones' => [
                        ['name' => 'Jhenaidah Sadar'],
                        ['name' => 'Kaliganj'],
                    ]],
                    ['name' => 'Chuadanga', 'zones' => [
                        ['name' => 'Chuadanga Sadar'],
                        ['name' => 'Damurhuda'],
                    ]],
                    ['name' => 'Magura', 'zones' => [
                        ['name' => 'Magura Sadar'],
                        ['name' => 'Sreepur'],
                    ]],
                    ['name' => 'Meherpur', 'zones' => [
                        ['name' => 'Meherpur Sadar'],
                        ['name' => 'Gangni'],
                    ]],
                    ['name' => 'Narail', 'zones' => [
                        ['name' => 'Narail Sadar'],
                        ['name' => 'Lohagara'],
                    ]],
                ],
            ],
            [
                'name' => 'Barishal',
                'districts' => [
                    ['name' => 'Barishal', 'zones' => [
                        ['name' => 'Barishal Sadar'],
                        ['name' => 'Bakerganj'],
                        ['name' => 'Gournadi'],
                    ]],
                    ['name' => 'Barguna', 'zones' => [
                        ['name' => 'Barguna Sadar'],
                        ['name' => 'Amtali'],
                    ]],
                    ['name' => 'Bhola', 'zones' => [
                        ['name' => 'Bhola Sadar'],
                        ['name' => 'Char Fasson'],
                    ]],
                    ['name' => 'Jhalokathi', 'zones' => [
                        ['name' => 'Jhalokathi Sadar'],
                        ['name' => 'Rajapur'],
                    ]],
                    ['name' => 'Patuakhali', 'zones' => [
                        ['name' => 'Patuakhali Sadar'],
                        ['name' => 'Galachipa'],
                        ['name' => 'Kalapara'],
                    ]],
                    ['name' => 'Pirojpur', 'zones' => [
                        ['name' => 'Pirojpur Sadar'],
                        ['name' => 'Mathbaria'],
                    ]],
                ],
            ],
            [
                'name' => 'Sylhet',
                'districts' => [
                    ['name' => 'Sylhet', 'zones' => [
                        ['name' => 'Sylhet Sadar'],
                        ['name' => 'Golapganj'],
                        ['name' => 'Beanibazar'],
                    ]],
                    ['name' => 'Moulvibazar', 'zones' => [
                        ['name' => 'Moulvibazar Sadar'],
                        ['name' => 'Sreemangal'],
                    ]],
                    ['name' => 'Habiganj', 'zones' => [
                        ['name' => 'Habiganj Sadar'],
                        ['name' => 'Nabiganj'],
                        ['name' => 'Madhabpur'],
                    ]],
                    ['name' => 'Sunamganj', 'zones' => [
                        ['name' => 'Sunamganj Sadar'],
                        ['name' => 'Chhatak'],
                    ]],
                ],
            ],
            [
                'name' => 'Rangpur',
                'districts' => [
                    ['name' => 'Rangpur', 'zones' => [
                        ['name' => 'Rangpur Sadar'],
                        ['name' => 'Badarganj'],
                        ['name' => 'Pirganj'],
                    ]],
                    ['name' => 'Dinajpur', 'zones' => [
                        ['name' => 'Dinajpur Sadar'],
                        ['name' => 'Birganj'],
                        ['name' => 'Parbatipur'],
                    ]],
                    ['name' => 'Gaibandha', 'zones' => [
                        ['name' => 'Gaibandha Sadar'],
                        ['name' => 'Gobindaganj'],
                    ]],
                    ['name' => 'Kurigram', 'zones' => [
                        ['name' => 'Kurigram Sadar'],
                        ['name' => 'Nageshwari'],
                    ]],
                    ['name' => 'Lalmonirhat', 'zones' => [
                        ['name' => 'Lalmonirhat Sadar'],
                        ['name' => 'Hatibandha'],
                    ]],
                    ['name' => 'Nilphamari', 'zones' => [
                        ['name' => 'Nilphamari Sadar'],
                        ['name' => 'Saidpur'],
                    ]],
                    ['name' => 'Panchagarh', 'zones' => [
                        ['name' => 'Panchagarh Sadar'],
                        ['name' => 'Tetulia'],
                    ]],
                    ['name' => 'Thakurgaon', 'zones' => [
                        ['name' => 'Thakurgaon Sadar'],
                        ['name' => 'Pirganj'],
                    ]],
                ],
            ],
        ];

        foreach ($locations as $divData) {
            $division = Division::firstOrCreate(
                ['name' => $divData['name']],
                ['status' => true]
            );

            foreach ($divData['districts'] as $distData) {
                $district = District::firstOrCreate(
                    [
                        'division_id' => $division->id,
                        'name' => $distData['name'],
                    ],
                    [
                        'status' => true,
                    ]
                );

                if (!empty($distData['zones'])) {
                    foreach ($distData['zones'] as $zoneData) {
                        Zone::firstOrCreate(
                            [
                                'district_id' => $district->id,
                                'name' => $zoneData['name'],
                            ],
                            [
                                'status' => true,
                            ]
                        );
                    }
                }
            }
        }
    }
}
