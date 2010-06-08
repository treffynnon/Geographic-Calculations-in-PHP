#[Geographic Calculations in PHP][originalurl]
Recently I have been involved with a project that maps yachts during an ocean race, which got me thinking about basic calculations and conversions that would be useful to fellow developers. I envisage this being useful in projects leveraging Google or Yahoo maps. For the moment the class performs the following functions:

* Calculate the distance between two coordinate points on the earth's surface (using Vincenty, Haversine, Great Circle or The Cosine Law)
* Conversion between units (metres to kilometres, nautical miles and miles).
* Convert coordinate notation (decimals to degrees, minutes & seconds and back again).
	
That is all you get for the moment, but it is pretty powerful for getting a "as the crow flies" distance between two coordinates. Vincenty's formula is the most accurate method for calculation, but it is also the most processor intensive.

The attached file package contains an extensive set of "api" documentation and PHPDoc comments throughout the class to make customisation and use easier. I have also included a demo php file in there so you can see how it is intended to be used. It should also be pointed out here that this class requires some of the new OO features only available in PHP5, but it could easily be edited to be backwards compatible with PHP4.

[originalurl]: http://blog.simonholywell.com/post/374218456/geographic-calculations-in-php