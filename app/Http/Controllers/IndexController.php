<?php

namespace App\Http\Controllers;

use App\Contracts\GetClients;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Illuminate\Http\JsonResponse;
class IndexController extends Controller
{

    public function showIndexPage(): View
    {
        return view('pages.index');
    }

    /**
     * @return
     */
    public function getUsersCount(): JsonResponse
    {
        return response()->json(User::count());
    }

    public function importUsers(GetClients $service)//: JsonResponse
    {
        $result = $service->getClients();
        return response()->json(['all'=>$result->all,'updated'=>$result->updated,'inserted'=>$result->inserted]);
    }
}
