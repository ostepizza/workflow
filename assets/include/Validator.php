<?php
class Validator {
    // Stores feedback used in the feedback-squares on pages with forms
    public $feedback = array();

    // Flag for checking whether the input provided is valid
    public $valid = true;

    /**
     * Resets the validator, so that it can be used again
     * @return void
     */
    function validatorReset() {
        $this->valid = true;
    }

    /**
     * Returns all feedback stored in the feedback array as a string
     * @return string All feedback stored in the feedback array
     */
    function printAllFeedback() {
        return implode($this->feedback);
    }

    /**
     * Validates all inputs on the registration page
     * @param bool $tosCheckmark Whether the user has checked the terms & conditions checkbox
     * @param string $email The email the user has entered
     * @param string $password The password the user has entered
     * @param string $confirmPassword The password the user has entered in the confirm password field
     * @param string $firstName The first name the user has entered
     * @param string $lastName The last name the user has entered
     */
    function validateRegistration($tosCheckmark, $email, $password, $confirmPassword, $firstName, $lastName) {
        $this->validateTosCheckmark($tosCheckmark);
        $this->validateEmail($email);
        $this->validatePasswordRegister($password);
        $this->validatePasswordConfirm($password, $confirmPassword);
        $this->validateFirstName($firstName);
        $this->validateLastName($lastName);
    }

    /**
     * Validates all inputs on the login page
     * @param string $email The email the user has entered
     * @param string $password The password the user has entered
     */
    function validateLogin($email, $password) {
        $this->validateEmail($email);
        $this->validatePasswordLogin($password);
    }

    /**
     * Validates the ToS-checkmark on the registration page
     * (Should be refactored to a generic validateCheckbox-method)
     * @param bool $tosCheckmark Whether the user has checked the terms & conditions checkbox
     */
    function validateTosCheckmark($tosCheckmark) {
        if (!$tosCheckmark) {
            array_push($this->feedback, 'You need to accept the terms & conditions.<br>');
            $this->valid = false;
        }
    }

    /**
     * Validates an email input, sets $valid to false if conditions aren't met
     * @param string $email The email to be validated
     */
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

    /**
     * Validates a password input, sets $valid to false if conditions aren't met
     * @param string $password The password to be validated
     */
    function validatePasswordLogin($password) {
        if (empty($password)) {
            array_push($this->feedback, 'You need to enter a password.<br>');
            $this->valid = false;
            return;
        }
    }

    /**
     * Validates a password input, sets $valid to false if conditions aren't met
     * Despite the name, this method is used for both registering and changing a users password
     * @param string $password The password to be validated
     */
    function validatePasswordRegister($password) {
        if (!empty($password)) {
            if(!preg_match('@[0-9]@', $password)) {
                array_push($this->feedback, 'Password does not contain a number.<br>');
                $this->valid = false;
            }
            if (!preg_match('@[^\w]@', $password)) {
                array_push($this->feedback, 'Password does not contain a special character.<br>');
                $this->valid = false;
            }
            if (strlen($password) < 8) {
                array_push($this->feedback, 'Password is not at least 8 characters long.<br>');
                $this->valid = false;
            }
            return;
        } else {
            array_push($this->feedback, 'You need to enter a password.<br>');
            $this->valid = false;
            return;
        }
    }

    /**
     * Validates a password input, sets $valid to false if conditions aren't met
     * @param string $currentPassword The current password the user has entered
     * @param string $newPassword The new password the user has entered
     * @param string $confirmNewPassword The new password the user has entered in the confirm password field
     * @param string $hashedPassword The hashed password from the database
     */
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

    /**
     * Validates a password input, sets $valid to false if conditions aren't met
     * @param string $password The password to be validated
     * @param string $confirmPassword The password to be validated
     */
    function validatePasswordConfirm($password, $confirmPassword) {
        if ($password != $confirmPassword) {
            array_push($this->feedback, 'Password and confirm password do not match.<br>');
            $this->valid = false;
            return;
        }
    }

    /**
     * Validates a name input, sets $valid to false if conditions aren't met
     * @param string $name The name to be validated
     * @param string $nameType The type of name (eg. first or last)
     */
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

    /**
     * Validates a first name input, sets $valid to false if conditions aren't met
     * @param string $firstName The first name to be validated
     */
    function validateFirstName($firstName) {
        $this->validateName($firstName, 'first');
    }

    /**
     * Validates a last name input, sets $valid to false if conditions aren't met
     * @param string $lastName The last name to be validated
     */
    function validateLastName($lastName) {
        $this->validateName($lastName, 'last');
    }

