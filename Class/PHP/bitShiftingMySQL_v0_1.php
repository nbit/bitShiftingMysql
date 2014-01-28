<?php
/***
 * Class BitShiftingBlindMySQL
 * Syntax: PHP
 * Date: 01-2014
 *
 * ###### PHP BitShiftingMySQL CLASS v0.1Beta ######
 *
 * Coder: Norberto Martínez a.k.a. kl4nx
 * Contact: klanx.martinez@gmail.com
 * Twitter: @klanx
 *
 * #################################################
 * NOTES:
 * --
 * ...
 * #################################################
 * MODERATORS
 * --
 * ...
 * #################################################
 *
 */
class BitShiftingMySQL{
    /**
     * @var bool
     */
    public $dataConnection = false;
    /**
     * @var string
     */
    public $userDB = '';
    /**
     * @var string
     */
    public $passDB = '';
    /**
     * @var string
     */
    public $portDB = '';
    /**
     * @var string
     */
    public $hostDB = 'localhost';
    /**
     * @var string
     */
    public $nameDB = '';
    /**
     * @var int
     */
    public $bits = 8;
    /**
     * @var int
     */
    public $position = 7;
    /**
     * @var int
     */
    public $substr_pos = 1;
    /**
     * @var int
     */
    public $substr_len = 1;
    /**
     * @var int
     */
    public $const = 0;
    /**
     * @var int
     */
    public $alter_const = 1;
    /**
     * @var string
     */
    public $binary = '';
    /**
     * @var string
     */
    public $response = '';
    /**
     * @var string
     */
    public $query_compare_template = "SELECT (ascii((substr('{value}',{substr_pos},{substr_len}))) >> {position})={bin};";
    /**
     * @var string
     */
    public $query_consult_ascci = "SELECT b'{bin}';";

    /**
     * __construct($value)
     * @param $value
     */
    public function __construct($value)
    {
        if(!$this->dataConnection){
             $this->connect();
        }
        $this->run($value);
    }

    /**
     * connect()
     */
    public function connect()
    {
        $this->dataConnection = mysql_connect($this->hostDB, $this->userDB ,$this->passDB);
        mysql_select_db($this->nameDB, $this->dataConnection );
    }

    /**
     * buildQuery($str, $params)
     * @param null $str
     * @param null $params
     * @return string
     */
    public function buildQuery($str=null,
        $params=null
    ) {
        $replace = array();
        foreach ($params as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        return strtr($str, $replace);
    }

    /**
     * query($query)
     * @param $query
     * @return string
     */
    public function query($query){
        $exec=mysql_query($query, $this->dataConnection);
        return mysql_result($exec, 0);
    }

    /**
     * getVal($value)
     * @param $value
     * @return string
     */
    public function getVal($value){
        $this->reset();
        while($this->position>=0){
            $params = array("value"=>$value,
            "substr_pos"=>$this->substr_pos,
            "substr_len"=>$this->substr_len,
            "position"=>$this->position,
            "bin"=> bindec($this->binary.$this->const)
            );

            $doQuery = $this->buildQuery($this->query_compare_template, $params);
            $response = $this->query($doQuery);

            if($response==1){
                $this->binary = $this->binary.$this->const;
            } else {
                $this->binary = $this->binary.$this->alter_const;
            }
            $this->position--;
        }
        $q = $this->buildQuery($this->query_consult_ascci, array("bin"=>$this->binary));
        $response = $this->query($q);
        return $response;
    }

    /**
     * setDefaultVars()
     */
    public function reset(){
        $this->bits = 8;
        $this->position = 7;
        $this->substr_pos = 1;
        $this->substr_len = 1;
        $this->const = 0;
        $this->alter_const = 1;
        $this->binary = '';
    }

    /**
     * run()
     * @param $value
     */
    public function run($value){
        $part=substr($value, 0, 1);
        for($i=0;$i<strlen($value);$i++){
            if($i>0){
                $part = substr($value, -($i), 1);
            }
            $this->response .= "*".$this->getVal($part);
        }
        // Enjoy ;3
        echo $this->response;
    }
}

?>
