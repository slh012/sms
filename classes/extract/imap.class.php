<?php
class imap extends email
{

    protected $imap;

    static public $instance;
    static public $instance_f = array();


    private function __construct()
    {

    }

    //factory
    static public function f($connection)
    {

        list($host, $email, $password)=explode('~',$connection);

        if(empty(self::$instance_f[$connection])){
            debug::output("Starting Connection to $email ($host)");
          $i=new imap;
          $i->connect($host,$email,$password);
          self::$instance_f[$connection] = $i;

          return $i;
        }else{
          return self::$instance_f[$connection];
        }

    }

    //instance
    static public function i($connection='')
    {

        if(!isset(self::$instance))
            {
            
                list($host, $email, $password)=explode('~',$connection);
                debug::output("Starting Connection to $email ($host)");
                    self::$instance=new imap;

                    self::$instance->connect($host, $email, $password);

            }

            return self::$instance;
    }

    public function connect($host,$email,$password){
        
        $this->imap = imap_open($host,$email,$password) ;

        if(!is_resource($this->imap))
        {
                debug::output($email);
            throw new emailException(imap_last_error($this->imap),imap_errors($this->imap));
        }else{
             debug::output("Connected to $email");
        }



    }

    public function check(){
        debug::output("Starting Check...");
        $object = imap_check($this->imap);
        if(!is_object($object)){
            throw new emailException(imap_last_error($this->imap),imap_errors($this->imap));
        }else{
            return $object;
        }
    }
    
    public function search($criteria = 'ALL'){
        debug::output("Starting Search...");
        $emails = imap_search($this->imap,$criteria);
        if(!is_array($emails))
        {
            throw new emailException(imap_last_error($this->imap),imap_errors($this->imap));
        }else{
            return $emails;
        }
    }

    public function fetch_overview ($email_number='0', $options = '0'){
        debug::output("Fetching Overview...");
        $overview =  imap_fetch_overview($this->imap,$email_number,$options);
        if(empty($overview))
        {
            throw new emailException(imap_last_error($this->imap),imap_errors($this->imap));
        }else{
            return  $overview;
        }
    }

    public function fetch_body($email_number='0',$section='1', $options='0'){
        //debug::output("Fetching Body...");
        $body =  imap_fetchbody($this->imap,$email_number,$section, $options);
        if(empty($body))
        {
                debug::output("No Email in body...");
            //throw new emailException(imap_last_error($this->imap),imap_errors($this->imap));
        }else{
            return  $body;
        }
    }

    public function  headerinfo($msgnum){
        $object = imap_headerinfo($this->imap, $msgnum); 
        if(!is_object($object)){
            throw new emailException(imap_last_error($this->imap),imap_errors($this->imap));
        }else{
            return $object;
        }
        
    }
    
    
    public function close(){
        debug::output("Disconnecting...");
        if(imap_close($this->imap) === false)
        {
            throw new emailException(imap_last_error($this->imap),imap_errors($this->imap));
        }
    }

}
?>
