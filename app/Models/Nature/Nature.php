<?php

namespace App\Models\Nature;

use Illuminate\Database\Eloquent\Model;

class Nature extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['description', 'cfop_state', 'cfop_state_st', 'cfop_no_state', 'cfop_no_state_st', 'customer_type', 'cod_user_reg', 'cod_user_alt'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
    /**
     * Update information in the clients database
     *
     * @param array $nature
     * @param int $codNature
     * @return boolean
     */
    public function edit(array $nature, int $codNature)
    {
        return $this->where('id', $codNature)->update($nature);
    }
}
