<?php

    namespace app\model;
    
    require_once 'app/craft/core/database.php';
    
    use Exception;
    use app\core\CDatabase;
    
    class CUserModel
    {
        private $m_user_id = 0;
        private $m_name = '';
        private $m_email = '';
        private $m_password = '';
        
        public function __construct($_user_id)
        {
            if(!isset($_user_id) || !is_int($_user_id))
            {
                throw new Exception('User-ID is not specified.');
            }
            else
            {
                $this->m_user_id = $_user_id;
                
                try
                {
                    $this->loadUserByID();
                } 
                catch(Exception $e) 
                {
                    throw new Exception($e->getMessage());
                }
            }
        }
         
        private function loadUserByID()
        {
            $db = CDatabase::getInstance();
            $db->prepare("select * from user WHERE user_id= ?");
            $db->bindParams(array($this->m_user_id));
            $db->execute();

            $result = $db->fetch();
            while ($row = $result->fetch_assoc()){
                $this->m_name      = $row['name'];
                $this->m_email      = $row['email'];
                $this->m_password      = $row['password'];
            }//end of while
        }

        public static function getAllUserAsArray()
        {
            $db = CDatabase::getInstance();
            $db->prepare("select * from user");
            $db->execute();

            $result = $db->fetch();
            while ($row = $result->fetch_assoc()){
                $userArray[] = new CUserModel($row['user_id']);
            }
            return $userArray;
        }


        public function getUserId()
        {
            return $this->m_user_id;
        }

        
        public function getName()
        {
            return $this->m_name;
        }

        public  function getPassword(){
            return $this->m_password();
        }

        public function getEmail(){
            return $this->m_email;
        }
    }
    