<?php
class Validator {
    // Stores feedback used in the feedback-squares on pages with forms
    public $feedback = array();

    // Flag for checking whether the input provided is valid
    public $valid = true;

    function __construct(){

    }

    function validatorReset() {
        $this->valid = true;
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

    function validatePasswordNew($currentPassword, $newPassword, $confirmNewPassword, $hashedPassword) {
        if($newPassword == $confirmNewPassword) {
            if(password_verify($currentPassword, $hashedPassword)) {
                if($currentPassword != $newPassword) {
                    $this->validatePasswordRegister($newPassword);
                } else {
                    array_push($this->feedback, 'Your new password can not be the same as your current password.<br>');
                    $this->valid = false;
                    return;
                }
            } else {
                array_push($this->feedback, 'Your current password is incorrect.<br>');
                $this->valid = false;
                return;
            }
        } else {
            array_push($this->feedback, 'New password and confirm password do not match.<br>');
            $this->valid = false;
            return;
        }
    }

    // Validates names, sets $valid to false if conditions aren't met. $name is the name checked, $nameType is the type of name (eg. first or last)
    function validateName($name, $nameType) {
        if (!empty($name)) {
            if (!preg_match("/^(?!\s+$)[\p{L}'\s-]+$/u", $name)) {
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

    // Validates phone number input, sets $valid to false if conditions aren't met
    function validateTelephone($phoneNumber) {
        if (!preg_match("/^\+?\d*$/", $phoneNumber)) {
            array_push($this->feedback, 'Phone number can only consist of numbers and a preceeding +.<br>');
            $this->valid = false;
            return;
        } else if (strlen($phoneNumber) > 25) {
            array_push($this->feedback, 'Phone number can not exceed 25 characters.<br>');
            $this->valid = false;
            return;
        }
    }

    /*
        Used to validate a users birthday, by setting valid to false if the format is wrong or date is after today
        TODO: Refactor to a generic validateDate-method 
    */
    function validateBirthday($date) {
        if ($date == '') {
            // If the field is empty, it's valid as the user hasn't entered a date or wants to remove it. Just return.
            return;
        } else if (!preg_match("/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/", $date)) {
            // If the date doesn't follow the format YYYY-MM-DD, it's not valid. Flag false and return. 
            // (Certain dates are unaccounted for, like february 31st, which doesn't exist)
            array_push($this->feedback, 'Date is not in a valid format.<br>');
            $this->valid = false;
            return;
        } else if (strtotime($date) > time()) {
            // If the date is after today, flag false and return.
            array_push($this->feedback, 'Date cannot be after today.<br>');
            $this->valid = false;
            return;
        }
    }

    /*
        Used to validate the length of generic fields, that have no specific criteria and are optional to enter
        $fieldValue is the input to be checked, $fieldName is the name of the field (e.g. Location), and $maxLength is the max amount of characters it can be.
        Sets the valid flag to false if $fieldValue is longer than the $maxLength
    */
    function validateGenericField($fieldValue, $fieldName, $maxLength) {
        if (strlen($fieldValue) > $maxLength) {
            array_push($this->feedback, $fieldName . ' can not exceed ' . $maxLength . ' characters.<br>');
            $this->valid = false;
            return;
        }
    }

    // Validates location field, flags valid to false if it exceeds 40 characters
    function validateLocation($location) {
        $this->validateGenericField($location, 'Location', 40);
    }

    // Validates competence field, flags valid to false if it exceeds 5000 characters
    function validateCompetence($competence) {
        $this->validateGenericField($competence, 'Competence', 5000);
    }

    // Validates a company name, and sets valid to false if it's empty or above 100 characters aren't met
    function validateCompanyName($name) {
        if (!empty($name)) {
            if(strlen($name) <= 100) {
                // This can be extended to further change the criteria for name validation
                return;
            } else {
                array_push($this->feedback, 'Company name can not be over 100 characters.<br>');
                $this->valid = false;
                return;
            }
        } else {
            array_push($this->feedback, 'Company name can not be empty.<br>');
            $this->valid = false;
            return;
        }
    }

    // Validates a company description, flags valid to false if empty or above 500 characters
    function validateCompanyDescription($description) {
        if(strlen($description) <= 500) {
            // This can be extended to further change the criteria for description validation
            return;
        } else {
            array_push($this->feedback, 'Company description can not be over 500 characters.<br>');
            $this->valid = false;
            return;
        }
    }
}
?>