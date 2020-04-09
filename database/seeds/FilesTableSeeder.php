<?php

use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FilesTableSeeder extends Seeder
{
    /**
     * @var $db DatabaseManager
     */
    private DatabaseManager $db;

    private string $basePath;
    private string $folderPath;
    private string $thumbnailPath;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clean the DB.
        $this->db = app()->make('db');
        $this->db->table('files')->truncate();

        // Set base variables
        $this->basePath = storage_path('uploads');
        $this->folderPath = $this->basePath.'/se/ed/ed/';
        $this->thumbnailPath = $this->basePath.'/thumbnail/se/ed/ed/';

        // Clean the folder dirs
        exec('rm -rf '.$this->basePath.'/se/');
        exec('rm -rf '.$this->basePath.'/thumbnail/se/');

        if(!is_dir($this->folderPath)) {
            mkdir($this->folderPath, 0775, true);
            mkdir($this->thumbnailPath, 0775, true);
        }

        for($i=0; $i<10; $i++) {
            // Upload some files
            $this->uploadAFile();
        }

    }

    private function makeGuid()
    {
        $guid = Str::orderedUuid()->toString();
        $arr = explode('-', $guid);
        $arr[0] = 'seeded';
        $guid = implode('-', $arr);

        return $guid;
    }

    private function uploadAFile()
    {
        $guid = $this->makeGuid();
        $fileName = $guid.'.png';
        $storagePath = $this->folderPath.$fileName;
        $thumbPath = $this->thumbnailPath.$fileName;

        $file = UploadedFile::fake()->image($fileName)->size(200);

        file_put_contents($storagePath, $file);
        file_put_contents($thumbPath, $file);

        $hash = md5_file($storagePath);

        $this->db->table('files')->insert([
            'folder_id' => 1,
            'guid' => $guid,
            'name' => 'SuperAdmin file upload',
            'description' => 'The first file uploaded by the super user',
            'extension' => 'png',
            'mimetype' => 'image/png',
            'size' => '200',
            'storage_method' => 'DISK',
            'storage_region' => 'uploads',
            'storage_path' => $storagePath,
            'file_hash' => $hash,
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
