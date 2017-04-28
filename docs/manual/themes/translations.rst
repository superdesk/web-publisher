Translations
------------

The Symfony Translation component supports a variety of file formats for translation files, but in accordance with `best practices suggested in the Symfony documentation <https://symfony.com/doc/current/best_practices/i18n.html>`_, the XLIFF file format is preferred.
`JMSTranslationBundle <http://jmsyst.com/bundles/JMSTranslationBundle>`_ has been added to the project to facilitate the creation and updating of such files.

The use of abstract keys such as :code:`index.welcome.title` is preferred, with an accompanying description :code:`desc` in English to inform a translator what needs to be translated.
This description could simply be the English text which is to be displayed, but additional information about context could be provided to help a translator.

Abstract keys are used for two main reasons:

 #. Translation messages are mostly written by developers, and changes might be necessitated later. These changes would then result in changes for all supported languages instead of only for the source language, and some translations might be lost in the process.
 #. Some words in English are spelled differently in other languages, depending on their meaning, so providing context is important.

Here is an example of the preferred syntax in twig templates:

.. code-block:: twig

    {{ 'index.welcome.title'|trans|desc('Welcome to Default Theme!') }}

Translation labels added to Twig and php files can be extracted and added to XLIFF files using a `console command <http://jmsyst.com/bundles/JMSTranslationBundle/master/usage>`_ :code:`app/console translation:extract`.
This command can be used to create or update a XLIFF file in the locale :code:`en` for the :code:`DefaultTheme` of the FixturesBundle:

.. code-block:: bash

    app/console translation:extract en --dir=./src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/ --output-dir=./src/SWP/Bundle/FixturesBundle/Resources/themes/DefaultTheme/translations

This will create or update a XLIFF file in English called :code:`messages.en.xlf`, which can be used with a translation tool.
