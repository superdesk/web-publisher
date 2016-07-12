Usage
=====

Validators
----------

This component provides one type of validator by default:

- `ninjs`_ validator

The first one is a simple implementation of JSON format validation against concrete schema. The second one is a
custom implementation of ninjs validator which validates given value by Superdesk ninjs schema.

Superdesk Ninjs Validator
~~~~~~~~~~~~~~~~~~~~~~~~~

This validator validates value against specific Superdesk ninjs format schema.

Usage:

.. code-block:: php

   <?php
   // example.php
   // ..

   use SWP\Component\Bridge\Validator\NinjsValidator;

   $validator = new NinjsValidator();
   if ($validator->isValid('{some ninjs format value}')) {
       // valid
   }

   // not valid


The Superdesk ninjs schema:

.. code-block:: json

    {
        "$schema": "http://json-schema.org/draft-03/schema#",
        "id" : "http://www.iptc.org/std/ninjs/ninjs-schema_1.1.json#",
        "type" : "object",
        "title" : "IPTC ninjs - News in JSON - version 1.1 (approved, 2014-03-12) / document revision of 2014-11-15: geometry_* moved under place",
        "description" : "A news item as JSON object -- copyright 2014 IPTC - International Press Telecommunications Council - www.iptc.org - This document is published under the Creative Commons Attribution 3.0 license, see  http://creativecommons.org/licenses/by/3.0/  $$comment: as of 2014-03-13 ",
        "additionalProperties" : false,
        "patternProperties" : {
            "^description_[a-zA-Z0-9_]+" : {
                "description" : "A free-form textual description of the content of the item. (The string appended to description_ in the property name should reflect the format of the text)",
                "type" : "string"
            },
            "^body_[a-zA-Z0-9_]+" : {
                "description" : "The textual content of the news object. (The string appended to body_ in the property name should reflect the format of the text)",
                "type" : "string"
            }
        },
        "properties" : {
            "guid" : {
                "description" : "The identifier for this news object",
                "type" : "string",
                "format" : "guid",
                "required" : true
            },
            "type" : {
                "description" : "The generic news type of this news object",
                "type" : "string",
                "enum" : ["text", "audio", "video", "picture", "graphic", "composite"]
            },
            "slugline" : {
                "description" : "The slugline",
                "type" : "string",
                "required" : true
            },
            "mimetype" : {
                "description" : "A MIME type which applies to this news object",
                "type" : "string"
            },
            "representationtype" : {
                "description" : "Indicates how complete this representation of a news item is",
                "type" : "string",
                "enum" : ["complete", "incomplete"]
            },
            "profile" : {
                "description" : "An identifier for the kind of content of this news object",
                "type" : "string"
            },
            "version" : {
                "description" : "The version of the news object which is identified by the uri property",
                "type" : "string"
            },
            "versioncreated" : {
                "description" : "The date and time when this version of the news object was created",
                "type" : "string",
                "format" : "date-time"
            },
            "embargoed" : {
                "description" : "The date and time before which all versions of the news object are embargoed. If absent, this object is not embargoed.",
                "type" : "string",
                "format" : "date-time"
            },
            "pubstatus" : {
                "description" : "The publishing status of the news object, its value is *usable* by default.",
                "type" : "string",
                "enum" : ["usable", "withheld", "canceled"]
            },
            "urgency" : {
                "description" : "The editorial urgency of the content from 1 to 9. 1 represents the highest urgency, 9 the lowest.",
                "type" : "number"
            },
            "priority" : {
                "description" : "The editorial priority of the content from 1 to 9. 1 represents the highest priority, 9 the lowest.",
                "type" : "number"
            },
            "copyrightholder" : {
                "description" : "The person or organisation claiming the intellectual property for the content.",
                "type" : "string"
            },
            "copyrightnotice" : {
                "description" : "Any necessary copyright notice for claiming the intellectual property for the content.",
                "type" : "string"
            },
            "usageterms" : {
                "description" : "A natural-language statement about the usage terms pertaining to the content.",
                "type" : "string"
            },
            "language" : {
                "description" : "The human language used by the content. The value should follow IETF BCP47",
                "type" : "string"
            },
            "service" : {
                "description" : "A service e.g. World Photos, UK News etc.",
                "type" : "array",
                "items" : {
                    "type" : "object",
                    "additionalProperties" : false,
                    "properties" : {
                        "name" : {
                            "description" : "The name of a service",
                            "type" : "string"
                        },
                        "code" : {
                            "description": "The code for the service in a scheme (= controlled vocabulary) which is identified by the scheme property",
                            "type" : "string"
                        }
                    }
                }
            },
            "person" : {
                "description" : "An individual human being",
                "type" : "array",
                "items" : {
                    "type" : "object",
                    "additionalProperties" : false,
                    "properties" : {
                        "name" : {
                            "description" : "The name of a person",
                            "type" : "string"
                        },
                        "rel" : {
                            "description" : "The relationship of the content of the news object to the person",
                            "type" : "string"
                        },
                        "scheme" : {
                            "description" : "The identifier of a scheme (= controlled vocabulary) which includes a code for the person",
                            "type" : "string",
                            "format" : "uri"
                        },
                        "code" : {
                            "description": "The code for the person in a scheme (= controlled vocabulary) which is identified by the scheme property",
                            "type" : "string"
                        }
                    }
                }
            },
            "organisation" : {
                "description" : "An administrative and functional structure which may act as as a business, as a political party or not-for-profit party",
                "type" : "array",
                "items" : {
                    "type" : "object",
                    "additionalProperties" : false,
                    "properties" : {
                        "name" : {
                            "description" : "The name of the organisation",
                            "type" : "string"
                        },
                        "rel" : {
                            "description" : "The relationship of the content of the news object to the organisation",
                            "type" : "string"
                        },
                        "scheme" : {
                            "description" : "The identifier of a scheme (= controlled vocabulary) which includes a code for the organisation",
                            "type" : "string",
                            "format" : "uri"
                        },
                        "code" : {
                            "description": "The code for the organisation in a scheme (= controlled vocabulary) which is identified by the scheme property",
                            "type" : "string"
                        },
                        "symbols" : {
                            "description" : "Symbols used for a finanical instrument linked to the organisation at a specific market place",
                            "type" : "array",
                            "items" : {
                                "type" : "object",
                                "additionalProperties" : false,
                                "properties" : {
                                    "ticker" : {
                                        "description" : "Ticker symbol used for the financial instrument",
                                        "type": "string"
                                    },
                                    "exchange" : {
                                        "description" : "Identifier for the marketplace which uses the ticker symbols of the ticker property",
                                        "type" : "string"
                                    }
                                }
                            }
                        }
                    }
                }
            },
            "place" : {
                "description" : "A named location",
                "type" : "array",
                "items" : {
                    "type" : "object",
                    "additionalProperties" : false,
                    "patternProperties" : {
                        "^geometry_[a-zA-Z0-9_]+" : {
                            "description" : "An object holding geo data of this place. Could be of any relevant geo data JSON object definition.",
                            "type" : "object"
                        }
                    },
                    "properties" : {
                        "name" : {
                            "description" : "The name of the place",
                            "type" : "string"
                        },
                        "rel" : {
                            "description" : "The relationship of the content of the news object to the place",
                            "type" : "string"
                        },
                        "scheme" : {
                            "description" : "The identifier of a scheme (= controlled vocabulary) which includes a code for the place",
                            "type" : "string",
                            "format" : "uri"
                        },
                        "qcode" : {
                            "description": "The code for the place in a scheme (= controlled vocabulary) which is identified by the scheme property",
                            "type" : "string"
                        },
                        "state" : {
                            "description" : "The state for the place",
                            "type" : "string"
                        },
                        "group" : {
                            "description" : "The place group",
                            "type" : "string"
                        },
                        "name" : {
                            "description" : "The place name",
                            "type" : "string"
                        },
                        "country" : {
                            "description" : "The country name",
                            "type" : "string"
                        },
                        "world_region" : {
                            "description" : "The world region",
                            "type" : "string"
                        }
                    }
                }
            },
            "subject" : {
                "description" : "A concept with a relationship to the content",
                "type" : "array",
                "items" : {
                    "type" : "object",
                    "additionalProperties" : false,
                    "properties" : {
                        "name" : {
                            "description" : "The name of the subject",
                            "type" : "string"
                        },
                        "rel" : {
                            "description" : "The relationship of the content of the news object to the subject",
                            "type" : "string"
                        },
                        "scheme" : {
                            "description" : "The identifier of a scheme (= controlled vocabulary) which includes a code for the subject",
                            "type" : "string",
                            "format" : "uri"
                        },
                        "code" : {
                            "description": "The code for the subject in a scheme (= controlled vocabulary) which is identified by the scheme property",
                            "type" : "string"
                        }
                    }
                }
            },
            "event" : {
                "description" : "Something which happens in a planned or unplanned manner",
                "type" : "array",
                "items" : {
                    "type" : "object",
                    "additionalProperties" : false,
                    "properties" : {
                        "name" : {
                            "description" : "The name of the event",
                            "type" : "string"
                        },
                        "rel" : {
                            "description" : "The relationship of the content of the news object to the event",
                            "type" : "string"
                        },
                        "scheme" : {
                            "description" : "The identifier of a scheme (= controlled vocabulary) which includes a code for the event",
                            "type" : "string",
                            "format" : "uri"
                        },
                        "code" : {
                            "description": "The code for the event in a scheme (= controlled vocabulary) which is identified by the scheme property",
                            "type" : "string"
                        }
                    }
                }
            },
            "object" : {
                "description" : "Something material, excluding persons",
                "type" : "array",
                "items" : {
                    "type" : "object",
                    "additionalProperties" : false,
                    "properties" : {
                        "name" : {
                            "description" : "The name of the object",
                            "type" : "string"
                        },
                        "rel" : {
                            "description" : "The relationship of the content of the news object to the object",
                            "type" : "string"
                        },
                        "scheme" : {
                            "description" : "The identifier of a scheme (= controlled vocabulary) which includes a code for the object",
                            "type" : "string",
                            "format" : "uri"
                        },
                        "code" : {
                            "description": "The code for the object in a scheme (= controlled vocabulary) which is identified by the scheme property",
                            "type" : "string"
                        }
                    }
                }
            },
            "byline" : {
                "description" : "The name(s) of the creator(s) of the content",
                "type" : "string"
            },
            "headline" : {
                "description" : "A brief and snappy introduction to the content, designed to catch the reader's attention",
                "type" : "string"
            },
            "located" : {
                "description" : "The name of the location from which the content originates.",
                "type" : "string"
            },
            "renditions" : {
                "description" : "Wrapper for different renditions of non-textual content of the news object",
                "type" : "object",
                "additionalProperties" : false,
                "patternProperties" : {
                    "^[a-zA-Z0-9]+" : {
                        "description" : "A specific rendition of a non-textual content of the news object.",
                        "type" : "object",
                        "additionalProperties" : false,
                        "properties" : {
                            "href" : {
                                "description" : "The URL for accessing the rendition as a resource",
                                "type" : "string",
                                "format" : "uri"
                            },
                            "mimetype" : {
                                "description" : "A MIME type which applies to the rendition",
                                "type" : "string"
                            },
                            "title" : {
                                "description" : "A title for the link to the rendition resource",
                                "type" : "string"
                            },
                            "height" : {
                                "description" : "For still and moving images: the height of the display area measured in pixels",
                                "type" : "number"
                            },
                            "width" : {
                                "description" : "For still and moving images: the width of the display area measured in pixels",
                                "type" : "number"
                            },
                            "sizeinbytes" : {
                                "description" : "The size of the rendition resource in bytes",
                                "type" : "number"
                            }
                        }
                    }
                }
            },
            "associations" : {
                "description" : "Content of news objects which are associated with this news object.",
                "type" : "object",
                "additionalProperties" : false,
                "patternProperties" : {
                    "^[a-zA-Z0-9]+" :  { "$ref": "http://www.iptc.org/std/ninjs/ninjs-schema_1.0.json#" }
                }
            }
        }
    }

