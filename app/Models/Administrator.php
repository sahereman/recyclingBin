<?php

namespace App\Models;

use Encore\Admin\Auth\Database\Administrator as AdministratorModel;

class Administrator extends AdministratorModel
{

    public function boxes()
    {
        return $this->belongsToMany(Box::class, 'box_admin_users', 'admin_user_id');
    }

}
