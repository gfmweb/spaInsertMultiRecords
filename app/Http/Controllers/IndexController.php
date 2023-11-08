<?php

namespace App\Http\Controllers;

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

    public function importUsers(): JsonResponse
    {

        return response()->json(['all'=>400,'updated'=>200,'inserted'=>100]);
    }
}
