<?php
/*=============================================================================
 *
 * This function will format the phone number field to look pretty ;-)
 * 
 *===========================================================================*/
function formatPhoneNumber($row, $Phone)
{
  if (!is_null($row)) {
    switch ($row['PrimaryContact']) {
      case "Home":
        $Phone = $row['HomePhone'];
        break;
      case "Mobile":
        $Phone = $row['MobilePhone'];
        break;
      case "Work":
        $Phone = $row['WorkPhone'];
        break;
      default:
        $Phone = "None";
        break;
    }
  }

  $phoneNumber = preg_replace('/[^0-9]/', '', $Phone);

  if (strlen($phoneNumber) > 10) {
    $countryCode = substr($phoneNumber, 0, strlen($phoneNumber) - 10);
    $areaCode = substr($phoneNumber, -10, 3);
    $nextThree = substr($phoneNumber, -7, 3);
    $lastFour = substr($phoneNumber, -4, 4);

    $phoneNumber = '+' . $countryCode . ' (' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
  } else if (strlen($phoneNumber) == 10) {
    $areaCode = substr($phoneNumber, 0, 3);
    $nextThree = substr($phoneNumber, 3, 3);
    $lastFour = substr($phoneNumber, 6, 4);

    $phoneNumber = '(' . $areaCode . ') ' . $nextThree . '-' . $lastFour;
  } else if (strlen($phoneNumber) == 7) {
    $nextThree = substr($phoneNumber, 0, 3);
    $lastFour = substr($phoneNumber, 3, 4);

    $phoneNumber = $nextThree . '-' . $lastFour;
  }
  return $phoneNumber;
}
/*=============================================================================
 *
 * This function will format the zip codefield to look pretty ;-)
 * 
 *===========================================================================*/
function formatZipCode(&$Zip)
{
  $Zip = preg_replace('/[^0-9]/', '', $Zip);

  if (strlen($Zip) > 5) {
    $ZipCode = substr($Zip, 0, strlen($Zip) - 4);
    $ZpCode4 = substr($Zip, -4, 4);

    $Zip = $ZipCode . '-' . $ZpCode4;
  }
  return $Zip;
}
/*=============================================================================
 *
 * This function will format the zip codefield to look pretty ;-)
 * 
 *===========================================================================*/
function formatEmail($Email)
{

  $str = "<a href='mailto:" . strtolower($Email) . "?subject=Merit Badge Counselor'>" . strtolower($Email) . "</a>";

  return $str;
}
/*=============================================================================
 *
 * This function will return left length of a string
 * 
 *===========================================================================*/
function left($str, $length)
{
  return substr($str, 0, $length);
}
/*=============================================================================
 *
 * This function will return mid length of a string
 * 
 *===========================================================================*/
function mid($str, $start, $length)
{
  return substr($str, $start, $length);
}
/*=============================================================================
 *
 * This function will return right length of a string
 * 
 *===========================================================================*/
function right($str, $length)
{
  return substr($str, -$length);
}
