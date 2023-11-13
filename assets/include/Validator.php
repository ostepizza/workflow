<?php
class Validator {
    // Stores feedback used in the feedback-squares on pages with forms
    public $feedback = array();

    // Flag for checking whether the input provided is valid
    public $valid = true;

    function __construct(){

    }

    // Returns all feedback as a string
    function printAllFeedback() {
        return implode($this->feedback);
    }

    // Used to validate all form inputs on the registration page
    function validateRegistration($tosCheckmark, $email, $password, $firstName, $lastName) {
        $this->validateTosCheckmark($tosCheckmark);
        $this->validateEmail($email);
        $this->validatePasswordRegister($password);
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
    }

    // Used to validate login-form inputs
    function validateLogin($email, $password) {
        $this->validateEmail($email);
        $this->validatePasswordLogin($password);
    }

    // Used to validate the tos checkmark (can probably be changed to work with any checkmark)
    function validateTosCheckmark($tosCheckmark) {
        if (!$tosCheckmark) {
            array_push($this->feedback, 'You need to accept the terms & conditions.<br>');
            $this->valid = false;
        }
    }

    // Validates email input, sets $valid to false if conditions aren't met
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

    // Validates password input for login password, sets $valid to false if empty
    function validatePasswordLogin($password) {
        if (empty($password)) {
            array_push($this->feedback, 'You need to enter a password.<br>');
            $this->valid = false;
            return;
        }
    }

    // Validates password input for registering/changing user password, sets $valid to false if all conditions aren't met
    function validatePasswordRegister($password) {
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

    // Validates names, sets $valid to false if conditions aren't met. $name is the name checked, $nameType is the type of name (eg. first or last)
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

    // Validates first-name input, sets $valid to false if conditions aren't met
    function validateFirstName($firstName) {
        $this->validateName($firstName, 'first');
    }

    // Validates last-name input, sets $valid to false if conditions aren't met
    function validateLastName($lastName) {
        $this->validateName($lastName, 'last');
    }
}
?>