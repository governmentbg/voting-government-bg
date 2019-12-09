<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Libraries\XMLParser;
use Illuminate\Support\Facades\Storage;

class exportOrgsToJSON extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exportOrg:json {path : Path to xml files.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses XML files to JSON file ready fro import';

    protected $parser;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->parser =  new XMLParser();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try{
            $path = $this->argument('path');

            $files = $this->searchFilesDirectory($path);
            $allData = [];
            foreach($files as $key => $filePath) {
                if(!$this->parser->loadFile($filePath)){
                    $this->info('Could not parse file: ' . $filePath);
                    continue;
                }
                $this->info($filePath);

                $data = $this->parser->getParsedData();
                
                $bar = $this->output->createProgressBar(count($data));
                $bar->start();
                $allData = array_merge($allData, $data);
                $bar->finish();
                $this->info(''); //add new row
                $this->info(''); //add new row
            }

            Storage::disk('local')->put('predefined_list.json', json_encode($allData));
        }
        catch(\Exception $e){
            $this->error($e->getMessage());
        }
    }

    private function searchFilesDirectory($path)
    {
        if(File::exists($path)) {
            return [$path];
        }
        
        $fileSystemIterator = new \FilesystemIterator($path);

        $files = [];
        foreach ($fileSystemIterator as $fileInfo){
            $type = $fileInfo->getType(); //directory or file
            if($type == 'file' && $fileInfo->getExtension() == 'xml'){
                $files[] = $fileInfo->getPathname();
            }
            else if($type == 'dir'){
                $files = array_merge($files, $this->searchFilesDirectory($fileInfo->getPathname()));
            }
        }

        return $files;
    }
}
