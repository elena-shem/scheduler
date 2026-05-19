<?php

namespace Util;


class Dateformatter
{
    
    /* Used to reformat dates from client side to MySQL dates
     * Input:  date in the format "dd/mm/yyyy"
     * Output: date in the format "yyyy-mm-dd"
     */
    public static function clientToServer($date)
    {
        return implode('-', array_reverse(explode('/', $date)));
    }
    
    /* Used to reformat MySQL dates to dates for client use
     * Input:  date in the format "yyyy-mm-dd"
     * Output: date in the format "dd/mm/yyyy"
     */
    public static function serverToclient($date)
    {
        return implode('/', array_reverse(explode('-', $date)));
    }
    
}

