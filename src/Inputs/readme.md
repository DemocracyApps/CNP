## Story Input, Collectors and the Collector Cycle

All story input is done via a Collector whose behavior is defined by a Collector Specification. In the simplest case the collector just provides a map between inputs (e.g., data in columns of a spreadsheet) and outputs (a set of denizens and the relations between them). In a more complex case, the specification tells the system how to create an interactive storytelling experience for the user.

### Collector Specification

A collector specification contains 3 parts: elements, relations and input.

### Collector Cycle

At a high level, the cycle is very simple. On create, we load a Collector using the specified ID and use it to set up the input form. On store, we pull the relevant inputs out of the form data into the Collector and then process the input to 
generate the nodes and edges of the story. In the case of an auto-generated input form, we may cycle through the creating the input form and extracting data multiple times until all inputs have been received (i.e., if the auto-generated input form is more than a single page).


