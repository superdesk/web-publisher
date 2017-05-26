.. _running-webpublisher-tests:

Running Superdesk Publisher Tests
=====================================

The Superdesk Publisher project uses a third-party service which automatically runs tests
for any submitted :doc:`patch <patches>`. If the new code breaks any test,
the pull request will show an error message with a link to the full error details.

In any case, it's a good practice to run tests locally before submitting a
:doc:`patch <patches>` for inclusion, to check that you have not broken anything.

.. _phpunit:
.. _dependencies_optional:

Before Running the Tests
------------------------

To run the Superdesk Publisher test suite, install the external dependencies used during the
tests, such as Doctrine, Twig and Monolog. To do so,
:doc:`install Composer </cookbooks/developers/composer>` and execute the following:

.. code-block:: bash

    composer install

.. _running:

.. note::

    For unit tests we use PHPSpec, for functional tests PHPUnit and Behat for integration.

Running the PHPUnit Tests
-------------------------

Then, run the test suite from the Superdesk Publisher root directory with the following
command:

.. code-block:: bash

    bin/phpunit -c app/

The output should display ``OK``. If not, read the reported errors to figure out
what's going on and if the tests are broken because of the new code.

.. tip::

    The entire Superdesk Publisher suite can take up to several minutes to complete. If you
    want to test a single component/bundle, type its path after the ``phpunit`` command,
    e.g.:

    .. code-block:: bash

        bin/phpunit src/SWP/Bundle/MultiTenancyBundle/

.. tip::

    On Windows, install the `ConEmu`_, `ANSICON`_ or `Mintty`_ free applications
    to see coloured test results.

Running the PHPSpec specs
-------------------------

.. note::

    This section is based on `Sylius documentation <http://docs.sylius.org>`_.

PHPSpec is a PHP toolset to drive emergent design by specification.
It is not really a testing tool, but a design instrument, which helps structuring the objects and how they work together.

The Superdesk Publisher approach is to always describe the behaviour of the next object you are about to implement.

As an example, we'll write a service, which sets the current tenant in the context.
To initialize a new spec, use the ``desc`` command.

We just need to tell **PHPSpec** we will be working on
the `TenantContext` class.

.. code-block:: bash

    bin/phpspec desc "SWP\Component\MultiTenancy\Context\TenantContext"
    Specification for TenantContext created in spec.

What have we just done? **PHPSpec** has created the spec for us. You can
navigate to the spec folder and see the spec there:

.. code-block:: php

    <?php

    namespace spec\SWP\Component\MultiTenancy\Context;

    use PhpSpec\ObjectBehavior;
    use Prophecy\Argument;

    class TenantContextSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->shouldHaveType('SWP\Component\MultiTenancy\Context\TenantContext');
        }
    }

The object behaviour is made of examples. Examples are encased in public methods,
started with ``it_`` or ``its_``.

**PHPSpec** searches for such methods in your specification to run.
Why underscores for example names? ``just_because_its_much_easier_to_read``
than ``someLongCamelCasingLikeThat``.

Now, let's write the first example, which will set the current tenant:

.. code-block:: php

    <?php

    namespace spec\SWP\Component\MultiTenancy\Context;

    use PhpSpec\ObjectBehavior;
    use SWP\Component\MultiTenancy\Model\TenantInterface;

    class TenantContextSpec extends ObjectBehavior
    {
        function it_is_initializable()
        {
            $this->shouldHaveType('SWP\Component\MultiTenancy\Context\TenantContext');
        }

        function it_should_set_tenant(TenantInterface $tenant)
        {
            $tenant->getId()->willReturn(1);
            $tenant->getSubdomain()->willReturn('example1');
            $tenant->getName()->willReturn('example1');

            $this->setTenant($tenant)->shouldBeNull();
        }
    }

The example looks clear and simple, the ``TenantContext`` service should obtain the tenant id, name, subdomain and call the method to set the tenant.

Try running the example by using the following command:

.. code-block:: bash

    bin/phpspec run

    > spec\SWP\Component\MultiTenancy\Context\TenantContext

      ✘ it should set tenant
          Class TenantContext does not exists.

             Do you want me to create it for you? [Y/n]

Once the class is created and you run the command again, PHPSpec will ask if it should create the method as well.
Start implementing the initial version of the TenantContext.

.. code-block:: php

    <?php

    namespace SWP\Component\MultiTenancy\Context;

    use SWP\Component\MultiTenancy\Model\TenantInterface;

    /**
     * Class TenantContext.
     */
    class TenantContext implements TenantContextInterface
    {
        /**
         * @var TenantInterface
         */
        protected $tenant;

        /**
         * {@inheritdoc}
         */
        public function setTenant(TenantInterface $tenant)
        {
            $this->tenant = $tenant;
        }
    }

Done! If you run PHPSpec again, you should see the following output:

.. code-block:: bash

    bin/phpspec run

    > spec\SWP\Component\MultiTenancy\Context\TenantContext

      ✔ it should set tenant

    1 examples (1 passed)
    123ms

This example is greatly simplified, in order to illustrate how we work.
More examples might cover errors, API exceptions and other edge-cases.

A few tips & rules to follow when working with PHPSpec & Superdesk Publisher:

* RED is good, add or fix the code to make it green;
* RED-GREEN-REFACTOR is our rule;
* All specs must pass;
* When writing examples, **describe** the behaviour of the object in the present tense;
* Omit the ``public`` keyword;
* Use underscores (``_``) in the examples;
* Use type hinting to mock and stub classes;
* If your specification is getting too complex, the design is wrong. Try decoupling a bit more;
* If you cannot describe something easily, probably you should not be doing it that way;
* shouldBeCalled or willReturn, never together, except for builders;
* Use constants in assumptions but strings in expected results;

.. _ConEmu: https://conemu.github.io/
.. _ANSICON: https://github.com/adoxa/ansicon/releases
.. _Mintty: https://mintty.github.io/
