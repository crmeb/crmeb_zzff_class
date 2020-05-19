babel.js
========

Javascript microframework for multilanguage purpose. It replaces strings from a json-language file
to the innerHTML to the element with the id of the json key.

Usage:
------


	<script src="babel.js"></script>


API
---


	babel.config({
		lang: "en", // default language
		dir: "lang", // the directory of the language files
		extension: "json" // extension of the language files
	})

	babel.activate("en"); // loads and activates a language


Language Files
--------------

Language files are simple JSON files:

```
{
	heading: "Heading",
	intro: "Introduction Text"
}
```

How it works
------------

`babel.activate()` with the default language is run on DOMContentLoaded.
`babel.activate()` works as follows: For each key in the 
json: `document.getElementById(key).innerHTML = value`
