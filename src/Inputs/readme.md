# Story Input, Collectors and the Collector Cycle

All story input is done via a Collector whose behavior is defined by a Collector Specification. In the simplest case the collector just provides a map between inputs (e.g., data in columns of a spreadsheet) and outputs (a set of denizens and the relations between them). In a more complex case, the specification tells the system how to create an interactive storytelling experience for the user.

## Collector Specification

A collector specification is a JSON file that contains one or more of the following sections: elements, relations and input. A single specification may contain all 3 or it may contain a subset. Specifications can be combined by setting a 'baseSpecification' variable to the ID of another specification that should be merged in. Multiple definitions of sections are allowed, but those loaded later supersede previous ones. In order to be valid for actually driving creation of a story, a final merged specification must contain all 3 sections.

### Element and Relations

Elements provide a list of the denizens that will be created, relations specify the relations between them. More documentation on these to be done later. Here is a simple example of a JSON file containing only these two sections:

```
{
    "name": "Global Giving Collector Specification",
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
}```

### Input

The input section specifies how to map data to fill the structure defined by elements and relations. Inputs can also specify ways to generate structures beyond those defined statically.


## Collector Cycle

At a high level, the cycle is very simple. On create, we load a Collector using the specified ID and use it to set up the input form. On store, we pull the relevant inputs out of the form data into the Collector and then process the input to 
generate the nodes and edges of the story. In the case of an auto-generated input form, we may cycle through the creating the input form and extracting data multiple times until all inputs have been received (i.e., if the auto-generated input form is more than a single page).


