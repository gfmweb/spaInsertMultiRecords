<?php

namespace App\Services;

use App\Contracts\GetClients;
use App\Jobs\updateUserRecordsJob;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
class getClientService implements GetClients
{
    private $clients;
    private $newCount;
    private $updateCount;
    private $allCount;
    private $updateUsers;
    private $toInsert;
    private $toUpdate;

    /**
     * Получение командв на обработку
     * @return mixed
     */
    public function getClients()
    {
        $this->ApiRequest();
        $this->clearClients();
        $this->getCounts();
        $this->prepareToInsertUpdate();
        $this->createClients();
        $this->updateClients();
        return (object)['all'=>$this->allCount,'inserted'=>$this->newCount,'updated'=>$this->updateCount];
    }

    /**
     * Запрос к Апи
     * @return void
     */
    private function ApiRequest():void
    {
        $response = Http::get('https://randomuser.me/api/?results=5000');
        $clients = collect();
        foreach ($response->object()->results as $key=>$value) {
            $clients->push($value);
        }
        $this->clients =  $clients;
    }

    /**
     * Оставляем в каждом поступившем пользователе только необходимую нам информацию
     * @return object
     */
    private function clearClients()
    {
        $clear = collect();
        foreach ($this->clients as $client)
        {
             $clear->push((object)[
                 'first_name' => $client->name->first,
                 'last_name' => $client->name->last,
                 'email' => $client->email,
                 'age' => $client->dob->age
             ]);
        }
        return $this->clients = $clear;
    }

    /**
     * Получаем новые значения счетчиков
     * @return void
     */
    private function getCounts():void
    {
        $fio = [];
        foreach ($this->clients as $client)
        $fio[] = $client->first_name.$client->last_name;
        $this->updateUsers  = User::whereIn(DB::raw("CONCAT(`first_name`,`last_name`)"),$fio)->get();
        $this->updateCount = count($this->updateUsers);
        $this->newCount = count($this->clients) - $this->updateCount;
        $this->allCount = $this->newCount + User::count();
    }

    /**
     * @return void
     * Разбиение данных на 2 потока (новые / для обновления)
     */
    private function prepareToInsertUpdate():void
    {
       $this->toInsert = collect();
       $this->toUpdate = collect();
       foreach ($this->clients as $client){
           $new = true;
           foreach ($this->updateUsers as $user){
               if($user->first_name == $client->first_name && $user->last_name == $client->last_name){
                   $new = false; break;
               }
           }
           if(!$new){$this->toUpdate->push($client);}
           else{$this->toInsert->push($client);}
       }

    }

    /**
     * Запись новыйх пользователей
     * @return void
     */
    private function createClients():void
    {
        $chunks = $this->toInsert->chunk(500);
        $chunks->toArray();
        foreach($chunks as $chunk=>$value){
            $data = [];
             foreach ($value as $el){
                 $el = (array)$el;
                 $el['created_at'] = Carbon::now()->format('Y-m-d H:i:s');
                 $el['updated_at'] = $el['created_at'];
                 $data[] = $el;
             }
            User::insert($data);
        }
    }

    /**
     * Запуск ресурсоёмкой работы по обновлению данных
     * @return void
     */
    private function updateClients():void
    {
        dispatch(new updateUserRecordsJob($this->updateUsers,$this->toUpdate));
    }
}
