<?php

const DB_HOST                 = '127.0.0.1';     //host of mysql
const DB_USER                 = 'root';          //username of the mysql user
const DB_PASS                 = 'mysqlbk';       //password for mysql user
const DB_DATABASE             = 'credit_union';  //mysql database to connect to
const MEMBERS_TABLE           = 'Members';       //mysql table for member info
const ACCOUNTS_TABLE          = 'Accounts';      //mysql table for account info
const MEMBER_EMAIL_FIELD      = 'email';         //mysql field for member email
const MEMBER_NUMBER_FIELD     = 'memberNumber';  //mysql field for member number
const ACCOUNT_BALANCE_FIELD   = 'balance';       //mysql field for account balance
const ACCOUNT_NUMBER_FIELD    = 'accountNumber';  //mysql field for account number


class Member {
    private $mysqlConnection;
    /*
     * @desc Initialize the member class, setup the mysql connection
     */
    public function __construct () {
        $this->mysqlConnection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        if (!$this->mysqlConnection) {
            die('Could not connect to mysql: '.mysql_error());
        }
        mysql_select_db(DB_DATABASE, $this->mysqlConnection);
    }

    /*
     * @desc Save a new member to the Members table
     * @param $memberSpec array - associative array of a member to be created
     * Example memberSpec:
     * $memberSpec = array(
     *   'firstName' => 'John',
     *   'lastName'  => 'Smith',
     *   'phone'     => '555-5550',
     *   'email'     => 'john@smith.com'
     * );
     * @returns boolean - true if member record is successfully saved, false
     *                    otherwise
     */
    public function addMember( $memberSpec ) {
        $keys = array();
        $values = array();
        foreach ($memberSpec as $key => $val) {
            $keys []= mysql_real_escape_string($key);
            $values []= "'".mysql_real_escape_string($val)."'";
        }

        $query = "INSERT INTO ".MEMBERS_TABLE." (".implode(',', $keys).") VALUES (".implode(',', $values).")";
        $result = mysql_query($query, $this->mysqlConnection);
        if (!$result) {
            echo "\n".mysql_error()."\n";
        }
        return $result;
    }

    /*
     * @desc Find a Member record in the Members table
     * @param $email string - email address of the member to lookup
     * @returns array - associative array of Members record, false if none
     *                  exists
     */
    public function getMemberByEmail( $email ) {
        $query = "SELECT * FROM ".MEMBERS_TABLE.
                 " WHERE ".MEMBER_EMAIL_FIELD."='".mysql_real_escape_string($email)."' LIMIT 1";
        $result = mysql_query($query, $this->mysqlConnection);
        if (!$result) {
            echo "\n".mysql_error()."\n";
            return false;
        }
        return mysql_fetch_array($result, MYSQL_ASSOC);
    }

    /*
     * @desc Get the Members record for the given member number
     * @param $memberNumber integer - should be an existing Member's unique
     *                                member number
     * @return array - associative array of the Members record
     */
    public function getMemberByNumber( $memberNumber ) {
        $memberNumber = intval($memberNumber);
        $search = "SELECT * FROM ".MEMBERS_TABLE.
                  " WHERE ".MEMBER_NUMBER_FIELD."=".$memberNumber." LIMIT 1";
        $searchResult = mysql_query($search, $this->mysqlConnection);
        if (!$searchResult) {
            return false;
        }
        $result = mysql_fetch_array($searchResult);
        return $result;
    }
}



class Account {
    private $mysqlConnection;
    /*
     * @desc Initialize the member class, setup the mysql connection
     */
    public function __construct () {
        $this->mysqlConnection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        if (!$this->mysqlConnection) {
            die('Could not connect to mysql: '.mysql_error());
        }
        mysql_select_db(DB_DATABASE, $this->mysqlConnection);
    }

    /*
     * @desc Add a new record to the Accounts table for the given member number
     * @param $memberNumber integer - should be an existing Member’s unique
     *                                member number
     * @return boolean - true if account was successfully added, false if not
     */
    public function addAccount( $memberNumber ) {
        $member = new Member();
        $memberAccount = $member->getMemberByNumber($memberNumber);
        if (!$memberAccount) {
            return false;
        }

        $insert = "INSERT INTO ".ACCOUNTS_TABLE." ( memberNumber, balance) VALUES (".$memberNumber.", 0)";
        $result = mysql_query($insert, $this->mysqlConnection);
        if (!$result) {
            echo "\n".mysql_error()."\n";
        }
        return $result;
    }

    /*
     * @desc Get the Accounts record for the given member number
     * @param $memberNumber integer - should be an existing Member's unique
     *                                member number
     * @return array - associative array of the Accounts record
     */
    public function getAccount( $memberNumber ) {
        $memberNumber = intval($memberNumber);
        $search = "SELECT * FROM ".ACCOUNTS_TABLE." WHERE ".MEMBER_NUMBER_FIELD."=".$memberNumber." LIMIT 1";
        $searchResult = mysql_query($search, $this->mysqlConnection);
        if (!$searchResult) {
            return false;
        }
        $result = mysql_fetch_array($searchResult);
        return $result;
    }
}




class Credit_Union {
    //name is a string set at the time a Credit_Union initialized
    private $name;
    private $mysqlConnection;

    /*
     * @desc Initialize the member class, setup the mysql connection
     */
    public function __construct ($name) {
        $this->name = $name;
        $this->mysqlConnection = mysql_connect(DB_HOST, DB_USER, DB_PASS);
        if (!$this->mysqlConnection) {
            die('Could not connect to mysql: '.mysql_error());
        }
        mysql_select_db(DB_DATABASE, $this->mysqlConnection);
    }

    /*
     * @desc Returns the name set in the constructor
     * @return string - name of the credit union
     */
    public function getName() {
        return $this->name;
    }

    /*
     * @desc Get the total number of members associated with the credit union
     * @return integer - number of members
     */
    public function getMemberCount() {
        $query = "SELECT COUNT(".MEMBER_NUMBER_FIELD.") FROM ".MEMBERS_TABLE;
        $result = mysql_query($query, $this->mysqlConnection);
        if (!$result) {
            return false;
        }
        $result = mysql_fetch_array($result, MYSQL_NUM);
        if (!$result) {
            return false;
        }
        return $result[0];
    }

    /*
     * @desc Get the total sum of account balances with the credit union
     * @return money - total sum of account balances
     */
    public function getTotalDeposits() {
        $query = "SELECT SUM(".ACCOUNT_BALANCE_FIELD.") FROM ".ACCOUNTS_TABLE;
        $result = mysql_query($query, $this->mysqlConnection);
        if (!$result) {
            return false;
        }
        $result = mysql_fetch_array($result, MYSQL_NUM);
        if (!$result) {
            return false;
        }
        return $result[0];
    }

    /*
     * @desc Add an amount of money to an account balance
     * @return boolean - true if money is successfully added, false otherwise
     */
    public function deposit( $memberNumber, $accountNumber, $depositAmount ) {
        $update = "UPDATE ".ACCOUNTS_TABLE.
                  " SET ".ACCOUNT_BALANCE_FIELD."=".ACCOUNT_BALANCE_FIELD."+".floatval($depositAmount).
                  " WHERE ".MEMBER_NUMBER_FIELD."=".$memberNumber." AND ".ACCOUNT_NUMBER_FIELD."=".$accountNumber;
        $result = mysql_query($update, $this->mysqlConnection);
        return $result;
    }
}
