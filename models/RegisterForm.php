<?php
namespace app\models;

use Yii;
use yii\base\Model;
use app\models\User; // Dein User-Modell

class RegisterForm extends Model
{
    public $username;
    public $password;
    
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            ['username', 'string', 'min' => 3, 'max' => 255],
            ['password', 'string', 'min' => 6],
        ];
    }
    
    public function register()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new User();
        $user->username = $this->username;
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        $user->auth_key = Yii::$app->security->generateRandomString();
        
        return $user->save() ? $user : null;
    }
}