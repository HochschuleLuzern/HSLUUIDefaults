<?php declare(strict_types=1);

class ilHSLUUIDefaultsAccessChecker
{
    private ilRbacReview $rbac_review;
    private bool $admin_role_is_defiend;
    private ?int $admin_role_id;

    public function __construct(ilRbacReview $rbac_review)
    {
        $this->rbac_review = $rbac_review;

        // Check if the admin role is set and if the given ID is indeed the object ID of a role
        if (SYSTEM_ROLE_ID !== null
            && \ilObject::_lookupType(SYSTEM_ROLE_ID) == 'role'
        ) {
            $this->admin_role_is_defiend = true;
            $this->admin_role_id = (int) SYSTEM_ROLE_ID;
        } else {
            $this->admin_role_is_defiend = false;
            $this->admin_role_id = null;
        }
    }

    public function checkIfAdminRoleIsDefinedAndUserIsAdmin(\ilObjUser $user) : bool
    {
        return $this->admin_role_is_defiend
            && $this->rbac_review->isAssigned($user->getId(), $this->admin_role_id) ;
    }
}