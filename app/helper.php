<?php

if (!function_exists('getOddFirstOrderClass')) {
    function getOddFirstOrderClass($key) {
        if($key%2 != 0){
            return 'order-lg-2';
        }
    }
}

if (!function_exists('getOddSecondOrderClass')) {
    function getOddSecondOrderClass($key) {
        if($key%2 != 0){
            return 'order-lg-1';
        }
    }
}

if (!function_exists('getFavoriteCount')) {
    function getFavoriteCount() {
        $favorites = session()->get('favorites', []);
        return count($favorites);
    }
}

if (!function_exists('getFavorites')) {
    function getFavorites() {
        $favorites = session()->get('favorites', []);
        return $favorites;
    }
}

?>