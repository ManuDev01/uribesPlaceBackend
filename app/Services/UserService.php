<?php

    namespace App\Services;

    use Illuminate\Support\Facades\DB;

  class UserService {
  
  public function listAll(){
      return DB::select("SELECT * FROM users");
  }
      
    
  }

?>
