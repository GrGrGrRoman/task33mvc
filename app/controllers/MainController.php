<?php 

namespace App\controllers;

use App\core\Controller;
use App\models\Account;
use App\models\Main;

class MainController extends Controller
{
    public function indexAction()
    {
        $this->view->render('Главная');
    }
    
    public function chatroomAction()
    {
        if (!Account::checkAuth())
        {
            $this->view->redirect('login');
        }

        $objChatroom = new Main;
        $objUser = new Account;

        $viewArr = 
        [
            'chatrooms' => $objChatroom->getAllChatRooms(),
            'users' => $objUser->getAllUsers(),
        ];

        $this->view->render('Чат', $viewArr);
    }    
}