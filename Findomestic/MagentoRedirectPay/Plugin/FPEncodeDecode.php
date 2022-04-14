<?php
namespace Findomestic\MagentoRedirectPay\Plugin;

class FPEncodeDecode
{
    public static function unencode($encoded)
    {

        if ($encoded == null || strlen($encoded) == 0) {
            return false;
        }

        $args = [];
        $exploded = explode('-', $encoded);

        if (count($exploded) == 0) {
            return false;
        }

        foreach ($exploded as $argument) {
            $data = explode(':', $argument);
            $args[$data[0]] = $data[1];
        }

        return $args;
    }


    /**
     * @param $args
     * @return false|string
     *
     * encode the return args that will be handled by the endpoint (maybe move to the module class)
     */
    public static function encodeUrlArgs($args)
    {
        if (count($args) == 0) {
            return false;
        }
        $string = '';
        foreach ($args as $key => $value) {
            if ($string != '') {
                $string .= '-';
            }
            $string .= $key . ':' . $value;
        }

        return $string;
    }
}
