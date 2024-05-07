<?php

namespace App\controllers;

use App\core\Controller;
use App\models\Account;
use App\lib\Helpers;
use function App\d;
use function App\dd;

class AccountController extends Controller
{
    public function loginAction()
    {
        if (isset($_POST['join']) and (empty($_POST['email']) or empty($_POST['password']))) {
            $_SESSION['errors'][] = 'Заполните поля';
        }

        if (isset($_POST['join']) and $_POST['token'] == $_COOKIE['PHPSESSID']) {            
            $objUser = new Account;

            if (!$objUser->checkLogin($_POST['email'], (string)$_POST['password'])) {
                $_SESSION['errors'][] = 'Email/пароль введены не верно!';
                Helpers::get_alert();
            } elseif (!$objUser->checkReg($_POST['email'])) {
                $_SESSION['errors'][] = 'Проверьте почту, подтвердите регистрацию по ссылке!';
                Helpers::get_alert();
            } else {
                $objUser->setEmail($_POST['email']);
                $objUser->setLoginStatus(1);
                $objUser->setLastLogin(date('Y-m-d H:i:s'));
                $userData = $objUser->getUserByEmail();

                if (is_array($userData) and count($userData) > 0) {
                    $objUser->setId($userData['id']);
                    if ($objUser->updateLoginStatus()) {
                        $_SESSION['user'][$userData['id']] = $userData;
                        $_SESSION['user'][$userData['id']]['login_status'] = 1;
                        $this->view->redirect('chatroom');
                    } else {
                        $_SESSION['errors'][] = 'Вход не удался';
                        Helpers::get_alert();
                    }
                }
            }
        }
        $this->view->render('Вход');
    }

    public function registerAction()
    {
        if (isset($_POST['join']) and (empty($_POST['email']) or empty($_POST['password']))) {
            $_SESSION['errors'][] = 'Заполните поля';
        }

        if (isset($_POST['join']) and !empty($_POST['email']) and $_POST['token'] == $_COOKIE['PHPSESSID']) {
            if (!$this->model->validate(['email', 'password',], $_POST)) {
                $_SESSION['errors'][] = 'Проверьте правильность ввода логина/пароля';
            }
            $objUser = new Account;
            $token = $objUser->createToken();
            $objUser->setEmail($_POST['email']);
            $objUser->setName($_POST['email']);
            $objUser->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT));
            $objUser->setLoginStatus(0);
            $objUser->setRegStatus(0);
            $objUser->setAvatar(0);
            $objUser->setHideEmail(0);
            $objUser->setToken($token);
            $objUser->setLastLogin(date('Y-m-d H:i:s'));
            if ($objUser->getUserByEmail($_POST['email'])) {
                $_SESSION['errors'][] = 'Пользователь с таким email уже зарегистрирован';
            }
            if (!$objUser->getUserByEmail($_POST['email']) and $objUser->save()) {
                $lastId = $objUser->dbConn->lastInsertId();
                $objUser->setId($lastId);
                $_SESSION['success'][] = 'Проверьте почту, подтвердите регистрацию по ссылке!';
                Helpers::get_alert();
            } else {
                $_SESSION['errors'][] = 'Что-то пошло не так... повторите попытку регистрации';
                Helpers::get_alert();
            }
        }
        $this->view->render('Регистрация');
    }

    public function confirmAction()
    {
        $objUser = new Account;

        if (!$objUser->checkTokenExists($this->route['token']))
        {
            $_SESSION['errors'][] = 'Ошибка подтверждения регистрации по EMAIL, token не найден';
            Helpers::get_alert();
            exit;
        }
        $_SESSION['success'][] = 'Регистрация завершена, можно войти в систему';
        $objUser->activate($this->route['token']);
        $this->view->redirect('/login');
    }

    public function logoutAction()
    {
        unset($_SESSION['user']);
        session_destroy();
        $this->view->redirect('login');
    }

    public function leaveAction()
    {
        if (isset($_POST['action']) and $_POST['action'] == 'leave') {
            $objUser = new Account;
            $objUser->setLoginStatus(0);
            $objUser->setLastLogin(date('Y-m-d h:i:s'));
            $objUser->setId($_POST['userId']);

            if ($objUser->updateLoginStatus()) {
                unset($_SESSION['user']);
                session_destroy();
                echo json_encode(['status' => 1, 'msg' => "Вышел"]);
            } else {
                echo json_encode(['status' => 0, 'msg' => "Может позже..."]);
            }
        }
    }

    public function profileAction()
    {
        if (!Account::checkAuth()) {
            $this->view->redirect('login');
        }

        $userId = array_key_first($_SESSION['user']);

        if (isset($_POST['edit']) and $_POST['token'] == $_COOKIE['PHPSESSID']) {
            $objUser = new Account;

            if (isset($_POST['username']) and $_SESSION['user'][$userId]['name'] == $_POST['username']) {
                $objUser->setName($_POST['username']);
                $objUser->updateUser($userId);
                $_SESSION['user'][$userId]['name'] = $_POST['username'];
            } elseif (isset($_POST['username'])) {
                if ($objUser->checkUserExists($_POST['username'])) {
                    $_SESSION['errors'][] = 'Такое ИМЯ уже используется';
                } else {
                    $objUser->setName($_POST['username']);
                    $objUser->updateUser($userId);
                    $_SESSION['user'][$userId]['name'] = $_POST['username'];
                }
            }

            if (isset($_POST['hide_email'])) {
                $objUser->setHideEmail(1);
                $objUser->updateHideEmail($userId);
                $_SESSION['user'][$userId]['hide_email'] = 1;
            } else {
                $objUser->setHideEmail(0);
                $objUser->updateHideEmail($userId);
                $_SESSION['user'][$userId]['hide_email'] = 0;
            }
        }
        $this->view->render('Профиль');
    }

    public function profileavatareditAction()
    {
        if (!Account::checkAuth()) {
            $this->view->redirect('login');
        }

        $userId = array_key_first($_SESSION['user']);

        if (!empty($_FILES)) {
            $objUser = new Account;

            if (!$objUser->imageValidate($_FILES)) {
                exit;
            }

            $file = $objUser->imageAdd($userId);

            if (!$file) {
                $_SESSION['errors'][] = 'Ошибка добавления в БД';
                echo 'Ошибка добавления в БД';
                exit;
            }

            $objUser->imageUpload($_FILES['img']['tmp_name'], $userId);
            $_SESSION['user'][$userId]['avatar'] = 1;
        }
        $this->view->redirect('profile');
    }
}
