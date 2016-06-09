<?php
/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace SWP\Bundle\ContentBundle\Doctrine\Phpcr;

use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
use SWP\Component\Common\Repository\RepositoryInterface;

class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function add($object)
    {
        $this->dm->persist($object);
        $this->dm->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function remove($object)
    {
        if (null !== $this->find($object->getId())) {
            $this->dm->remove($object);
            $this->dm->flush();
        }
    }
}
