<?php

namespace RFC\Component\Validator;

class IntegerValidator
{
    public function validate($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT) === false) {
            // contraint
            // echo "Variable %s is not an integer";
        }
    }
}