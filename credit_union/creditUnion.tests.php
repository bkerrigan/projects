<?php

class BaseTest {
    private static $_tests  = 0;
    private static $_passed = 0;
    private static $_failed = 0;

    public function eq( $value, $expected, $msg ) {
        self::$_tests++;
        if( $value == $expected ) {
            self::$_passed++;
            echo "PASSED: {$msg}\n";
        } else {
            self::$_failed++;
            $vStr = var_export( $value, true );
            $eStr = var_export( $expected, true );
            echo "FAILED: {$msg} Expected: {$eStr} Got: {$vStr}\n";
        }
    }

    public function aaEq( $value, $expected, $msg ) {
        self::$_tests++;
        $errors = array();
        foreach( $expected as $k => $v ) {
            if( $value[$k] != $v ) {
                $errors[] = "{$k} Expected: {$v} Got: {$value[$k]}";
            }
        }

        if( count( $errors ) > 0 ) {
            self::$_failed++;
            $errStr = implode( ', ', $errors );
            echo "FAILED: {$msg} {$errStr}\n";
        } else {
            self::$_passed++;
            echo "PASSED: {$msg}\n";
        }
    }

    public function report() {
        $tests  = self::$_tests;
        $passed = self::$_passed;
        $failed = self::$_failed;
        echo "\nRan {$tests} tests:\n";
        echo "Passed: {$passed} Failed: {$failed}\n";
    }
}

class TestMember extends BaseTest {
    private static $_spec = array(
        'firstName' => 'Friedrich',
        'lastName'  => 'Raiffeisen',
        'phone'     => '1-650-555-5555',
        'email'     => 'f.r@example.com'
    );
    public function test() {
        $m = new Member();
        $spec = self::$_spec;

        $r = $m->addMember( $spec );
        $this->eq( $r, true, "Add Member" );

        $i = $m->getMemberByEmail( $spec['email'] );
        $this->aaEq( $i, $spec, "Get Member" );

        $j = $m->getMemberByEmail( $spec['firstName'] );
        $this->eq( $j, false, "Non-member Get Member" );
    }
}

class TestAccount extends BaseTest {
    private static $_spec = array(
        'firstName' => 'Friedrich',
        'lastName'  => 'Raiffeisen',
        'phone'     => '1-650-555-5555',
        'email'     => 'f.r@example.com'
    );

    private $_member;

    public function __construct() {
        $spec = self::$_spec;

        $m = new Member();
        $this->_member = $m->getMemberByEmail( $spec['email'] );
    }

    public function test() {
        $a = new Account();

        $r = $a->addAccount( $this->_member['memberNumber'] );
        $this->eq( $r, true, "Add Account" );

        $r = $a->addAccount( 'a' );
        $this->eq( $r, false, "Fail Account" );

        $r = $a->getAccount( $this->_member['memberNumber'] );
        $this->eq( $this->_member['memberNumber'], $r['memberNumber'], "Get Account" );
    }
}

class TestCredit_Union extends BaseTest {
    private static $_spec = array(
        'firstName' => 'Friedrich',
        'lastName'  => 'Raiffeisen',
        'phone'     => '1-650-555-5555',
        'email'     => 'f.r@example.com'
    );
    private static $_name = 'Low Budget Credit Union';
    private static $_deposit = 20.99;

    private $_member;
    private $_account;

    public function __construct() {
        $spec = self::$_spec;

        $m = new Member();
        $this->_member = $m->getMemberByEmail( $spec['email'] );

        $a = new Account();
        $this->_account = $a->getAccount( $this->_member['memberNumber'] );
    }

    public function test() {
        $c = new Credit_Union( self::$_name );

        $this->eq( $c->getName(), self::$_name, "Credit Union name" );

        $this->eq( $c->getMemberCount(), 1, "Credit Union member count" );

        $total = $c->getTotalDeposits();
        $mn = $this->_account['memberNumber'];
        $an = $this->_account['accountNumber'];
        $r = $c->deposit( $mn, $an, self::$_deposit );
        $this->eq( $r, true, "Credit Union valid deposit" );

        $expectedTotal = $total + self::$_deposit;
        $newTotal = $c->getTotalDeposits();
        $this->eq( $newTotal, $expectedTotal, "Credit Union deposit" );

        $r = $c->deposit( 'a', 'a', self::$_deposit );
        $this->eq( $r, false, "Credit Union invalid deposit" );

        $newTotal = $c->getTotalDeposits();
        $this->eq( $newTotal, $expectedTotal, "Credit Union deposit" );
    }
}


