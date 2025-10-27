<?php

namespace Database\Seeders;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        // List of currency names
        $currencies = [
            'AUD' => 'A$',   // Australian Dollar
            'BRL' => 'R$',   // Brazilian Real
            'GBP' => '£',    // British Pound
            'CAD' => 'C$',   // Canadian Dollar
            'AED' => 'د.إ',  // UAE Dirham
            'EUR' => '€',    // Euro
            'INR' => '₹',    // Indian Rupee
            'IDR' => 'Rp',   // Indonesian Rupiah
            'ILS' => '₪',    // Israeli Shekel
            'JPY' => '¥',    // Japanese Yen
            'KWD' => 'KD',   // Kuwaiti Dinar
            'MYR' => 'RM',   // Malaysian Ringgit
            'MXN' => 'Mex$', // Mexican Peso
            'SGD' => 'S$',   // Singapore Dollar
            'KRW' => '₩',    // South Korean Won
            'USD' => '$',    // US Dollar
            'VND' => '₫',    // Vietnamese Dong
        ];

        // Create or update each currency
        foreach ($currencies as $name => $symbol) {
            Currency::updateOrCreate(
                ['name' => $name],
                [
                    'name' => $name,
                    'symbol' => $symbol
                ]
            );
        }

        $path2 = database_path('seeders/sql/flaggroups.sql');
        $sql2 = File::get($path2);
        DB::unprepared($sql2);
        $path1 = database_path('seeders/sql/systemflags.sql');
        $sql1 = File::get($path1);
        DB::unprepared($sql1);
        $path3 = database_path('seeders/sql/languages.sql');
        $sql3 = File::get($path3);
        DB::unprepared($sql3);
        $path4 = database_path('seeders/sql/pages.sql');
        $sql4 = File::get($path4);
        DB::unprepared($sql4);
        $path5 = database_path('seeders/sql/email_templates.sql');
        $sql5 = File::get($path5);
        DB::unprepared($sql5);
    }
}
