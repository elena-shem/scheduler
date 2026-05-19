<?php

namespace Util;


class Telephone
{
    
    public static function beautify($number)
    {
        $number = preg_replace('/\s+/', '', $number);
        
        if(substr($number, 0, 3) == "+30" && strlen($number) == 13)
        {
            return \Num::format_phone("0030".substr($number, 3), "(0000) 000 000 0000");
        }
        else if(substr($number, 0, 4) == "0030" && strlen($number) == 14)
        {
            return \Num::format_phone($number, "(0000) 000 000 0000");
        }
        else
        {
            return $number;
        }
    }
    
}

