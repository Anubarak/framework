<?php

    namespace Anu;

    /*
     * This should be a wrapper for our main program flow.
     * Useful to have an overview.
     * 
     * Certainly this should be extended for your use. (for example different application states)
     */
    
    class CApplication{
        private $m_navigation  = array(
                                0 => array('page' => 'home', 'title' => 'Home'),
                                1 => array('page' => 'newGame', 'title' => 'Create New Game'),
                                2 => array('page' => 'game', 'title' => 'View Last Games'),
        );

        private $m_REQUEST     = array();
        
        //title and description are only given for testing to the constructor (look at index.php to understand the progress)
        public function __construct($_applicationTitle, $_applicationSubtitle)
        {
            $this->m_title       = $_applicationTitle;
            $this->m_subtitle    = $_applicationSubtitle;
            
            //feel free to use them ;) these are the global PHP requests
            $this->m_REQUEST     = array_merge($_GET, $_POST);
        }
        
        public function run()
        {
			anu()->request->process();
        }
        
		private function getLoginButton(){
			if(isset($_SESSION["user_id"])){
				$userModel = new CUserModel($_SESSION["user_id"]);
				return "Hallo" . $userModel->getName().'<a href="" class="" id="logoutBtn">Logout</a>';
			}else{
				return '<a href="" class="" id="loginBtn">Login</a>';
			}
		}
		
        private function getNavigationString()
        {
            $navigation = '';
            $max = count($this->m_navigation);
            for($i = 0; $i < $max; ++$i)
            {
                $navigation .= '<a href="?page='.$this->m_navigation[$i]['page'].'">'.$this->m_navigation[$i]['title'].'</a>';
            }
            
            return $navigation;
        }
    }
    
?>