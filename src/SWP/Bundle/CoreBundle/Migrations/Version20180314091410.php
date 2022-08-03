<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Behat\Transliterator\Transliterator;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use SWP\Bundle\ContentBundle\Model\ArticleAuthor;
use SWP\Bundle\ContentBundle\Model\ArticleAuthorInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180314091410 extends AbstractMigration implements ContainerAwareInterface
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

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_author ADD slug VARCHAR(255) DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
   public function postUp(Schema $schema) : void
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $query = $entityManager
            ->createQuery('SELECT au.id, au.name FROM SWP\Bundle\ContentBundle\Model\ArticleAuthor AS au  WHERE au.slug IS NULL');
        $articleAuthors = $query->getArrayResult();

        /** @var ArticleAuthorInterface $articleAuthor */
        foreach ($articleAuthors as $articleAuthor) {
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

    /**
     * @param Schema $schema
     *
     * @throws \Doctrine\DBAL\Migrations\AbortMigrationException
     */
    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_author DROP slug');
    }
}
