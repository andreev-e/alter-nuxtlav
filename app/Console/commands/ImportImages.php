<?php

namespace App\Console\Commands;

use \App\Models\Poi;
use \App\Models\Image;
use Illuminate\Console\Command;

class ImportImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $pois =  Poi::select('id')->get();
        foreach ($pois as $poi) {
            echo PHP_EOL;
            echo $poi->id . PHP_EOL;
            for ($i = 1; $i < 4; $i++) {
                $path = $poi->id . '/' . $i . '.jpg';
                $url = 'https://altertravel.ru/images/' . $path;
                $handle = curl_init($url);
                // curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($handle, CURLOPT_NOBODY, TRUE);
                $response = curl_exec($handle);
                $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
                echo $httpCode . ' - ';
                if ($httpCode === 200) {
                    Image::updateOrCreate([
                        'path' => $path,
                    ],[
                        'parent' => $poi->id,
                        'author' => null,
                        'is_main' => $i === 1,
                        'description' => $poi->{'photo_text' . $i},
                    ]);
                }
                curl_close($handle);
            }
        }
    }
}
