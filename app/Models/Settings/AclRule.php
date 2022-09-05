<?php
declare(strict_types=1);

namespace App\Models\Settings;

use Illuminate\Database\Eloquent\Model;

class AclRule extends model
{
    protected $table = 'acl_rules';

    protected $fillable = ['rule_name', 'rule_parent_id', 'is_enable', 'label', 'description'];

    public function childrens()
    {
        return $this->hasMany($this, 'rule_parent_id');
    }
}
