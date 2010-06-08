<?php
/**
 * Geography v0.0.2 10/12/2006
 * Simon Holywell
 * http://www.simonholywell.com/
 *
 * You may use this class as long as all
 * copyright information remains intact.
 * Be kind and stick a link to simonholywell.com
 * on your website.
 */
class Geography {

	/**
	 * Mean radius of the earth
	 *
	 * @var float
	 */
    private $earthRadius = 6371;  //mean radius in KM

    /* http://www.movable-type.co.uk/scripts/LatLongVincenty.html
    The most accurate and widely used globally-applicable model for the earth ellipsoid is WGS-84, used in this script. Other ellipsoids offering a better fit to the local geoid include Airy (1830) in the UK, International 1924 in much of Europe, Clarke (1880) in Africa, and GRS-67 in South America. America (NAD83) and Australia (GDA) use GRS-80, functionally equivalent to the WGS-84 ellipsoid.
  	WGS-84 	a = 6 378 137 m (2 m) 	b = 6 356 752.3142 m 	f = 1 / 298.257223563
  	GRS-80 	a = 6 378 137 m 	b = 6 356 752.3141 m 	f = 1 / 298.257222101
  	Airy (1830) 	a = 6 377 563.396 m 	b = 6 356 256.909 m 	f = 1 / 299.3249646
  	Intl 1924 	a = 6 378 388 m 	b = 6 356 911.946 m 	f = 1 / 297
  	Clarke (1880) 	a = 6 378 249.145 m 	b = 6 356 514.86955 m 	f = 1 / 293.465
  	GRS-67 	a = 6 378 160 m 	b = 6 356 774.719 m 	f = 1 / 298.25 */
	/**
	 * Major Semiax in metres.
	 *
	 * @var float
	 */
    private $majorSemiax = 6378137;
    /**
     * Minor Semiax in metres.
     *
     * @var float
     */
    private $minorSemiax = 6356752.3141;
    /**
     * Calculation method switch.
     * Used to action a variable method.
     *
     * @var string
     */
    protected $calculationMethod = 'vincenty';

    /**
     * Set the method to be used for calculating
     * the distance between two coordinates.
     *
     * Defaults to Vincenty's
     *
     * v  - Vincenty's formula
     * gc - Simplified Great Circle formula
     * h  - Haversine Formula
     * cs - Cosine Law Formula
     *
     * Vincenty's is the most accurate but also
     * contains quite involved calculations so if
     * you don't need the highest accuracy it maybe
     * best to choose a less intensive method.
     *
     * @param string $method
     */
    public function setCalculationMethod($method) {
        switch($method) {
            case 'v':
                $this->calculationMethod = 'vincenty';
               break;
            case 'gc':
                $this->calculationMethod = 'greatCircle';
               break;
            case 'h':
                $this->calculationMethod = 'haversine';
               break;
			case 'cs':
                $this->calculationMethod = 'cosineLaw';
               break;
            default:
                $this->calculationMethod = 'vincenty';
               break;
        }
    }

    /**
     * Supply a coordinate in Degrees, Minutes
     * and Seconds that you wish to be converted
     * to a decimal representation.
     *
     * The default format for input is: 52 12 17N
     * but you can supply a custom regex to $format
     * for the function to parse different input
     * formats.
     *
     * @param string $coord
     * @param string $format
     * @return float
     */
    public function convertToDecimal($coord, $format = false) {
        if(!$format)
            $format = '([0-9]{1,3}) ([0-9]{1,2}) ([0-9]{1,2}[.]{0,1}[0-9]*)([N,S,E,W])';

        preg_match('/'.$format.'/', $coord, $matches);
        $degrees = $matches[1];
        $minutes = $matches[2] * (1/60);
        $seconds = $matches[3] * (1/60 * 1/60);

        $coordinate = '';
        if(isset($matches[4]) and (
            $matches[4] == 'S' or
            $matches[4] == 'W')
          ) {
               $coordinate .= '-';
           }

        $coordinate .= $degrees + $minutes + $seconds;
        return (float)$coordinate;
    }

    /**
     * Helper function to convert decimal
     * latitudes into Degrees, Minutes,
     * Seconds notation.
     *
     * The default output format is: 52 12 17N
     * but you can supply a custom sprintf format
     * to $output_format for the function to parse
     * different output formats.
     *
     * @param float $coord
     * @param string $output_format
     * @return string
     */
    public function convertLatToDMS($coord, $output_format = false) {
        return $this->convertToDMS($coord, 'lat', $output_format);
    }

