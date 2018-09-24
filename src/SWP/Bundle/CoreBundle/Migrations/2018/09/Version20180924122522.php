<?php

declare(strict_types=1);

namespace SWP\Migrations;

use Behat\Transliterator\Transliterator;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180924122522 extends AbstractMigration implements ContainerAwareInterface
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
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id', 'integer');
        $rsm->addScalarResult('keywords', 'keywords', 'array');

        $query = $entityManager->createNativeQuery('SELECT id, keywords FROM swp_article', $rsm);
        $articles = $query->getResult();

        $dbConnection = $entityManager->getConnection();
        $nextvalQuery = $dbConnection->getDatabasePlatform()->getSequenceNextValSQL('swp_keyword_id_seq');
        $newId = (int) $dbConnection->fetchColumn($nextvalQuery);

        $keywords = [];
        foreach ($articles as $article) {
            foreach ($article['keywords'] as $articleKeyword) {
                if (!\array_key_exists($articleKeyword, $keywords)) {
                    $keywords[$articleKeyword] = [
                        'name' => $articleKeyword,
                        'id' => $newId,
                    ];

                    $this->addSql('INSERT INTO swp_keyword (id, slug, name) VALUES (:id, :slug, :name)', [
                        'id' => $newId,
                        'slug' => Transliterator::urlize($articleKeyword),
                        'name' => $articleKeyword,
                    ]);
                    ++$newId;
                }
            }
        }

        foreach ($articles as $article) {
            foreach ($article['keywords'] as $articleKeyword) {
                if (array_key_exists($articleKeyword, $keywords)) {
                    $this->addSql(
                        'INSERT INTO swp_article_keyword (article_id, keyword_id) VALUES (:article_id, :keyword_id)',
                        [
                            'article_id' => $article['id'],
                            'keyword_id' => $keywords[$articleKeyword]['id'],
                        ]
                    );
                    ++$newId;
                }
            }
        }

        $this->addSql('ALTER TABLE swp_article DROP keywords');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE swp_article ADD keywords TEXT NOT NULL');
    }
}
