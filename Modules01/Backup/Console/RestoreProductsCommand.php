<?php

namespace Modules\Backup\Console;

use Illuminate\Console\Command;
use Modules\Backup\Entities\Backup;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Modules\Products\Entities\Product as EntitiesProduct;

class RestoreProductsCommand extends Command
{
    protected $signature = 'backup:restore-products {filename}';
    protected $description = 'Restore products table from a backup JSON file';

    public function handle()
    {
        $filename = $this->argument('filename');
        $backup = Backup::where('filename', $filename)->first();

        if (!$backup || !Storage::disk(config('backup.disk'))->exists($backup->path)) {
            $this->error("Backup file {$filename} not found.");
            return;
        }

        try {
            $content = Storage::disk(config('backup.disk'))->get($backup->path);
            if (config('backup.encrypt')) {
                $content = Crypt::decryptString($content);
            }

            $data = json_decode($content, true);

            EntitiesProduct::truncate(); // Optional: clear existing data
            foreach (array_chunk($data, 1000) as $chunk) {
                EntitiesProduct::insert($chunk);
            }

            $this->info("Products restored from {$filename}");
        } catch (\Exception $e) {
            $this->error('Restore failed: ' . $e->getMessage());
        }
    }
}
