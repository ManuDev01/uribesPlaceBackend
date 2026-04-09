<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\JwtService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('users')]
class UserController extends Controller
{
    protected $users;
    protected $jwt;

    public function __construct(UserService $UserService, JwtService $jwtService)
    {
        $this->users = $UserService;
        $this->jwt = $jwtService;
    }

    #[Post('/registerUser')]
    public function registerUser(Request $request)
    {
        $userData = $request->all();
        $newUser = $this->users->register($userData);

        return response()->json(['data' => $newUser], 201);
    }

    #[Get('/getAllUsers')]
    public function getAll()
    {
        $allUsers = $this->users->listAll();

        return response()->json(['data' => $allUsers], 200);
    }

    #[Post('/login')]
    public function login(Request $request)
    {

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

    #[Patch('/updateUser')]
    public function updateUser(Request $request)
    {
        $userData = $request->all();
        $this->users->updateUser($userData);

        return response()->json(['message' => 'User updated successfully'], 200);
    }

    #[Delete('/delete/{id}')]
    public function deleteUser(Request $request)
    {
        $id = $request->route('id');
        $this->users->delete($id);

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    #[Get('/tiendasSeguidas/{userId}')]
    public function getTiendasSeguidas($userId){
        return response()->json($this->users->getTiendasSeguidas($userId), 200);
    }

    #[Get('/estados')]
    public function getEstados(){
        return response()->json($this->users->getEstados(), 200);
    }

    #[Get('/municipios/{idState}')]
    public function getMunicipios($idState){
        return response()->json($this->users->getMunicipios($idState), 200);
    }

    #[Post('/entradaASitio')]
    public function visitaSitio(){
        
    }

    # TODO: Verificar Funcionamiento del Endpoint
    #[Patch('/tiempoSitio/{idVisitante}/{tiempoEnSitio}')]
    public function salidaSitio($idVisitante, $tiempoEnSitio){
        return responsa()->json($this->users->salidaSitio($idVisitante, $tiempoEnSitio));
    }
}
