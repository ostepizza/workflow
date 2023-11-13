<?php
class Validator {
    public $feedback = array();
    public $valid = true;

    function __construct(){

    }

    function printAllFeedback() {
        /*foreach ($this->feedback as $fb) {
            echo $fb;
        }*/
        return implode($this->feedback);
    }

    function validateRegistration($tosCheckmark, $email, $password, $firstName, $lastName) {
        $this->validateTosCheckmark($tosCheckmark);
        $this->validateEmail($email);
        $this->validatePassword($password);
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
    }

    function validateTosCheckmark($tosCheckmark) {
        if (!$tosCheckmark) {
            array_push($this->feedback, 'You need to accept the terms & conditions.<br>');
            $this->valid = false;
        }
    }

    function validateEmail($email) {
        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                array_push($this->feedback, $email . ' is not a valid email.<br>');
                $this->valid = false;
                return;
            }
        } else {
            array_push($this->feedback, 'You need to enter an email.<br>');
            $this->valid = false;
            return;
        }
    }

    function validatePassword($password) {
        if (!empty($password)) {
            if(!preg_match('@[0-9]@', $password)) {
                array_push($this->feedback, 'Password does not contain a number.<br>');
                $this->valid = false;
                return;
            }
            if (!preg_match('@[^\w]@', $password)) {
                array_push($this->feedback, 'Password does not contain a special character.<br>');
                $this->valid = false;
                return;
            }
            if (strlen($password) < 8) {
                array_push($this->feedback, 'Password is not at least 8 characters long.<br>');
                $this->valid = false;
                return;
            }
        } else {
            array_push($this->feedback, 'You need to enter a password.<br>');
            $this->valid = false;
            return;
        }
    }

    function validateName($name, $nameType) {
        if (!empty($name)) {
            if (!preg_match("/^[a-zA-Z-' ]*$/", $name)) {
                array_push($this->feedback, 'Only letters and white space allowed in ' . $nameType . ' name "' . $name . '".<br>');
                $this->valid = false;
                return;
            }
        } else {
            array_push($this->feedback, 'You need to enter a ' . $nameType . ' name.<br>');
            $this->valid = false;
            return;
        }
    }

    function validateFirstName($firstName) {
        $this->validateName($firstName, 'first');
    }

    function validateLastName($lastName) {
        $this->validateName($lastName, 'last');
    }
}
?>