<?php
namespace User;
class ControllerUser
{
	function register($f3, $args){
		$login = $args['login'];
		$pwd = $args['pwd'];

		$login = $this->_hash($login);
		$pwd = $this->_hash($pwd);

		$db = \F3::get('DB');

			//проверка на уже существование пользователя с таким логином
		$result = $db->exec("SELECT login FROM user WHERE login = ?", $login);
		if(count($result)!=0)
				die("-1"); //пользователь с таким логином уже есть существует

			$db->exec("INSERT INTO user (login, password) VALUES (:login, :pwd)", array("login"=>$login, "pwd"=>$pwd));
			$user_id = $this->_findUserIdByLogin($login, $db);
			$token = $this->_createToken($user_id);
			die("1;".$token);
		}

		function login($f3, $args){
			$login = $args['login'];
			$pwd = $args['pwd'];

			$login = $this->_hash($login);
			$pwd = $this->_hash($pwd);

			$db = \F3::get('DB');

			//проверка на существования такой пары логина и пароля
			$result1 = $db->exec("SELECT login, password FROM user WHERE login = :login AND password = :pwd", array("login"=>$login, "pwd"=>$pwd));
			if(count($result1)!=0)
			{
				$user_id = $this->_findUserIdByLogin($login, $db);
				//проверка на наличие токена от этого юзера
				$result2 = $db->exec("SELECT id FROM token_user WHERE id_user_fk = $user_id");
				if(count($result2)!=0)
					$db->exec("DELETE FROM token_user WHERE id_user_fk = $user_id");
				$token = $this->_createToken($user_id);
				die("1;".$token); 
			}
			else
				die("-1");
		}

		function addReport($f3, $args){
			$token = $args['token'];
			$report_id = $args['report_id'];

			$db = \F3::get('DB');

			$user_id = $this->_findUserIdByToken($token, $db);
			if($user_id==0)
			die(); //Такой юзер не зарегистрирован

		//Проверка на уже добавление доклада в посещенные
		$result = $db->exec("SELECT id_user_fk FROM user_report WHERE id_user_fk = :user_id AND id_report_fk = :report_id", array("user_id"=>$user_id, "report_id"=>$report_id));
		if(count($result)!=0)
			die("-1"); 

		$db->exec("INSERT INTO user_report (id_user_fk, id_report_fk) VALUES (:user_id, :report_id)", array("user_id"=>$user_id, "report_id"=>$report_id));
		die("1");
	}

	function removeReport($f3, $args){
		$token = $args['token'];
		$report_id = $args['report_id'];

		$db = \F3::get('DB');

		$user_id = $this->_findUserIdByToken($token, $db);
		if($user_id==0)
			die(); //Такой юзер не зарегистрирован


		$result = $db->exec("DELETE FROM user_report WHERE id_user_fk = :user_id AND id_report_fk = :report_id", array("user_id"=>$user_id, "report_id"=>$report_id));
		die("1");
	}

	function LoadAllVisitedReports($f3, $args){
		$token = $args['token'];	

		$db = \F3::get('DB');

		$user_id = $this->_findUserIdByToken($token, $db);
		if($user_id==0)
			die(); //Такой юзер не зарегистрирован

		$result = $db->exec("SELECT id_report, report_name, time FROM user_report INNER JOIN report ON user_report.id_report_fk = report.id_report WHERE id_user_fk = ?", $user_id);
		die(json_encode($result, JSON_UNESCAPED_UNICODE));
	}

	/* Necessary Functions for methods above */
	/*---------------------------------------*/
	function _hash($str){
		return md5(md5($str));
	}

	function _createToken($user_id){
		$length = 30;
		$chars = 'abdefhknrstyz23456789';
		$random_str = "";
		for ($i=0; $i < $length; $i++)
			$random_str .= substr($chars, rand(1, strlen($chars))-1, 1);

		$token = $this->_hash($random_str);

		$db = \F3::get('DB');
		$db->exec('INSERT INTO token_user (id_user_fk, token, time_add) VALUES (?,?,?)', array($user_id, $token, date("Y-m-d H:i:s")));
		return $token;
	}

	function _findUserIdByLogin($login, $db){
		$result = $db->exec("SELECT id FROM user WHERE login = ?", $login);
		$user_id = (int)$result['0']['id'];
		return $user_id;
	}

	function _findUserIdByToken($token, $db){
		$result = $db->exec("SELECT id_user_fk FROM token_user WHERE token = ?", $token);
		$user_id = (int)$result['0']['id_user_fk'];
		return $user_id;
	}
}
?>