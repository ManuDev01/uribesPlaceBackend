<?php

    namespace App\Http\Controllers;

    use App\Services\UserService;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

  Class UserController extends Controller{
    protected $users;

    public function __construct(UserService $UserService) {
        $this->users = $UserService;
    }

    public function getAll(){
        $allUsers = $this->users->listAll();

        return response() -> json (['data' => $allUsers], 200);
    }

  }
