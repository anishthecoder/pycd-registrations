<?php

/**
 * This file contains various helper functions that will be used throughout
 * the application.
 */


/**
 * @param String $fullname
 * @return boolean TRUE if the name is of the format "firstname mi. lastname"
 *					with the middle initial being options. FALSE otherwise.
 */
function is_fullname_valid($fullname){
    /* Validate fullname */
    $regex = "/^(?:[A-Za-z.-]+(?:\s+|$)){2,3}$/";
    if (preg_match($regex, $fullname) != 1) return FALSE;
		else return TRUE;
}

/**
 * @param String $email
 * @return boolean TRUE if the given email is valid. FALSE otherwise.
 */
function is_email_valid($email){
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}


function is_string_pure($string){
	$regex = "/^[a-zA-Z ]+$/";
    if (preg_match($regex, $string) != 1) return FALSE;
		else return TRUE;
}