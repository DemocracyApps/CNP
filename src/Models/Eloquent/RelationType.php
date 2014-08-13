<?php
namespace DemocracyApps\CNP\Models\Eloquent;

class RelationType extends \Eloquent {

    /**
     * name           REQUIRED, must be unique
     * @property string
     *
     * allowedfrom    See note below, blank if any allowed
     * @property string
     *
     * allowedto      See note below, blank if any allowed
     * @property string
     *
     * inverse        If blank, relation is its own inverse,
     *                  otherwise id of inverse relation
     * @property integer
     *
     * NOTE:
     *   The allowedfrom/allowedto parameters are string versions of lists
     *   of scapes/denizentypes that the relation is allowed to come from/go to.
     *   I think the format should be a comma-separated list of the form S[:D],
     *   where S is a scape id and D is a denizen type id from that scape.
     *   When we actually start verifying this, we may want to convert to arrays
     *   here internally
     */

	protected $table = 'relation_types';
    protected $fillable = ['name', 'allowedfrom', 'allowedto'];
    /**
     * @var array
     */
    public static $validationRules = ['name'=>'required'];
    /**
     * @var string $messages Error messages from validation
     */
    public $messages;

    public function getName() {
        return $this->name;
    }
    public function isValid()
    {
        $validation=\Validator::make($this->attributes, static::$validationRules);
        if ($validation->passes()) {
            return true;
        }
        $this->messages = $validation->messages();
    }

    public function initializeInverse (RelationType $obj, $nm)
    {
        $obj->name              = $nm;
        $obj->allowedfrom       = $this->allowedto;
        $obj->allowedto         = $this->allowedfrom;
        $obj->inverse           = $this->id;
    }
}
