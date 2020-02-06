<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\PredefinedOrganisation;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class PopulateList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:predefinedList';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    const COLUMN_MAP = [
        0 => 'eik',
        1 => 'reg_number',
        2 => 'reg_date',
        3 => 'status',
        4 => 'status_date',
        6 => 'name',
        8 => 'city',
        9 => 'address',
        10 => 'phone',
        12 => 'email',
        18 => 'tools',
        19 => 'goals',
        20 => 'description'
        
    ];
    
//    array:22 [
//        0 => "﻿"Булстат""
//        1 => "№ на вписване"
//        2 => "Дата на вписване"
//        3 => "Статус"
//        4 => "Дата на статуса"
//        5 => "Вид"
//        6 => "Наименование"
//        7 => "Организационна форма"
//        8 => "Седалище"
//        9 => "Адрес"
//        10 => "Телефони"
//        11 => "Факсове"
//        12 => "Ел. поща"
//        13 => "Съд по регистрация"
//        14 => "Фирмено дело №"
//        15 => "Година"
//        16 => "Партиден номер"
//        17 => "Цели"
//        18 => "Средства"
//        19 => "Предмет на дейност"
//        20 => "Наименование на УО"
//        21 => ""
//      ]

    const DELIMITER = ',';

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
            //$data = array_map('str_getcsv', file(storage_path('app/csv/predefined_list.csv')));  
            $data = csv_to_array(storage_path('csv/predefined_list.csv'));
            array_shift($data);  // remove column header

            $bar = $this->output->createProgressBar(count($data));
            
            $columns = self::COLUMN_MAP;
            $bar->start();

            DB::beginTransaction();
            foreach($data as $key => $org) {
                $predefinedOrg = new PredefinedOrganisation();
                
                foreach($columns as $index => $column) {
                    if(isset($org[$index])){
                        if($index == 20){ //description column
                            $predefinedOrg->{$column} = $this->getOrgDescription($org);
                        }
                        else{
                            $predefinedOrg->{$column} = $org[$index];
                        }
                    }
                }
                
                try{
                    $predefinedOrg->save();
                }
                catch(QueryException $e){
                    //eik already exists
                    if($e->getCode() == '23000' && $e->errorInfo[1] == 1062){
                        if($predefinedOrg->eik != 0){
                            //update existing record with newer
                            $existingOrg = PredefinedOrganisation::where('eik', $predefinedOrg->eik)->first(); 
                            if($existingOrg){
                                foreach($columns as $index2 => $column) {
                                    if(isset($org[$index2])){
                                        if($index2 == 20 && !empty($this->getOrgDescription($org))){ //description column
                                            $existingOrg->{$column} = $this->getOrgDescription($org);
                                        }
                                        else if(!empty($org[$index2])){
                                            $existingOrg->{$column} = $org[$index2];
                                        }
                                    }
                                }
                                $existingOrg->save();   
                            }
                            continue;
                        }
                        else{
                            //eik is empty - don't-save
                            $bar->clear();
                            $this->warn('Невалиден ЕИК: "' . $org[0] . '"');
                            $bar->display();
                            continue;
                        }
                    }
                    throw $e;
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

    private function getOrgDescription($org)
    {
        $desc = '';
        if(isset($org[14]) && !empty($org[14])){
            $desc .= 'дело номер:' . $org[14];
        }
        if(isset($org[15]) && !empty($org[15])){
            $desc .= ', година: ' . $org[15];
        }
        if(isset($org[13]) && !empty($org[13])){
            $desc .= ', съд: ' . $org[13];
        }
        if(isset($org[20]) && !empty($org[20])){
            $desc .= ', Наименование на УО: ' .  $org[20];
        }

        return $desc;
    }
}
