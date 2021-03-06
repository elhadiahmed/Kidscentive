<?php
/**
 * Author : Ahmed El Hadi
 */
class AuthController extends Controller {

	function getLogin() {
		return View::make('login');
	}

	function getRegister() {
		return View::make('register');
	}

	function getAddTask() {
		return View::make('addtask');
	}

	function getEditTask() {
		$id = Input::get('id');
		echo 'hadi' . $id;
		/*
		 $task = Task::FindOrFail($id);
		 return View::make('edittask')->with('task', $task);*/
	}

	function postLogin() {
		$rules = array('username' => 'required', 'password' => 'required');
		$validator = Validator::make(Input::all(), $rules);
		if ($validator -> fails()) {
			return Redirect::route('login') -> withErrors($validator);
		}
		$auth = Auth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password'), //'provider' => 'parent'
		), false);
		/*false remember me*/
		if (!$auth) {
			//$kidauth = $auth = KidAuth::attempt(array('username' => Input::get('username'), 'password' => Input::get('password'), 'provider'=>'kid'), false);
			//if (!$kidauth)
			return Redirect::route('login') -> withErrors(array('Invalid credentials were provided. '));
			//return Redirect::route('home');
		}
		$user = Auth::user();
		if ($user -> type == 'parent')
			return Redirect::route('kids.index');
		return Redirect::route('incentives.index');
	}

	function postRegister() {
		$rules = array('email' => 'required|email', 'username' => 'required', 'fullname' => 'required', 'password2' => 'required', 'password' => 'required');
		$validator = Validator::make(Input::all(), $rules);
		if ($validator -> fails()) {
			return Redirect::route('register') -> withErrors($validator);
		} else {
			if (Input::get('password') == Input::get('password2')) {
				$parent = new User;
				$parent -> fullname = Input::get('fullname');
				$parent -> username = Input::get('username');
				$parent -> email = Input::get('email');
				$parent -> password = Hash::make(Input::get('password'));
				$parent -> role = Input::get('role');
				$parent -> type = 'parent';
				$parent -> save();
				return Redirect::route('login') -> with('message', 'Thanks for registering!');
			} else {
				$validator2 = "Password Mismatch";
				return Redirect::route('register') -> withErrors($validator2);
			}
		}
	}

	function getEditProfile() {
		$parent = Auth::user();
		return View::make('parents.edit') -> with('user', $parent);
	}

	function postEditProfile() {
		$rules = array('username' => 'required', 'fullname' => 'required');
		$validator = Validator::make(Input::all(), $rules);
		if ($validator -> fails()) {
			return Redirect::route('parents.edit') -> withErrors($validator);
		} else {
			$parent = Auth::user();
			$parent -> fullname = Input::get('fullname');
			$parent -> username = Input::get('username');
			$parent -> email = Input::get('email');
			$parent -> role = Input::get('role');
			$parent -> save();
			return Redirect::route('login') -> with('message', 'Thanks for registering!');
		}

	}

	function postAddTask() {
		$rules = array('title' => 'required', 'points' => 'required');
		$validator = Validator::make(Input::all(), $rules);
		if ($validator -> fails()) {
			return Redirect::route('addtask') -> withErrors($validator);
		} else {
			$task = new Task;
			$task -> title = Input::get('title');
			$task -> points = Input::get('points');
			$task -> priority = Input::get('priority');
			$task -> assignee_id = Input::get('assignee_id');
			$task -> creator_id = Auth::user() -> id;
			$task -> done = false;
			$task -> expiry_date = Input::get('expiry_date');
			$task -> save();
			return Redirect::route('home') -> with('message', 'your task has been added!');

		}
	}

	function postEditTask() {

	}

	function signout() {
		Auth::logout();
		return Redirect::to('login');
	}

}
