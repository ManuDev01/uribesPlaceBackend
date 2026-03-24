<?php

    namespace App\Http\Controllers;

    use App\Services\UserService;
    use App\Services\JwtService;
    use Illuminate\Http\Request;
    use App\Http\Controllers\Controller;

    use Spatie\RouteAttributes\Attributes\Get;
    use Spatie\RouteAttributes\Attributes\Post;
    use Spatie\RouteAttributes\Attributes\Delete;
    use Spatie\RouteAttributes\Attributes\Prefix;

    #[Prefix('users')]
  Class UserController extends Controller{
    protected $users;
    protected $jwt;

    public function __construct(UserService $UserService, JwtService $jwtService) {
        $this->users = $UserService;
        $this->jwt = $jwtService;
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

        $user = $this->users->login($request->all());

        if ($user) {
            $token = $this->jwt->createToken($user);

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'data' => $user], 200);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    #[Delete('/delete/{id}')]
    public function deleteUser(Request $request) {
            $id = $request->route('id');
            $this->users->delete($id);

            return response()->json(['message' => 'User deleted successfully'], 200);
    }
  }
