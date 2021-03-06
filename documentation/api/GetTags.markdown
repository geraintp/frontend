Open Photo API / Get Photos
=======================
#### OpenPhoto, a photo service for the masses

----------------------------------------

1. [Purpose][purpose]
1. [Endpoint][endpoint]
1. [Parameters][parameters]
1. [Examples][examples]
  * [Curl][example-curl]
  * [PHP][example-php]
1. [Response][response]
  * [Sample][sample]

----------------------------------------

<a name="purpose"></a>
### Purpose of the Get Tags API

Use this API to get a user's tags.

----------------------------------------

<a name="endpoint"></a>
### Endpoint

_Authentication: required_

    GET /tags.json

<a name="parameters"></a>
### Parameters

_None_

----------------------------------------

<a name="examples"></a>
### Examples

<a name="example-curl"></a>
#### Command line curl

    curl "http://jmathai.openphoto.me/tags.json"

<a name="example-php"></a>
#### PHP

    $ch = curl_init('http://jmathai.openphoto.me/tags.json');
    curl_exec($ch);

----------------------------------------

<a name="response"></a>
### Response

The response is in a standard [response envelope][Envelope].

* _message_, A string describing the result. Don't use this for anything but reading.
* _code_, _200_ on success
* _result_, An array of [Tag][Tag] objects

<a name="sample"></a>
#### Sample

    {
      "message":"",
      "code":200,
      "result":
      [
        {
          "id": "mountain",
          "count": 1
        },
        {
          "id": "jaisen",
          "count": 10,
          "email": "jaisen@jmathai.com"
        },
        {
          "id": "New York",
          "count": 9,
          "latitude": 12.3456,
          "longitude": 78.9012
        },
        {
          "id": "Sunnyvale",
          "count":23 
          "latitude": 13.579,
          "longitude": 24.68
        },
        ....
      ]
    }

[Envelope]: api/Envelope.markdown
[Tag]: schemas/Tag.markdown
[purpose]: #purpose
[endpoint]: #endpoint
[parameters]: #parameters
[examples]: #examples
[example-curl]: #example-curl
[example-php]: #example-php
[response]: #response
[sample]: #sample

