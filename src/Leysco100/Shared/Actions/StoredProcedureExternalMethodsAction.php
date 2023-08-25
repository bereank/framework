<?php

namespace Leysco100\Shared\Actions;

class StoredProcedureExternalMethodsAction
{
  public static function ValidateMobileNumber($phone)
  {

    $phoneRegex = '/^\+\d{1,3}\d{9}$/';

    return preg_match($phoneRegex, $phone);
  }

  public static function validateEmail($email)
  {
    // A regular expression to match the email format
    $emailRegex = "/^[^\s@]+@[^\s@]+\.[^\s@]+$/";
    if (strtolower($email) === "n/a" || strtolower($email) === "n\a") {
      return true;
    } else {
      // Return true if the email matches the regex, false otherwise
      return preg_match($emailRegex, $email);
    }
  }

  public static function validateName($name)
  {
    // A regular expression to match the email format
    //      $nameRegex = "/^[a-zA-Z]+\s[a-zA-Z]+$/";
    $nameRegex = "/^([a-zA-Z'.]{3,}[ ]{1,})+[a-zA-Z '.]*$/";
    // Return true if the email matches the regex, false otherwise
    return preg_match($nameRegex, $name);
  }
}