    /**
     * Validates a phone number input, sets $valid to false if conditions aren't met
     * @param string $phoneNumber The phone number to be validated
     */
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

    /**
     * Validates a birthday input, sets $valid to false if conditions aren't met
     * (Should be refactored to a generic validateDate-method)
     * @param string $date The date to be validated
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

    /**
     * Validates a generic field based on maximum length, flags valid to false if $fieldValue is longer than the $maxLength
     * @param string $fieldValue The value to be validated (e.g. Copenhagen)
     * @param string $fieldName The name of the field to be validated (e.g. Location)
     * @param int $maxLength The max length of the field (e.g. 40)
     */
    function validateGenericField($fieldValue, $fieldName, $maxLength) {
        if (strlen($fieldValue) > $maxLength) {
            array_push($this->feedback, $fieldName . ' can not exceed ' . $maxLength . ' characters.<br>');
            $this->valid = false;
            return;
        }
    }

    /**
     * Validates a generic field based on minimum length, flags valid to false if $fieldValue is shorter than the $minLength
     * @param string $fieldValue The value to be validated (e.g. Copenhagen)
     * @param string $fieldName The name of the field to be validated (e.g. Location)
     * @param int $minLength The min length of the field (e.g. 3)
     */
    function validateGenericFieldMinChar($fieldValue, $fieldName, $minLength) {
        if (strlen($fieldValue) < $minLength) {
            array_push($this->feedback, $fieldName . ' must be at least ' . $minLength . ' characters.<br>');
            $this->valid = false;
            return;
        }
    }

    /**
     * Validates a location input, sets $valid to false if it exceeds 40 characters
     * @param string $location The location to be validated
     */
    function validateLocation($location) {
        $this->validateGenericField($location, 'Location', 40);
    }

    /**
     * Validates a competence input, sets $valid to false if it exceeds 5000 characters
     * @param string $competence The competence to be validated
     */
    function validateCompetence($competence) {
        $this->validateGenericField($competence, 'Competence', 5000);
    }

    /**
     * Validates a search input, sets $valid to false if it exceeds 100 characters
     * @param string $search The search to be validated
     */
    function validateSearch($search) {
        $this->validateGenericField($search, 'Search', 100);
    }

    /**
     * Validates a search input, sets $valid to false if it is less than 3 characters
     * @param string $search The search to be validated
     */
    function validateSearchMinChar($search) {
        $this->validateGenericFieldMinChar($search, 'Search', 3);
    }

    /**
     * Validates a job listing title, and sets valid to false if it exceeds 200 characters
     * @param string $title The title to be validated
     */
    function validateJobListingTitle($title) {
        $this->validateGenericField($title, 'Title', 200);
    }

    /**
     * Validates a job listing description, and sets valid to false if it exceeds 5000 characters
     * @param string $description The description to be validated
     */
    function validateJobListingDescription($description) {
        $this->validateGenericField($description, 'Description', 5000);
    }

    /**
     * Validates a job application title, and sets valid to false if it exceeds 200 characters
     * @param string $title The title to be validated
     */
    function validateJobApplicationTitle($title) {
        $this->validateGenericField($title, 'Title', 200);
    }

    /**
     * Validates a job application description, and sets valid to false if it exceeds 5000 characters
     * @param string $description The description to be validated
     */
    function validateJobApplicationDescription($description) {
        $this->validateGenericField($description, 'Description', 5000);
    }

    /**
     * Validates a job listing deadline, and sets valid to false if something is wrong
     * (Should be refactored to a generic validateDate-method, together with validateBirthday())
     * @param string $date The date to be validated
     */
    function validateJobListingDeadline($date) {
        if ($date == '') {
            // If the field is empty, it's valid as the user hasn't entered a date or wants to remove it. Just return.
            return;
        } else if (!preg_match("/^\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])$/", $date)) {
            // If the date doesn't follow the format YYYY-MM-DD, it's not valid. Flag false and return. 
            // (Certain dates are unaccounted for, like february 31st, which doesn't exist)
            array_push($this->feedback, 'Date is not in a valid format.<br>');
            $this->valid = false;
            return;
        } else if (strtotime($date) < strtotime(date('Y-m-d'))  ) {
            // If the date is after today, flag false and return.
            array_push($this->feedback, 'Date can not be before today.<br>');
            $this->valid = false;
            return;
        }
    }

    /**
     * Validates a company name, and sets valid to false if it's empty or above 100 characters aren't met
     * @param string $name The name to be validated
     */
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

    /**
     * Validates a company description, and sets valid to false if it's empty or above 500 characters aren't met
     * @param string $description The description to be validated
     */
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