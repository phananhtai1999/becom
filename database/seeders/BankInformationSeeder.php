<?php

namespace Database\Seeders;

use App\Models\BankInformation;
use Illuminate\Database\Seeder;

class BankInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bankInformations = [
            [
                'swift_code' => 'PMFAUS66',
                'bank_name' => '1ST PMF BANCORP	',
                'bank_address' => 'LOS ANGELES, CALIFORNIA	',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'ABOCCNBJ021',
                'bank_name' => 'AGRICULTURAL BANK OF CHINA, THE',
                'bank_address' => 'BEIJING',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'ADDCJPJT',
                'bank_name' => 'A AND D CO., LTD.',
                'bank_address' => 'TOSHIMA-KU',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'ADDCJPJT',
                'bank_name' => 'A AND D CO., LTD.',
                'bank_address' => 'TOSHIMA-KU',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'TRSYDEFF',
                'bank_name' => 'FRANKFURT AM MAIN',
                'bank_address' => '360 TREASURY SYSTEMS AG',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'GASKFRPP',
                'bank_name' => '(GROUPE) ASTEK S.A.',
                'bank_address' => 'BOULOGNE BILLANCOURT',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'ADTVBRDF',
                'bank_name' => 'ACLA BANK',
                'bank_address' => 'BRASILIA',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'SGAZRU22',
                'bank_name' => "'BANK SGB' JSC",
                'bank_address' => 'VOLOGDA',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'PBERIT22',
                'bank_name' => "2019 POPOLARE BARI SME S.R.L.",
                'bank_address' => 'CONEGLIANO',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'AIVVCATT',
                'bank_name' => "AGF INVESTMENTS INC.",
                'bank_address' => 'TORONTO',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'AYGBESMM',
                'bank_name' => "A AND G BANCA PRIVADA, S.A.",
                'bank_address' => 'MADRID',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'ABOCKRSE',
                'bank_name' => "AGRICULTURAL BANK OF CHINA SEOUL BRANCH",
                'bank_address' => 'SEOUL',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'AFXBMXMM',
                'bank_name' => "AFORE XXI BANORTE, S.A. DE C.V.",
                'bank_address' => 'CIUDAD DE MEXICO',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'BFRPARBA',
                'bank_name' => "BANCO BBVA ARGENTINA S.A.",
                'bank_address' => 'BUENOS AIRES',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'PHBMMYKL',
                'bank_name' => "AFFIN BANK BERHAD",
                'bank_address' => 'KUALA LUMPUR',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'ABDIQAQA',
                'bank_name' => "ABU DHABI ISLAMIC BANK",
                'bank_address' => 'DOHA',
                'currency' => 'USD'
            ],
            [
                'swift_code' => 'ABOCVNVX',
                'bank_name' => 'AGRICULTURAL BANK OF CHINA LIMITED - HANOI BRANCH',
                'bank_address' => 'HANOI',
                'currency' => 'VND'
            ],
        ];

        foreach ($bankInformations as $bankInformation) {
            BankInformation::firstOrCreate(
                ['swift_code' => $bankInformation['swift_code']], $bankInformation
            );
        }
    }
}
