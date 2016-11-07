Usage
=====

Creating new content lists
--------------------------

Here is an example on how to create a new content list:

.. code-block:: php

    // src/AppBundle/Controller/MyController.php

    use SWP\Component\ContentList\Model\ContentListInterface;
    // ...
    public function createAction()
    {
        $repository = $this->container->get('swp.repository.content_list');

        /* @var ContentListInterface $contentList */
        $contentList = $this->get('swp.factory.content_list')->create();
        $contentList->setName('my content list');
        $contentList->setDescription('description');
        $contentList->setLimit(10);
        $contentList->setType(ContentListInterface::TYPE_AUTOMATIC);
        // ...

        $repository->add($contentList);

        // ...
    }

Adding new items to content list
--------------------------------

.. code-block:: php

    // src/AppBundle/Controller/MyController.php

    use SWP\Component\ContentList\Model\ContentListInterface;
    use SWP\Component\ContentList\Model\ContentListItemInterface;
    use Acme\AppBundle\Entity\Article;
    // ...
    public function createAction()
    {
        $repository = $this->container->get('swp.repository.content_list');

        /* @var ContentListInterface $contentList */
        $contentList = $this->get('swp.factory.content_list')->create();
        $contentList->setName('my content list');
        $contentList->setDescription('description');
        $contentList->setLimit(10);
        $contentList->setType(ContentListInterface::TYPE_AUTOMATIC);
        // ...

        /* @var ContentListItemInterface $contentListItem */
        $contentListItem = $this->get('swp.factory.content_list_item')->create();
        $contentListItem->setPosition(6);
        $contentListItem->setContent(new Article());
        $contentList->addItem($contentListItem);

        $repository->add($contentList);

        // ...
    }

.. note::

    ``Article`` class must implement ``SWP\Component\ContentList\Model\ListContentInterface``.

Deleting content lists
----------------------

.. code-block:: php

    // src/AppBundle/Controller/MyController.php

    use SWP\Component\ContentList\Model\ContentListInterface;
    use Acme\AppBundle\Entity\Article;
    // ...
    public function deleteAction($id)
    {
        $repository = $this->container->get('swp.repository.content_list');

        /* @var ContentListInterface $contentList */
        $contentList = $repository->findOneBy(['id' => $id]);
        // ...

        $repository->remove($contentList);

        // ...
    }

Deleting content lists items
----------------------------

.. code-block:: php

    // src/AppBundle/Controller/MyController.php

    use SWP\Component\ContentList\Model\ContentListItemInterface;
    use Acme\AppBundle\Entity\Article;
    // ...
    public function deleteAction($id)
    {
        $repository = $this->container->get('swp.repository.content_list_item');

        /* @var ContentListItemInterface $contentListItem */
        $contentListItem = $repository->findOneBy(['id' => $id]);
        // ...

        $repository->remove($contentListItem);

        // ...
    }

Forms
-----

Content list type selector
~~~~~~~~~~~~~~~~~~~~~~~~~~

If you want to use content list type selector inside your custom form you can do it by adding ``SWP\Bundle\ContentListBundle\Form\Type\ContentListTypeSelectorType`` form field type to your form:

.. code-block:: php

    namespace Acme\AppBundle\Form\Type;

    use Symfony\Component\Form\AbstractType;
    use Symfony\Component\Form\Extension\Core\Type\TextType;
    use Symfony\Component\Form\FormBuilderInterface;
    use Symfony\Component\Validator\Constraints\NotBlank;


    class MyListType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'description' => 'List name',
            ])
            ->add('type', ContentListTypeSelectorType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'description' => 'List type',
            ])
        }
    }

Alternatively, you could also extend from the default ``SWP\Bundle\ContentListBundle\Form\Type\ContentListType`` class if you would only add more fields on top of the existing form.

.. note::

    For more details on how to register custom factory, repository, object manager, forms using custom classes see
    SWPStorageBundle :doc:`/bundles/SWPStorageBundle/usage` section.

Getting content lists from repository
-------------------------------------

To get single or all content lists from the repository you can use default Doctrine ORM ``SWP\Bundle\ContentListBundle\Doctrine\ORM\ContentListRepository`` repository. It has the same methods
as Doctrine ORM ``EntityRepository``, but it contains an extra method to get content lists by its type:

- ``findByType(string $type): array`` - it gets many content lists by its type, type can be either: automatic or manual.

.. code-block:: php

    // src/AppBundle/Controller/MyController.php

    use SWP\Component\ContentList\Model\ContentListInterface;
    // ...
    public function getAction()
    {
        $repository = $this->container->get('swp.repository.content_list');

        $lists = $repository->findByType(ContentListInterface::TYPE_AUTOMATIC);
        var_dump($lists);die;
        // ...
    }

.. note::

    This repository is automatically registered as a service for you and can be accessible under service id:
    ``swp.repository.content_list`` in Symfony container.


Getting content lists items from repository
-------------------------------------------

To get content list items you can use default repository which is registered as a service under the
``swp.repository.content_list_item`` key in Symfony container. It extends default Doctrine ORM EntityRepository.
