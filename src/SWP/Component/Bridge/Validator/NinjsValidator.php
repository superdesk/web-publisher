<?php

/*
 * This file is part of the Superdesk Web Publisher Bridge Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\Bridge\Validator;

class NinjsValidator extends JsonValidator
{
    protected $schema = '{
  "$schema":"http://json-schema.org/draft-03/schema#",
  "type":"object",
  "title":"Superdesk extension of IPTC ninjs - News in JSON - version 1.1 (approved, 2016-??-??) / document revision of 2016-04-11: initial submit",
  "description":"A news item as JSON object -- copyright 2016 Sourcefabric - www.sourcefabric.org - This document is published under the Creative Commons Attribution 3.0 license, see  http://creativecommons.org/licenses/by/3.0/  $$comment: as of 2014-03-13 ",
  "additionalProperties":true,
  "patternProperties":{
    "^description_[a-zA-Z0-9_]+":{
      "description":"A free-form textual description of the content of the item. (The string appended to description_ in the property name should reflect the format of the text)",
      "type":"string"
    },
    "^body_[a-zA-Z0-9_]+":{
      "description":"The textual content of the news object. (The string appended to body_ in the property name should reflect the format of the text)",
      "type":"string"
    }
  },
  "required":[
    "guid"
  ],
  "properties":{
    "guid":{
      "description":"The identifier for this news object",
      "type":"string"
    },
    "type":{
      "description":"The generic news type of this news object",
      "type":"string",
      "enum":[
        "text",
        "audio",
        "video",
        "picture",
        "graphic",
        "composite"
      ]
    },
    "mimetype":{
      "description":"A MIME type which applies to this news object",
      "type":"string"
    },
    "profile":{
      "description":"An identifier for the kind of content of this news object",
      "type":"string"
    },
    "version":{
      "description":"The version of the news object which is identified by the uri property",
      "type":"string"
    },
    "versioncreated":{
      "description":"The date and time when this version of the news object was created",
      "type":"string",
      "format":"date-time"
    },
    "firstcreated":{
      "description":"The date and time when the first version of the item was created",
      "type":"string",
      "format":"date-time"
    },
    "firstpublished":{
      "description":"The date and time when the item has been published for the first time",
      "type":"string",
      "format":"date-time"
    },
    "embargoed":{
      "description":"The date and time before which all versions of the news object are embargoed. If absent, this object is not embargoed.",
      "type":"string",
      "format":"date-time"
    },
    "pubstatus":{
      "description":"The publishing status of the news object, its value is *usable* by default.",
      "type":"string",
      "enum":[
        "usable",
        "withheld",
        "canceled",
        "unpublished"
      ]
    },
    "urgency":{
      "description":"The editorial urgency of the content from 1 to 9. 1 represents the highest urgency, 9 the lowest.",
      "type":"number"
    },
    "priority":{
      "description":"The editorial priority of the content from 1 to 6. 1 represents the highest priority, 6 the lowest.",
      "type":"number"
    },
    "service":{
      "description":"The ANPA category of the content",
      "type":"array",
      "items":{
        "type":"object",
        "additionalProperties":false,
        "properties":{
          "code":{
            "description":"The qualified code of the category",
            "type":"string"
          },
          "name":{
            "description":"The name of the category",
            "type":"string"
          }
        }
      }
    },
    "genre":{
      "description":"The item genre",
      "type":"array",
      "items":{
        "type":"object",
        "additionalProperties":false,
        "properties":{
          "code":{
            "description":"The qualified code of the genre",
            "type":"string"
          },
          "name":{
            "description":"The name of the genre",
            "type":"string"
          }
        }
      }
    },
    "signal":{
      "description":"Warning signal for legal action",
      "type":"array",
      "items":{
        "type":"object",
        "additionalProperties":false,
        "properties":{
          "code":{
            "description":"The qualified code of the signal",
            "type":"string"
          },
          "name":{
            "description":"The name of the signal",
            "type":"string"
          }
        }
      }
    },
    "copyrightholder":{
      "description":"The person or organisation claiming the intellectual property for the content.",
      "type":"string"
    },
    "copyrightnotice":{
      "description":"Any necessary copyright notice for claiming the intellectual property for the content.",
      "type":"string"
    },
    "usageterms":{
      "description":"A natural-language statement about the usage terms pertaining to the content.",
      "type":"string"
    },
    "language":{
      "description":"The human language used by the content. The value should follow IETF BCP47",
      "type":"string",
      "required":true
    },
    "organisation":{
      "description":"An administrative and functional structure which may act as as a business, as a political party or not-for-profit party",
      "type":"array",
      "items":{
        "type":"object",
        "additionalProperties":false,
        "properties":{
          "name":{
            "description":"The name of the organisation",
            "type":"string"
          },
          "rel":{
            "description":"The relationship of the content of the news object to the organisation",
            "type":"string"
          },
          "symbols":{
            "description":"Symbols used for a finanical instrument linked to the organisation at a specific market place",
            "type":"array",
            "items":{
              "type":"object",
              "additionalProperties":false,
              "properties":{
                "ticker":{
                  "description":"Ticker symbol used for the financial instrument",
                  "type":"string"
                },
                "exchange":{
                  "description":"Identifier for the marketplace which uses the ticker symbols of the ticker property",
                  "type":"string"
                }
              }
            }
          }
        }
      }
    },
    "place":{
      "description":"A named location",
      "type":"array",
      "items":{
        "type":"object",
        "additionalProperties":false,
        "patternProperties":{
          "^geometry_[a-zA-Z0-9_]+":{
            "description":"An object holding geo data of this place. Could be of any relevant geo data JSON object definition.",
            "type":"object"
          }
        },
        "properties":{
          "name":{
            "description":"The name of the place",
            "type":"string"
          },
          "rel":{
            "description":"The relationship of the content of the news object to the place",
            "type":"string"
          },
          "scheme":{
            "description":"The identifier of a scheme (= controlled vocabulary) which includes a code for the place",
            "type":"string"
          },
          "code":{
            "description":"The code for the place in a scheme (= controlled vocabulary) which is identified by the scheme property",
            "type":"string"
          },
          "qcode":{
            "description":"The qcode for the place in a scheme (= controlled vocabulary) which is identified by the scheme property",
            "type":"string"
          },
          "state":{
            "description":"The state for the place",
            "type":"string"
          },
          "group":{
            "description":"The place group",
            "type":"string"
          },
          "country":{
            "description":"The country name",
            "type":"string"
          },
          "world_region":{
            "description":"The world region",
            "type":"string"
          },
          "country_code":{
            "description":"The country code",
            "type":"string"
          },
          "state_code":{
            "description":"The state code",
            "type":"string"
          }
        }
      }
    },
    "subject":{
      "description":"A concept with a relationship to the content",
      "type":"array",
      "items":{
        "type":"object",
        "additionalProperties":false,
        "properties":{
          "name":{
            "description":"The name of the subject",
            "type":"string"
          },
          "code":{
            "description":"The code for the subject in a scheme (= controlled vocabulary) which is identified by the scheme property",
            "type":"string"
          },
          "scheme":{
            "description":"The controlled vocabulary (scheme) identifier",
            "type":"string"
          }
        }
      }
    },
    "byline":{
      "description":"The name(s) of the creator(s) of the content",
      "type":"string",
      "maxLength": 255
    },
    "source":{
      "description":"The source from which the item was ingested",
      "type":"string"
    },
    "slugline":{
      "description":"Short name given to an article that is in production",
      "type":"string"
    },
    "headline":{
      "description":"A brief and snappy introduction to the content, designed to catch the reader\'s attention",
      "type":"string",
      "minLength": 1
    },
    "located":{
      "description":"The name of the location from which the content originates.",
      "type":"string"
    },
    "keywords":{
      "description":"Content keywords",
      "type":"array",
      "items":{
        "type":"string"
      }
    },
    "ednote":{
      "description":"Editor notes",
      "type":"string"
    },
    "renditions":{
      "description":"Wrapper for different renditions of non-textual content of the news object",
      "type":"object",
      "additionalProperties":false,
      "patternProperties":{
        "^[a-zA-Z0-9]+":{
          "description":"A specific rendition of a non-textual content of the news object.",
          "type":"object",
          "additionalProperties":false,
          "properties":{
            "href":{
              "description":"The URL for accessing the rendition as a resource",
              "type":"string",
              "format":"uri"
            },
            "mimetype":{
              "description":"A MIME type which applies to the rendition",
              "type":"string"
            },
            "height":{
              "description":"For still and moving images: the height of the display area measured in pixels",
              "type":"number"
            },
            "width":{
              "description":"For still and moving images: the width of the display area measured in pixels",
              "type":"number"
            },
            "poi":{
              "description":"The point of interest",
              "type":"object",
              "properties":{
                "x":{
                  "description":"The position on the X axis of the PoI",
                  "type":"integer"
                },
                "y":{
                  "description":"The position on the Y axis of the PoI",
                  "type":"integer"
                }
              }
            },
            "media":{
              "description":"Media identifier in Superdesk",
              "type":"string",
              "required" : true
            }
          }
        }
      }
    },
    "associations":{
      "description":"Content of news objects which are associated with this news object.",
      "type":"object",
      "additionalProperties":false,
      "patternProperties":{
        "^[a-zA-Z0-9]+[a-zA-Z0-9-]*":{
          "$ref":"#"
        }
      }
    },
    "extra":{
      "description":"Extra metadata",
      "type":"object",
      "additionalProperties":false,
      "patternProperties":{
        "^[a-zA-Z0-9]+":{
          "description":"Metadata field name"
        }
      }
    },
    "evolvedfrom":{
      "description":"Stores the original published item for which and update was published.",
      "type":"string"
    },
    "attachments":{
      "description":"Wrapper for different attachments of non-textual content of the news object",
      "type":"object",
      "additionalProperties":false,
      "patternProperties":{
        "^[a-zA-Z0-9]+":{
          "description":"A specific attachment of a non-textual content of the news object.",
          "type":"object",
          "additionalProperties":false,
          "properties":{
            "id":{
              "description":"The attachment identifier",
              "type":"string"
            },
            "title":{
              "description":"A title for the link to the attachment resource",
              "type":"string"
            },
            "description":{
              "description":"A description for the link to the attachment resource",
              "type":"string"
            },
            "filename":{
              "description":"The attachment file name",
              "type":"string"
            },
            "mimetype":{
              "description":"A MIME type which applies to the rendition",
              "type":"string"
            },
            "length":{
              "description":"The attachment file size",
              "type":"number"
            },
            "media":{
              "description":"Media identifier in Superdesk",
              "type":"string"
            },
            "href":{
              "description":"The URL for accessing the rendition as a resource",
              "type":"string",
              "format":"uri"
            }
          }
        }
      }
    },
    "charcount":{
      "description":"Number of characters in the item",
      "type":"number"
    },
    "wordcount":{
      "description":"Number of words in the item",
      "type":"number"
    },
    "readtime":{
      "description":"Estimated time (in minutes) to read the body of the item",
      "type":"number"
    },
    "authors":{
      "description":"Authors of the document",
      "type":"array",
      "items":{
        "type":"object",
        "properties":{
          "name":{
            "description":"The full name of the author",
            "type":"string"
          },
          "role":{
            "description":"What the author did for this resource",
            "type":"string"
          },
          "jobtitle":{
            "description":"author\'s Job Title qcode",
            "type":"object",
            "properies":{
              "qcode":{
                "type":"string"
              },
              "name":{
                "type":"string"
              }
            }
          },
          "biography":{
            "description":"Author\'s biography",
            "type":"string"
          },
          "avatar_url":{
            "description":"Author\'s avatar url",
            "type":"string",
            "format":"uri"
          }
        }
      }
    },
    "annotations":{
      "description":"Small messages linked to parts of the body",
      "type":"array",
      "items":{
        "type":"object",
        "additionalProperties":false,
        "properties":{
          "id":{
            "description":"The id of the annotation, same id as used in annotation-id attribute of <span> element in body_html",
            "type":"number"
          },
          "type":{
            "description":"Annotation type",
            "type":"string"
          },
          "body":{
            "description":"Content of the annotation, formatted with HTML",
            "type":"string"
          }
        }
      }
    }
  }
}';

    /**
     * {@inheritdoc}
     */
    public function getFormat()
    {
        return 'ninjs';
    }
}
