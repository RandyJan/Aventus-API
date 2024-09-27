<?php
namespace App\Traits;
trait auth{

    public function basicauth($requestHeader){
        $username = env('USER_NAME');
        $password = env('PASSWORD');
        $authHeader = 'Basic ' . base64_encode($username . ':' . $password);
        if($requestHeader == $authHeader){
            return true;
        }   
        return false;
    
    }
}
?>