Validator Chain
~~~~~~~~~~~~~~~

You could also use Validator Chain to validate the json value by many validators at once:

.. code-block:: php

   <?php
   // example.php
   // ..

   use SWP\Component\Bridge\ValidatorChain;
   use SWP\Component\Bridge\Validator\NinjsValidator;

   $validatorChain = ValidatorChain();
   $validatorChain->addValidator(new NinjsValidator(), 'ninjs');
   $validatorChain->addValidator(new CustomValidator(), 'custom');

   if ($validatorChain->isValid('{json value}')) {
       // valid
   }

   // not valid

Data Transformers
-----------------

Transformers are meant to transform incoming value to object representation.

This component supports one transformer by default:

- JSON to Package

JSON to Package Data Transformer
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This data transformer transforms JSON string to ``Package`` object. The input value is first validated by the Validator Chain
and if the validation is success it serializes JSON value to ``Package`` object.

The ``Package`` object is a one-to-one representation of Superdesk Package.


Usage:

.. code-block:: php

   <?php
   // example.php
   // ..

   use SWP\Component\Bridge\Transformer\JsonToPackageTransformer;

   $transformer = new JsonToPackageTransformer();
   $package = $transformer->transform('{json value}');

   var_dump($package);die; // will dump an instance of ``SWP\Component\Bridge\Model\Package`` object.