    /**
     * Helper function to convert decimal
     * longitudes into Degrees, Minutes,
     * Seconds notation.
     *
     * The default output format is: 52 12 17E
     * but you can supply a custom sprintf format
     * to $output_format for the function to parse
     * different output formats.
     *
     * @param float $coord
     * @param string $output_format
     * @return string
     */
    public function convertLongToDMS($coord, $output_format = false) {
        return $this->convertToDMS($coord, 'long', $output_format);
    }

    /**
     * Convert decimal coordinate to
     * Degrees, Minutes, Seconds notation.
     *
     * Supply $direction with either 'lat' or
     * 'long' for the relevant compass point
     * to be appended to the output.
     *
     * Give $output_format a custom sprintf
     * format if you would like a custom output
     * format.
     *
     * @param float $coord
     * @param string $direction
     * @param string $output_format
     * @return string
     */
    public function convertToDMS($coord, $direction = false, $output_format = false) {
        if(!$output_format)
            $output_format = '%d %d %F%s';

        $degrees = (integer)$coord;
        $compass = '';
        if($direction == 'lat') {
            if($degrees < 0)
                $compass = 'S';
            elseif($degrees > 0)
                $compass = 'N';
        }elseif($direction == 'long') {
            if($degrees < 0)
                $compass = 'W';
            elseif($degrees > 0)
                $compass = 'E';
        }
        $minutes = $coord - $degrees;
        if($minutes < 0)
            $minutes -= (2 * $minutes);
        if($degrees < 0)
            $degrees -= (2 * $degrees);

        $minutes = $minutes * 60;
        $seconds = $minutes - (integer)$minutes;
        $minutes = (integer)$minutes;
        $seconds = (float)$seconds * 60;

        $coordinate = sprintf($output_format, $degrees, $minutes, $seconds, $compass);
        return $coordinate;
    }

    /**
     * Calculate the distance between two
     * points using the Great Circle
     * formula.
     *
     * Supply instances of the coordinate class.
     *
     * http://www.ga.gov.au/geodesy/datums/distance.jsp#circle
     *
     * @param object $p1
     * @param object $p2
     * @return float
     */
    private function greatCircle($p1, $p2) {
        $degrees    = rad2deg(acos(sin($p1->latRadian) * sin($p2->latRadian) + cos($p1->latRadian) * cos($p2->latRadian) * cos($p2->longRadian - $p1->longRadian)));
        $arcMinutes = 60 * $degrees;
        $metres     = $arcMinutes * 1852;
        return number_format($metres, 3, '.', ''); //round to 1mm precision;
    }

    /**
     * Calculate the distance between two
     * points using the Haversine formula.
     *
     * Supply instances of the coordinate class.
     *
     * http://en.wikipedia.org/wiki/Haversine_formula
     *
     * @param object $p1
     * @param object $p2
     * @return float
     */
	private function haversine($p1, $p2) {
		$deltaLat  = $p2->latRadian - $p1->latRadian;
		$deltaLong = $p2->longRadian - $p1->longRadian;
		$a = sin($deltaLat / 2) * sin($deltaLat / 2) + cos($p1->latRadian) * cos($p2->latRadian) * sin($deltaLong / 2) * sin($deltaLong / 2);
		$c = 2 * atan2(sqrt($a), sqrt(1 - $a));
		$d = $this->earthRadius * $c * 1000;
		return number_format($d, 3, '.', '');
	}

	/**
	 * Calculate the distance between two
     * points using the Cosine Law formula.
     *
     * Supply instances of the coordinate class.
     *
     * http://en.wikipedia.org/wiki/Cosine_law
     *
     * @param object $p1
     * @param object $p2
     * @return float
     */
	private function cosineLaw($p1, $p2) {
		$d = acos(sin($p1->latRadian) * sin($p2->latRadian) + cos($p1->latRadian) * cos($p2->latRadian) * cos($p2->longRadian - $p1->longRadian)) * $this->earthRadius;
		return number_format($d * 1000, 3, '.', '');
	}

