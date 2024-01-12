<?php

namespace App\Models\Sodadmin;

use Illuminate\Database\Eloquent\Model;

class AdminModel extends Model {
    
    function setData(array $data) {
        foreach ($data as $key => $value) {
            if ($value == 0 || !empty($value)) {
                $this->$key = $value;
            }
        }
        return $this;
    }
}
?>