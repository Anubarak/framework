<?php
/**
 * Created by PhpStorm.
 * User: SECONDRED
 * Date: 18.07.2017
 * Time: 16:39
 */

namespace Anu;


class userService extends baseService
{
    private $currentUser = null;

    public      $tableName = null;
    public      $recordName = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;


    public function init(){
        $class = Anu::getClassByName($this, "Record", true);
        $this->tableName = $class->getTableName();
        $this->primary_key = $class->getPrimaryKey();
        $this->model = 'user';
        if($userId = anu()->session->get('user_id', null)){
            $this->currentUser = $this->getUserById($userId);
        }
        anu()->template->addAnuJsObject($this->currentUser, 'user');
    }


    /**
     * @param $title
     * @param $email
     * @param $password
     */
    public function login($title = null, $email = null, $password){
        $response = array();
        if(!$title && !$email){
            $response['username'] = 'Email or Username not set';
        }
        if(!$password){
            $response['password'] = 'Password not set';
        }

        $userId = anu()->database->get($this->tableName, $this->primary_key, array(
             'OR' => array(
                 'title'    => $title,
                 'email'    => $email
             ),
        ));

        if(!$userId){
            $response['username'] = Anu::parse('No user Found with email = {email} or title = {username}', array(
                'email' => $email,
                'username' => $title
            ));
        }

        if($userId && ($user = $this->getUserById($userId))){
            if (password_verify($password, $user->password)) {
                $this->currentUser = $user;
                anu()->session->set('user_id', $userId);
                return true;
            }
            $response['password'] = 'Password is not correct';
        }

        return $response;
    }

    /**
     *
     */
    public function logout(){
        $this->currentUser = null;
        anu()->session->set('user_id', null);
        return null;
    }


    public function getCurrentUser(){
        return $this->currentUser;
    }


    /**
     * @param $user            userModel
     * @return bool|int
     */
    public function saveUser($user){
        $this->defineDefaultValues($user);

        if(!$this->validate($user)){
            return false;
        }

        if(($user->newPassword || $user->repeatPassword) && ($user->repeatPassword !== $user->password)){
            $user->addError('password', 'Passwords do not match');
            $user->addError('repeatPassword', 'Passwords do not match');
            return false;
        }

        //check if its a new entry of if we should update an existing one
        $record =  new userRecord();
        $recordValues = $record->defineAttributes();
        if(!$user->id){
            $data = $user->getData();
            $values = array();
            foreach ($user->defineAttributes() as $key => $value){
                if($data[$key] !== 'now()'){
                    if(array_key_exists($key, $data) && array_key_exists($key, $recordValues)){
                        $values[$key] = ($data[$key])? $data[$key] : 0;
                    }
                }else{
                    if(array_key_exists($key, $recordValues)){
                        $values["#".$key] = $data[$key];
                    }
                }
            }
            //new entry -> crypt password
            if(isset($values['password'])){
                $values['password'] = password_hash($values['password'], PASSWORD_DEFAULT);
            }

            anu()->database->insert($this->tableName, $values);

            $id = anu()->database->id();
            $user->id = $id;
            return $id;
        }else{
            $data = $user->getData();
            $values = array();
            foreach ($user->defineAttributes() as $key => $value){
                if(isset($data[$key])){
                    if($data[$key] !== 'now()'){
                        if(array_key_exists($key, $data) && array_key_exists($key, $recordValues)){
                            $values[$key] = ($data[$key])? $data[$key] : 0;
                        }
                    }else{
                        if(array_key_exists($key, $recordValues)) {
                            $values["#" . $key] = $data[$key];
                        }
                    }
                }
            }

            if(isset($values['newPassword'])){
                $values['password'] = password_hash($values['newPassword'], PASSWORD_DEFAULT);
                unset($values['newPassword']);

            }

            anu()->database->update($this->tableName, $values, array(
                $this->tableName . "." . $this->primary_key => $user->id
            ));

            return anu()->database->id();
        }
    }

    /**
     * @param $userId
     * @return baseModel|null
     */
    public function getUserById($userId){
        return $this->getElementById($userId);
    }


    /**
     * @param $className
     * @param $permission
     * @param $entry baseModel|entryModel
     * @param $user userModel
     * @return bool
     */
    public function can($className, $permission, $entry = null, $user = null){
        if(!$user){
            $user = $this->currentUser;
        }
        if($user && $user->admin){
            //return true;
        }

        if($className && $permission){
            //check individual permissions first -> entry in userPermission Table
            if($user){
                $userHasPermission = anu()->database->has('userpermission', array(
                    'AND' => array(
                        'user_id'       => $user->id,
                        'permission'    => $className . "." .  $permission,
                )));

                if($userHasPermission){
                    return true;
                }
            }


            $permissions = anu()->$className->definePersmissions();
            $users = $permissions[$permission];
            foreach ($users as $userPermissions){
                switch ($userPermissions){
                    case Permission::All:
                        return true;
                        break;
                    case Permission::Admin:
                        if($user && (bool)$user->admin){
                            return true;
                        }
                        break;
                    case Permission::Author:
                        if($entry && $user){
                            if($entry->getAttribute('author_id') == $user->id){
                                return true;
                            }
                        }
                        break;
                    case Permission::LoggedIn:
                        if($user != null){
                            return true;
                        }
                        break;
                    case Permission::InGroup:
                        //TODO
                        break;
                    default:
                        //TODO everything else... custom permissions
                        break;

                }
            }
        }
        return false;
    }

    /**
     * Add Permission for user
     *
     * @param $user
     * @param $permission
     * @return bool|int|string
     */
    public function addPermission($user, $permission){
        if(is_numeric($user)){
            $user = $this->getUserById($user);
        }
        if(!$user || !$permission){
            return false;
        }

        anu()->database->insert('userpermission', array(
            'user_id'       => $user->id,
            'permission'    => $permission
        ));
        return true;
        //return anu()->database->id();
    }
}