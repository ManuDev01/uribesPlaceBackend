<?php

    namespace App\Http\Controllers;

    use App\Services\UserService;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use Spatie\RouteAttributes\Attributes\Get;
    use Spatie\RouteAttributes\Attributes\Post;
    use Spatie\RouteAttributes\Attributes\Prefix;

    #[Prefix('users')]
  Class UserController extends Controller{
    protected $users;

    public function __construct(UserService $UserService) {
        $this->users = $UserService;
    }

    #[Post('/registerUser')]
    public function registerUser(Request $request){
        $userData = $request->all();
        $newUser = $this->users->register($userData);

        return response() -> json (['data' => $newUser], 201);
    }

    #[Get('/getAllUsers')]
    public function getAll(){
        $allUsers = $this->users->listAll();

        return response() -> json (['data' => $allUsers], 200);
    }

    #[Get('/login')]
    public function login(Request $request){
        $loginData = $request->all();
        $user = $this->users->login($loginData);

        if ($user) {
            return response()->json(['data' => $user], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

  }
