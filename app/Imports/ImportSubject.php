<?php

namespace App\Imports;

use App\Models\Subject;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ImportSubject implements ToModel,WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public $counter = 0;
    public function __construct()
    {
        $counter = 0;
    }

    public function model(array $row)
    {
        $subjecttName = $row['name'];

        // Check if the subjectt already exists in the database
        $existingSubject = Subject::where('name', 'LIKE',$subjecttName)->first();

        if (!$existingSubject) {
            $this->counter++;

            // Subject doesn't exist, so create a new one
            return new Subject([
                'name' => $subjecttName,
            ]);
        }


        return null; // Returning null skips the insertion for existing departments
    }
}
