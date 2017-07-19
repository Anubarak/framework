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

    protected   $table = null;
    protected   $template = null;
    protected   $primary_key = null;
    protected   $id = 0;


    public function init(){
        $class = Anu::getClassByName($this, "Record", true);
        $this->table = $class->getTableName();
        $this->primary_key = $class->getPrimaryKey();
        if($userId = anu()->session->get('user_id', null)){
            $this->currentUser = $this->getUserById($userId);
        }
    }


    /**
     * @param $title
     * @param $email
     * @param $password
     */
    public function login($title = null, $email = null, $password = null){
        if(!$title && !$email){
            return true;
        }

        $userId = anu()->database->get($this->table, $this->primary_key, array(
             'OR' => array(
                 'title'    => $title,
                 'email'    => $email
             )
        ));

        if($userId){
            $this->currentUser = $this->getUserById($userId);
            anu()->session->set('user_id', $userId);
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function logout(){
        $this->currentUser = null;
        anu()->session->set('user_id', null);
    }


    public function getCurrentUser(){
        return $this->currentUser;
    }

    /**
     * @param $user            userModel
     * @return bool|int|string
     */
    public function saveUser($user){
        return $this->saveElement($user);
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