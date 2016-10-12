<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Bundle\ContentBundle\Doctrine\ORM;

use SWP\Bundle\ContentBundle\Model\RouteRepositoryInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;

class RouteRepository extends EntityRepository implements RouteRepositoryInterface
{
}
