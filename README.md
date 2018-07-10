# City Pantry - Backend/PHP coding challenge

City Pantry needs a program to search its database of vendors for menu items available given day, time, location and a headcount. Each vendor has a name, postcode and a maximum headcount it can serve at any time. Menu items each have a name, list of allergies and notice period needed for placing an order.

## Requirements

Your task is to write a console application that takes four input parameters:

```
filename - input file with the vendors data
day      - delivery day (dd/mm/yy)
time     - delivery time in 24h format (hh:mm)
location - delivery location (postcode without spaces, e.g. NW43QB)
covers   - number of people to feed
```
 
and prints a list of item names and item-specific allergens available to order given the following rules:

1. Vendor must be able to deliver to the requested location, e.g. vendor with a postcode starting "NW" can only deliver to a postcode starting with "NW", etc.
2. Vendor must be able to serve the requested number of covers
3. Item notice period must be less or equal to the difference between the search time and the actual time of the delivery

The input file is provided in the following EBNF-like text format:
    
```
vendors      = { vendor, new line, items, new line } EOF
vendor       = name, ";", postcode, ";", max covers
items        = { item, new line }
item         = name, ";", allergies, ";", advance time
name         = /[A-Za-z ]*/
postcode     = /[A-Za-z][A-Za-z0-9]*/
max covers   = /\d*/
advance time = /\d\dh/
new line     = "\r\n"
```

You may use [this example input file](./example-input).    

Calling your application with the example input on 20/10/15 at 12 AM with the parameters `21/10/15 11:00 NW43QB 20` should print the following lines:

```
Breakfast;gluten,eggs
````
    
Using the same input on 20/10/15 at 12 AM with the parameters `24/10/15 11:00 NW43QB 20` should print the following lines:

```
Premium meat selection;;
Breakfast;gluten,eggs
``` 

## Additional Notes

Structure your code as if this was a real, production application. You may however choose to provide simplified implementations for some aspects (e.g. in-memory persistence instead of a full database if you think any persistence is required at all).

State any assumptions you make as comments in the codebase. If any aspects of the above requirements are unclear then please also state, as comments in the source, your interpretation of the requirement.

The purpose of the exercise is to evaluate your approach to software development covering among other things elements of Object Oriented Design, Software Design Patterns and Testing. This exercise is not time bounded.

Complete the exercise in PHP. You are free to implement any mechanism for feeding input into your solution (for example, using hard coded data within unit test). You should provide sufficient evidence that your solution is complete by, as a minimum, indicating that it works correctly against the supplied test data.

Please, do not use any external libraries to solve this problem, but you may use external libraries or tools for building or testing purposes. Specifically, you are encouraged to use PHPUnit to test your code.
