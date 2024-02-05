Do at least ONE of the following tasks: refactor is mandatory. Write tests is optional, will be good bonus to see it. 
Upload your results to a Github repo, for easier sharing and reviewing.

Thank you and good luck!



Code to refactor
=================
1) app/Http/Controllers/BookingController.php
2) app/Repository/BookingRepository.php

Code to write tests (optional)
=====================
3) App/Helpers/TeHelper.php method willExpireAt
4) App/Repository/UserRepository.php, method createOrUpdate


----------------------------

What I expect in your repo:

X. A readme with:   Your thoughts about the code. What makes it amazing code. Or what makes it ok code. Or what makes it terrible code. How would you have done it. Thoughts on formatting, structure, logic.. The more details that you can provide about the code (what's terrible about it or/and what is good about it) the easier for us to assess your coding style, mentality etc

And 

Y.  Refactor it if you feel it needs refactoring. The more love you put into it. The easier for us to asses your thoughts, code principles etc


IMPORTANT: Make two commits. First commit with original code. Second with your refactor so we can easily trace changes. 


NB: you do not need to set up the code on local and make the web app run. It will not run as its not a complete web app. This is purely to assess you thoughts about code, formatting, logic etc


===== So expected output is a GitHub link with either =====

1. Readme described above (point X above) + refactored code 
OR
2. Readme described above (point X above) + refactored core + a unit test of the code that we have sent

Thank you!

===== thoughts about the code =====
- Overall the code is okay except for the points below
- Check for errors early and return immediately so it will not pass other if...else codes.
- I would like to emphasize that all request params in the BookingController has no validate which should be done to avoid or minimize errors.
- Use ternary operator as much as possible for a more readable code.
- Remove comments in code if they are not needed such as old code that is not being used. 
===== Notes regarding refactored code =====
1) app/Http/Controllers/BookingController.php
   a. PHP variables should be camel case
   b. Store env values in a variable
   c. use guard clause to avoid going into into other codes if you can return it immediately.
   d. use validation to check valid parameter values
   e. depending on version, don't use deprecated codes
   f. use ternary operator as much as possible
2) app/Repository/BookingRepository.php
   a. add logger to the constructor
   b. use type hinting in the method params
   c. PHP variables should be camel case

===== Location of code test =====
3) test/app/unit/HelpersTest.php
4) test/app/unit/UserRepositoryTest.php