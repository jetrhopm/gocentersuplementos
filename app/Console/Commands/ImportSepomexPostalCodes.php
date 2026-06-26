<?php

namespace App\Console\Commands;

use App\Models\PostalCode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSepomexPostalCodes extends Command
{
    protected $signature = 'postal-codes:import-sepomex {file : Ruta del archivo TXT de SEPOMEX} {--truncate : Vaciar la tabla antes de importar}';

    protected $description = 'Importa codigos postales de Mexico desde el TXT pipe-separated de SEPOMEX.';

    public function handle(): int
    {
        $path = (string) $this->argument('file');

        if (! is_file($path) || ! is_readable($path)) {
            $this->error('No se pudo leer el archivo: '.$path);

            return self::FAILURE;
        }

        if ($this->option('truncate')) {
            DB::table('postal_codes')->truncate();
        }

        $handle = fopen($path, 'rb');
        fgetcsv($handle, 0, '|');
        $count = 0;
        $batch = [];
        $now = now();

        while (($row = fgetcsv($handle, 0, '|')) !== false) {
            if (count($row) < 15 || ! preg_match('/^\d{5}$/', $row[0] ?? '')) {
                continue;
            }

            $municipality = $this->clean($row[3]);
            $city = $this->clean($row[5]) ?: $municipality;

            $batch[] = [
                'postal_code' => $row[0],
                'settlement' => $this->clean($row[1]),
                'settlement_type' => $this->clean($row[2]),
                'municipality' => $municipality,
                'state' => $this->clean($row[4]),
                'city' => $city,
                'zone' => $this->clean($row[13] ?? null),
                'state_code' => $this->clean($row[7] ?? null),
                'municipality_code' => $this->clean($row[11] ?? null),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $count++;

            if (count($batch) >= 1000) {
                $this->upsert($batch);
                $batch = [];
                $this->output->write('.');
            }
        }

        if ($batch) {
            $this->upsert($batch);
        }

        fclose($handle);

        $this->newLine();
        $this->info("Codigos postales importados/actualizados: {$count}");

        return self::SUCCESS;
    }

    private function clean(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_convert_encoding($value, 'UTF-8', 'UTF-8, ISO-8859-1');
    }

    private function upsert(array $batch): void
    {
        PostalCode::query()->upsert(
            $batch,
            ['postal_code', 'settlement', 'municipality', 'state'],
            ['settlement_type', 'city', 'zone', 'state_code', 'municipality_code', 'updated_at']
        );
    }
}
