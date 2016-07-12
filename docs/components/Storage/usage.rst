Usage
=====

Creating storage-agnostic objects
---------------------------------

The **Factory** allows you to create objects. Let's say you need to instantiate a new **Article** object,
which you would want to persist in the persistence storage.

You could use the following code to do that:

.. code-block:: php

   <?php

   // ..
   use SWP\Component\Storage\Factory\Factory;
   use Acme\DemoBundle\Article;

   $factory = new Factory(Article::class);
   $article = $factory->create();

   var_dump($article); // dumps Article object

By passing the fully qualified class name in Factory's construct we allow for a flexible way to create
storage-agnostic objects. For example, depending on the persistence backend, such as PHPCR or MongoDB,
you can instantiate new objects very easily using the same factory class.

Creating storage-agnostic repositories
--------------------------------------

In some cases you would need to implement different repositories for your persistence backend.
Let's say you are using Doctrine PHPCR and you want to have a generic way of adding and removing
objects from the storage, even if you decide to change Doctrine PHPCR to Doctrine MongoDB or something else.
By implementing **RepositoryInterface** in your new repository, you will be able to achive that.

Here's the example PHPCR repository implementation:

.. code-block:: php

    <?php

    namespace SWP\Bundle\StorageBundle\Doctrine\ODM\PHPCR;

    use Doctrine\ODM\PHPCR\DocumentRepository as BaseDocumentRepository;
    use SWP\Component\Storage\Model\PersistableInterface;
    use SWP\Component\Storage\Repository\RepositoryInterface;

    class DocumentRepository extends BaseDocumentRepository implements RepositoryInterface
    {
        /**
         * {@inheritdoc}
         */
        public function add(PersistableInterface $object)
        {
            $this->dm->persist($object);
            $this->dm->flush();
        }

        /**
         * {@inheritdoc}
         */
        public function remove(PersistableInterface $object)
        {
            if (null !== $this->find($object->getId())) {
                $this->dm->remove($object);
                $this->dm->flush();
            }
        }
    }

In this case, all objects that need to be persisted should implement **PersistableInterface**
which extends Doctrine's default ``Doctrine\Common\Persistence\ObjectRepository`` interface.
This component gives you simple interfaces to create storage-agnostic repositories.
