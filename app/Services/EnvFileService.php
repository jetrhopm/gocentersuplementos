<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;

class EnvFileService
{
    public function values(array $keys): array
    {
        $contents = $this->contents();

        return collect($keys)
            ->mapWithKeys(fn (string $key) => [$key => $this->readValue($contents, $key)])
            ->all();
    }

    public function update(array $values): void
    {
        $path = base_path('.env');
        $contents = $this->contents();

        foreach ($values as $key => $value) {
            $line = $key.'='.$this->formatValue($value);

            if (preg_match('/^'.preg_quote($key, '/').'=.*$/m', $contents)) {
                $contents = preg_replace('/^'.preg_quote($key, '/').'=.*$/m', $line, $contents);
            } else {
                $contents = rtrim($contents).PHP_EOL.$line.PHP_EOL;
            }
        }

        file_put_contents($path, $contents);

        Artisan::call('config:clear');
        Artisan::call('route:clear');
    }

    public function masked(?string $value): string
    {
        if ($value === null || $value === '' || strtolower($value) === 'null') {
            return 'Sin configurar';
        }

        $length = strlen($value);

        if ($length <= 8) {
            return str_repeat('*', $length);
        }

        return substr($value, 0, 4).str_repeat('*', min(16, $length - 8)).substr($value, -4);
    }

    private function contents(): string
    {
        return file_exists(base_path('.env')) ? file_get_contents(base_path('.env')) : '';
    }

    private function readValue(string $contents, string $key): ?string
    {
        if (! preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $contents, $matches)) {
            return null;
        }

        $value = trim($matches[1]);

        if ((str_starts_with($value, '"') && str_ends_with($value, '"'))
            || (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
            $value = substr($value, 1, -1);
        }

        return $value;
    }

    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null || $value === '') {
            return '';
        }

        $value = (string) $value;

        if (preg_match('/^[A-Za-z0-9_\-\.\/:@${}]+$/', $value)) {
            return $value;
        }

        return '"'.str_replace(['\\', '"'], ['\\\\', '\"'], $value).'"';
    }
}
