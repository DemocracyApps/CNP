# Story Input, Composers and the Composer Cycle

All story input is done via a Composer whose behavior is defined by a Composer Specification. In the simplest case the composer just provides a map between inputs (e.g., data in columns of a spreadsheet) and outputs (a set of elements and the relations between them). In a more complex case, the specification tells the system how to create an interactive storytelling experience for the user.

## Composer Specification

A composer specification is a JSON file that contains one or more of the following sections: elements, relations and input. A single specification may contain all 3 or it may contain a subset. Specifications can be combined by setting a 'baseSpecification' variable to the ID of another specification that should be merged in. Multiple definitions of sections are allowed, but those loaded later supersede previous ones. In order to be valid for actually driving creation of a story, a final merged specification must contain all 3 sections.

### Element and Relations

Elements provide a list of the elements that will be created, relations specify the relations between them. More documentation on these to be done later. Here is a simple example of a JSON file containing only these two sections:

```
{
    "name": "Global Giving Composer Specification",
    "version": "0.01",
    // The ID here is used by input specs to refer to these elements. IDs must be unique within this file.
    "elements": [
        {
            "id":"Story",
            "required": false,
            "type":"StoryElement"
        },
        {
            "id":"Topic",
            "type":"Tag"
        },
        {
            "id":"Organization",
            "type":"StoryElement"
        }
    ],
    "relations": [
        {
            "type": "Causes",
            "from": "Organization",
            "to":   "Story"
        },
        {
            "type": "Tags",
            "from": "Topic",
            "to":   "Story"
        }
    ]
}
```

### Input

The input section specifies how to map data to fill the structure defined by elements and relations. Inputs can also specify ways to generate structures beyond those defined statically.

The first thing the input section defines is the 'inputType'. Right now there are 2 types: csv-simple and auto-interactive. The csv-simple type just maps columns of a CSV file to the elements in the elements section. Here is an example for the specification above (assuming that the ID of the spec above is 1):

```
{
    "name": "Global Giving CSV Input Composer",
    "version": "0.01",
    "baseSpecificationId": 1,
    "input": {
        "inputType": "csv-simple",
        "map": {
            "skip": 1,
            "columnMap": [
                {
                    "column": 3,
                    "use":"title",
                    "columnType": "text"
                },
                {
                    "column": 2,
                    "use":"element",
                    "elementId": "Story",
                    "columnType": "text"
                },
                {
                    "column": 5,
                    "use":"element",
                    "elementId": "Topic",
                    "columnType": "text"
                },
                {
                    "column": 4,
                    "use":"element",
                    "elementId": "Organization",
                    "columnType": "text"
                }
            ]
        }
    }
}
```
The auto-interactive type is much more complex since it is designed to not only map input to the elements, but also to drive automatic creation of an interactive input form.

Here is a simple example (again applicable to the base specification above, assuming it has ID=1) that just creates text areas and text fields to input the data that will be mapped to the elements:

```
{
    "name": "Global Giving Auto-Interactive Input Composer",
    "version": "0.01",
    "baseSpecificationId": 1,
    "input": {
        "inputType": "auto-interactive",
        "map": [ 
            {
                "id":"1orso",
                "use":"element",
                "elementId":"Story",
                "inputType":"textarea",
                "prompt":"Please tell a story about a time when a person or an organization tried to help someone or change something in your community."
            },
            {
                "id":"2",
                "use":"summary",
                "inputType":"textarea",
                "prompt":"Write a summary for this story"
            },
            {
                "use":"pagebreak"
            },
            {
                "id":"3",
                "use":"title",
                "inputType":"text",
                "prompt":"Give your story a title"
            },
            {
                "id":"4",
                "use":"element",
                "elementId": "Topic",
                "inputType":"text",
                "prompt":"What is this story about?"
            },
            {
                "id":"anythingIwant",
                "use":"element",
                "elementId": "Organization",
                "inputType":"text",
                "prompt":"Name the organization or group most involved in what happened."
            }
       ]
    }
}

```
I think we want to allow the input to optionally define an *anchor*. If no anchor is defined, a Story element will be created and all elements will be placed in an *is-part-of* relation to it. If an anchor *is* defined, then no Story element is created. Have to think about whether (a) the title and summary uses work and (b) whether we have create *is-part-of* relations [probably not, on the latter].


## Composer

The only thing required to create a composer is the ID of a composer spec (to actually use, the spec must resolve to a valid spec with all 3 sections). 

A composer may optionally define a *referent* and *referentRelation*. If no *referent* is defined, the anchor element of the created story remains disconnected from anything but the internal elements of the story. If it is defined, the the anchor is connected to the referent via a referentRelation relation.


## Composer Cycle

At a high level, the cycle is very simple. On create, we load a Composer using the specified ID and use it to set up the input form. If input potentially requires multiple trips between client and server, we create structures to track progress. On store, we pull the relevant inputs out of the form data into the Composer and then process the input to generate the nodes and edges of the story. In the case of an auto-generated input form, we may cycle through the creating the input form and extracting data multiple times until all inputs have been received (i.e., if the auto-generated input form is more than a single page).



