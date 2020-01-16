<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\BulstatRegister;

class PopulateBulstatList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:bulstatList {path? : Path to JSON file from TR Register. }';

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
     * @return mixed
     */
    public function handle()
    {
        try{
            $path = $this->argument('path');
            if($path){
                try{
                    $data = json_decode(File::get($path));
                }
                catch (Illuminate\Contracts\Filesystem\FileNotFoundException $exception){
                    $this->info($exception->getMessage());
                }
            }
            else{
                $data = Storage::disk('local')->exists('bulstat_predefined_list.json') ? json_decode(Storage::disk('local')->get('bulstat_predefined_list.json')) : [];
            }

            $bar = $this->output->createProgressBar(count($data));

            $bar->start();

            DB::beginTransaction();
            foreach($data as $key => $org) {
                $org = (array)$org;
                try{
                    $eik = $org['eik'];
                    unset($org['eik']);
                    BulstatRegister::updateOrCreate(['eik' => $eik], $org);
                }
                catch(QueryException $e){
                    $this->error('Organisation ' . $org['name'] . ' EIK:'. $org['eik'] . ' could not be imported.' );
                    $this->error($e->getMessage());
                }

                $bar->advance();
            }
            DB::commit();
            $bar->finish();
        }
        catch(\Exception $e){
            DB::rollback();
            $this->error($e->getMessage());
        }
    }
}
