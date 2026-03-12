<?php

  use app/Services/UserService;
    use Illuminate\Http\Request;

  Class UserController {
    protected $users;

    public function __contruct(UserService $UserService) {
        $this->users;
    }

    public function getAll(){
        $allUsers = $this->users->listAll();
    
        return response() -> json (['data' => $allUsers], 200);
    }
    
  }
?>
