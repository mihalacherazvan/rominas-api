<?php

declare(strict_types=1);

namespace App\Auth\Spatie\Permission;

use Spatie\Permission\WildcardPermission as BaseWildcardPermission;

/**
 * Extends Spatie's wildcard permission with support for comma-delimited sub-parts,
 * e.g. a permission `users.update.editor,moderator` grants the action only when the
 * checked permission's matching part contains every listed sub-part. Index building
 * (`getIndex`/`buildIndex`) and `implies` are inherited unchanged from the base class.
 */
class WildcardPermission extends BaseWildcardPermission
{
    /**
     * @param  array<int, string>  $permission
     * @param  array<string, mixed>  $index
     */
    protected function checkIndex(array $permission, array $index): bool
    {
        if (array_key_exists((string) null, $index)) {
            return true;
        }

        if (empty($permission)) {
            return false;
        }

        $firstPermission = array_shift($permission);

        if (
            array_key_exists($firstPermission, $index)
            && is_array($index[$firstPermission])
            && $this->checkIndex($permission, $index[$firstPermission])
        ) {
            return true;
        }

        if (array_key_exists(self::WILDCARD_TOKEN, $index) && is_array($index[self::WILDCARD_TOKEN])) {
            return $this->checkIndex($permission, $index[self::WILDCARD_TOKEN]);
        }

        // Custom logic for checking comma-delimited sub-parts.
        if (str_contains($firstPermission, self::SUBPART_DELIMITER)) {
            $subParts = explode(self::SUBPART_DELIMITER, $firstPermission);

            foreach ($subParts as $subPart) {
                if (
                    ! array_key_exists($subPart, $index)
                    || ! is_array($index[$subPart])
                    || ! $this->checkIndex($permission, $index[$subPart])
                ) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
