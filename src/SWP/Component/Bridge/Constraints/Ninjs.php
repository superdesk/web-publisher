<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Component\Bridge\Constraints;

use Symfony\Component\Validator\Constraint;

class Ninjs extends Constraint
{
    const INVALID_FORMAT_ERROR = '34472cb0-5c90-4874-8b4e-8a9cb65f475c';

    protected static $errorNames = [
        self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR',
    ];

    public $message = 'The supplied JSON is not valid ninjs format.';
}
