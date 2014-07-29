<?php

class M
{
    public static $leader_position_std = 'соответствует стандарту';
    public static $leader_position_not_std = 'не соответствует формату. Возможные значения : ';
    public static $filed_not_repeated = 'не соответствует формату. Возможные значения : ';

    public static function _() {
        $args = func_get_args();
        $format = array_shift($args);
        return vsprintf($format, $args);
    }
}
