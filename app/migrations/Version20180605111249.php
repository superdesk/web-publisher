<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Behat\Transliterator\Transliterator;
use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180605111249 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface|null $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $query = $entityManager
            ->createQuery('SELECT au.id, au.name FROM SWP\Bundle\ContentBundle\Model\ArticleAuthor AS au  WHERE au.slug IS NULL');
        $articleAuthors = $query->getArrayResult();

        /** @var ArticleAuthorInterface $articleAuthor */
        foreach ((array) $articleAuthors as $articleAuthor) {
            $qb = $entityManager->createQueryBuilder();
            $query = $qb->update(ArticleAuthor::class, 'au')
                ->set('au.slug', '?1')
                ->where('au.id = ?2')
                ->setParameter(1, Transliterator::transliterate($articleAuthor['name']))
                ->setParameter(2, $articleAuthor['id'])
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
