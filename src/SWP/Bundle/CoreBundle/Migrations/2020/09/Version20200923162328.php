<?php declare(strict_types=1);

namespace SWP\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use SWP\Bundle\ContentBundle\Model\Article;
use SWP\Bundle\ContentBundle\Model\ArticleExtraEmbedField;
use SWP\Bundle\ContentBundle\Model\ArticleExtraTextField;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200923162328 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE swp_article_extra_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE swp_article_extra (id INT NOT NULL, article_id INT DEFAULT NULL, field_name VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, value VARCHAR(255) DEFAULT NULL, embed VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9E61B3177294869C ON swp_article_extra (article_id)');
        $this->addSql('ALTER TABLE swp_article_extra ADD CONSTRAINT FK_9E61B3177294869C FOREIGN KEY (article_id) REFERENCES swp_article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE swp_article_extra_id_seq CASCADE');
        $this->addSql('DROP TABLE swp_article_extra');
    }

    public function postUp(Schema $schema): void
    {
        $entityManager = $this->container->get('doctrine.orm.default_entity_manager');
        $query = $entityManager
            ->createQuery('SELECT a FROM SWP\Bundle\CoreBundle\Model\Article a');

        $batchSize = 20;
        $i = 1;
        $iterableResult = $query->iterate();
        foreach ($iterableResult as $row) {
            /** @var Article $article */
            $article = $row[0];

            $legacyExtra = $article->getExtra();
            if (empty($legacyExtra)) {
                continue;
            }

            foreach ($legacyExtra as $key => $extraItem) {
                if(is_array($extraItem)) {
                    $extra = new ArticleExtraEmbedField();
                    $extra->setFieldName($key);
                    $extra->setDescription($extraItem['description']);
                    $extra->setEmbed($extraItem['embed']);
                } else {
                    $extra = new ArticleExtraTextField();
                    $extra->setFieldName($key);
                    $extra->setValue($extraItem);
                }
                $extra->setArticle($article);
            }
            $entityManager->persist($extra);

            if (0 === ($i % $batchSize)) {
                $entityManager->flush();
                $entityManager->clear();
            }
            ++$i;
        }

        $entityManager->flush();
    }
}