    /**
     * Calculate the distance between two
     * points using Vincenty's formula.
     *
     * Supply instances of the coordinate class.
     *
     * http://www.movable-type.co.uk/scripts/LatLongVincenty.html
     *
     * @param object $p1
     * @param object $p2
     * @return float
     */
    private function vincenty($p1, $p2) {
        $a     = $this->majorSemiax;
        $b     = $this->minorSemiax;
        $f     = ($a - $b) / $a;  //flattening of the ellipsoid
        $L     = $p2->longRadian - $p1->longRadian;  //difference in longitude
        $U1    = atan((1 - $f) * tan($p1->latRadian));  //U is 'reduced latitude'
        $U2    = atan((1 - $f) * tan($p2->latRadian));
        $sinU1 = sin($U1);
        $sinU2 = sin($U2);
        $cosU1 = cos($U1);
        $cosU2 = cos($U2);

        $lambda  = $L;
        $lambdaP = 2 * pi();
        $i = 20;

        while(abs($lambda - $lambdaP) > 1e-12 and
              --$i > 0) {
            $sinLambda = sin($lambda);
            $cosLambda = cos($lambda);
            $sinSigma  = sqrt(($cosU2 * $sinLambda) * ($cosU2 * $sinLambda) + ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda) * ($cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda));

            if($sinSigma == 0)
                return 0;  //co-incident points

            $cosSigma   = $sinU1 * $sinU2 + $cosU1 * $cosU2 * $cosLambda;
            $sigma      = atan2($sinSigma, $cosSigma);
            $sinAlpha   = $cosU1 * $cosU2 * $sinLambda / $sinSigma;
            $cosSqAlpha = 1 - $sinAlpha * $sinAlpha;
            $cos2SigmaM = $cosSigma - 2 * $sinU1 * $sinU2 / $cosSqAlpha;
            if(is_nan($cos2SigmaM))
                $cos2SigmaM = 0;  //equatorial line: cosSqAlpha=0 (6)
            $c = $f / 16 * $cosSqAlpha * (4 + $f * (4 - 3 * $cosSqAlpha));
            $lambdaP = $lambda;
            $lambda = $L + (1 - $c) * $f * $sinAlpha * ($sigma + $c * $sinSigma * ($cos2SigmaM + $c * $cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM)));
        }

        if($i == 0)
            return false;  //formula failed to converge

        $uSq = $cosSqAlpha * ($a * $a - $b * $b) / ($b * $b);
        $A   = 1 + $uSq / 16384 * (4096 + $uSq * (-768 + $uSq * (320 - 175 * $uSq)));
        $B   = $uSq / 1024 * (256 + $uSq * (-128 + $uSq * (74 - 47 * $uSq)));
        $deltaSigma = $B * $sinSigma * ($cos2SigmaM + $B / 4 * ($cosSigma * (-1 + 2 * $cos2SigmaM * $cos2SigmaM) - $B / 6 * $cos2SigmaM * (-3 + 4 * $sinSigma * $sinSigma) * (-3 + 4 * $cos2SigmaM * $cos2SigmaM)));
        $d = $b * $A * ($sigma - $deltaSigma);
        return number_format($d, 3, '.', ''); //round to 1mm precision
    }

    /**
     * Convert Metres to Kilometres.
     *
     * @param float $metres
     * @return float
     */
    public function mToKm($metres) {
        return number_format($metres / 1000, 3, '.', '');
    }

    /**
     * Convert Metres to Nautical Miles
     *
     * @param float $metres
     * @return float
     */
    public function mToNM($metres) {
        return number_format($metres / 1852, 3, '.', '');
    }

    /**
     * Convert Metres to Miles
     *
     * @param float $metres
     * @return float
     */
	public function mToM($metres) {
		return number_format($metres * 0.000621371192, 3, '.', '');
	}

	/**
	 * Get distance in metres between the
	 * supplied longitudes and latitudes.
	 *
	 * You must supply the latitudes and
	 * longitudes as decimals.
	 *
	 * @param float $latitude1
	 * @param float $longitude1
	 * @param float $latitude2
	 * @param float $longitude2
	 * @return float
	 */
    public function getDistance($latitude1, $longitude1, $latitude2, $longitude2, $calculation_method = false) {
        $point1 = new Coordinate($latitude1, $longitude1);
        $point2 = new Coordinate($latitude2, $longitude2);

        if($calculation_method)
        	$this->setCalculationMethod($calculation_method);

        //use a variable function to decide the calculation method
        $function = $this->calculationMethod;
        return $this->$function($point1, $point2);
    }
}

/**
 * Store information about a coordinate.
 */
class Coordinate {
	/**
	 * Latitude in decimal form
	 *
	 * @var float
	 */
    public $latitude    = 0;
    /**
     * Longitude in decimal form
     *
     * @var float
     */
    public $longitude   = 0;
    /**
     * Latitude as Radians
     *
     * @var float
     */
    public $latRadian   = 0;
    /**
     * Longitude as Radians
     *
     * @var float
     */
    public $longRadian  = 0;
    /**
     * Name of the coordinate
     *
     * @var string
     */
    public $name        = '';
    /**
     * Description of the coordinate
     *
     * @var string
     */
    public $description = '';

    /**
     * Stick all the information into
     * the object upon instantiation.
     * Converting the longitude and
     * latitude into radians in the
     * process.
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $name
     * @param string $description
     */
    public function __construct($latitude, $longitude, $name = '', $description = '') {
        $this->latitude    = $latitude;
        $this->longitude   = $longitude;
        $this->name        = $name;
        $this->description = $description;

        $this->latRadian   = deg2rad($this->latitude);
        $this->longRadian  = deg2rad($this->longitude);
    }
}
?>