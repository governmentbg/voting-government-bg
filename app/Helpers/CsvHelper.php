<?php

if (!function_exists('csv_to_array')) {
    function csv_to_array($filename='', $delimiter=',')
    {
        if(!file_exists($filename) || !is_readable($filename))
            return FALSE;

        $data = array();
        if (($handle = fopen($filename, 'r')) !== FALSE)
        {
            while (($row = fgetcsv($handle, 10000, $delimiter)) !== FALSE)
            {
                $data[] =  $row;
            }
            fclose($handle);
        }
        return $data;
    }
}

