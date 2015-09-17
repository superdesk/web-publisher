<?php

namespace spec\SWP\ContentBundle\Controller;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiArticleControllerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\ContentBundle\Controller\ApiArticleController');
    }

    function its_method_get_should_return_an_article()
    {
        // Check if article exists in output
        // Check for valid HTTP status
    }

    function its_method_get_should_return_correct_datatype()
    {
        // Set Accept-header to json
        // Excepect json returned

        // Set Accept-header to xml
        // Excepect xml returned
    }

    function its_method_get_throws_an_exception_when_no_articles_are_found()
    {
       // Search for non-existing article
       // Check if exception is thrown
       // Check if correct HTTP status is returned
    }

    function its_method_new_should_add_an_article()
    {
        // Check if correct status is returned
        // Check if user reference is in Location-header
    }

    function its_method_new_should_return_correct_status_on_failure()
    {
        // Block adding user
        // Check if correct status is returned
    }

    function its_method_edit_should_update_article_data()
    {
        // Check if correct status is returned
    }

    function its_method_edit_should_return_correct_status_on_failure()
    {
        // Check if correct status is returned
    }

    function its_method_delete_should_remove_an_article()
    {
        // Check if correct status is returned
    }

    function its_method_delete_should_return_correct_status_on_failure()
    {
        // Check if correct status is returned
    }
}
