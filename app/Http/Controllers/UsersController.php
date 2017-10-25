<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index ()
    {
        $TestJSON_MyUsers = [
            '0' => [
                    'first_name' => 'Anatoly',
                    'last_name' => 'Karpov',
                    'location' => 'Kazakstan'
            ],
            '1' => [
                    'first_name' => 'Arnold',
                    'last_name' => 'Shvartcnigger',
                    'location' => 'USA'
            ]
        ];

        //return $TestJSON_MyUsers;
        return view('admin.users.index', compact('TestJSON_MyUsers'));
    }



	public function create ()
	{
		return view('admin.users.create');
	}



	public function store (Request $request)
	{
		return $request->all();
	}
}