This transformer can support reverse transform but it is not supported at the moment.

The below example will throw  ``SWP\Component\Bridge\Exception\MethodNotSupportedException`` exception:

.. code-block:: php

   <?php
   // example.php
   // ..

   use SWP\Component\Bridge\Model\Package;
   use SWP\Component\Bridge\Transformer\JsonToPackageTransformer;

   $package = new Package();
   // ..
   $transformer = new JsonToPackageTransformer();
   $package = $transformer->reverseTransform($package);

.. note::

    If the transformation will fail for some reason an exception ``SWP\Component\Bridge\Exception\TransformationFailedException`` will be thrown.

Data Transformer Chain
~~~~~~~~~~~~~~~~~~~~~~

You can use Transformer Chain to transform any value by many transformers at once:

.. code-block:: php

   <?php
   // example.php
   // ..

   use SWP\Component\Bridge\Transformer\DataTransformerChain;
   use SWP\Component\Bridge\Transformer\JsonToPackageTransformer;

   $transformerChain = DataTransformerChain(new JsonToPackageTransformer(), /* new CustomTransformer() */);
   $result = $transformer->transform(/* some value or object */);

   var_dump($result); // result of transformation

.. note::

    If the transformation will fail for some reason an exception ``SWP\Component\Bridge\Exception\TransformationFailedException`` will be thrown.

To reverse transform use ``reverseTransform`` method:

.. code-block:: php

   <?php
   // example.php
   // ..

   use SWP\Component\Bridge\Transformer\DataTransformerChain;
   use SWP\Component\Bridge\Transformer\JsonToPackageTransformer;

   $transformerChain = DataTransformerChain(new JsonToPackageTransformer(), /* new CustomTransformer() */);
   $result = $transformer->reverseTransform(/* some value or object */);

   var_dump($result); // result of transformation

.. _ninjs: http://dev.iptc.org/ninjs
