<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181008115755 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
    }

    /**
     * @param Schema $schema
     */
   public function postUp(Schema $schema) : void
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $query = $entityManager
            ->createQuery('SELECT au.id, au.avatarUrl FROM SWP\Bundle\ContentBundle\Model\ArticleAuthor AS au  WHERE au.avatarUrl IS NOT NULL');
        $articleAuthors = $query->getArrayResult();

        /** @var ArticleAuthorInterface $articleAuthor */
        foreach ((array) $articleAuthors as $articleAuthor) {
            $data = explode('/author/media/', $articleAuthor['avatarUrl']);

            if (!isset($data[1])) {
                continue;
            }

            $avatarBaseName = $data[1];

            $qb = $entityManager->createQueryBuilder();
            $query = $qb->update(ArticleAuthor::class, 'au')
                ->set('au.avatarUrl', ':url')
                ->where('au.id = :id')
                ->setParameters([
                    'url' => $avatarBaseName,
                    'id' => $articleAuthor['id'],
                ])
                ->getQuery();

            $query->execute();
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
    }
}
