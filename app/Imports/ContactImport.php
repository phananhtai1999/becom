<?php

namespace App\Imports;

use App\Models\Contact;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ContactImport implements ToModel, WithValidation, WithHeadingRow, SkipsOnFailure
{
    use Importable, SkipsFailures;

    /**
     * @param array $row
     * @return Contact
     */
    public function model(array $row)
    {
        $rowDob = date_format(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dob']), 'Y-m-d');

        return new Contact([
            'email' => $row['email'],
            'first_name' => $row['first_name'],
            'last_name' => $row['last_name'],
            'middle_name' => $row['middle_name'],
            'phone' => $row['phone'],
            'sex' => $row['sex'],
            'dob' => $rowDob == '1970-01-01' ? null : $rowDob,
            'city' => $row['city'],
            'country' => $row['country'],
            'user_uuid' => auth()->user()->getKey()
        ]);
    }

    /**
     * @return string[][]
     */
    public function rules(): array
    {
        return [
            'email' => ['required'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'middle_name' => ['nullable'],
            'phone' => ['nullable'],
            'dob' => ['nullable'],
            'sex' => ['nullable'],
            'city' => ['nullable'],
            'country' => ['nullable'],
        ];
    }
}
