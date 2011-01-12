<?php

require_once 'PHPUnit/Framework.php';
require_once("material_id_class.php");

 
class MaterialIdTest extends PHPUnit_Framework_TestCase
{

  // ---------------------------------------------------------------------------
  // Test normalizeISBN
  // ---------------------------------------------------------------------------

  public static function normalizeISBNProvider() {
    return array(
      array('', ''),                             // Normalize empty ISBN number
      array('0', '0'),                           // Normalize single character ISBN number
      array('0-13-014714-1', '0130147141'),      // Normalize correct ISBN number with dashes
      array('0-13-014714-2', '0130147142'),      // Normalize correct ISBN number (with checksum error) with dashes
      array('abe 0 - 83', '083'),                // Normalize ISBN number with dummy characters
      array('X130147141', 'X130147141'),         // X will be removed in all positions except first and last
      array('0X30147141', '030147141'),          // X will be removed in all positions except first and last
      array('01X0147141', '010147141'),          // X will be removed in all positions except first and last
      array('013X147141', '013147141'),          // X will be removed in all positions except first and last
      array('0130X47141', '013047141'),          // X will be removed in all positions except first and last
      array('01301X7141', '013017141'),          // X will be removed in all positions except first and last
      array('013014X141', '013014141'),          // X will be removed in all positions except first and last
      array('0130147X41', '013014741'),          // X will be removed in all positions except first and last
      array('01301471X1', '013014711'),          // X will be removed in all positions except first and last
      array('013014714X', '013014714X'),         // X will be removed in all positions except first and last
      array('x130147141', 'X130147141'),         // x will be removed in all positions except first and last
      array('0x30147141', '030147141'),          // x will be removed in all positions except first and last
      array('01x0147141', '010147141'),          // x will be removed in all positions except first and last
      array('013x147141', '013147141'),          // x will be removed in all positions except first and last
      array('0130x47141', '013047141'),          // x will be removed in all positions except first and last
      array('01301x7141', '013017141'),          // x will be removed in all positions except first and last
      array('013014x141', '013014141'),          // x will be removed in all positions except first and last
      array('0130147x41', '013014741'),          // x will be removed in all positions except first and last
      array('01301471x1', '013014711'),          // x will be removed in all positions except first and last
      array('013014714x', '013014714X'),         // x will be removed in all positions except first and last
    );
  }

  /**
  * @dataProvider normalizeISBNProvider
  */
  public function testNormalizeISBN($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::normalizeISBN($input));
  }


  // ---------------------------------------------------------------------------
  // Test validateISBN
  // ---------------------------------------------------------------------------

  public static function validateISBNProvider() {
    return array(
      array('', 0),                               // Incorrect length
      array('0', 0),                              // Incorrect length
      array('083', 0),                            // Incorrect length
      array('01234567890', 0),                    // Incorrect length
      array('0-13-014714-1', 0),                  // Dashes not allowed (removed by normalization)
      array('0A130147141', 0),                    // Letters not allowed
      array('0130147141', '0130147141'),          // Valid ISBN number with correct checksum
      array('0130147142', 0),                     // Valid ISBN number with incorrect checksum
      array('X130147140', 'X130147140'),          // X valid in first position
      array('X130147141', 0),                     // ... and with incorrect checksum
      array('x130147140', 'x130147140'),          // x also valid in first position
      array('x130147141', 0),                     // ... and with incorrect checksum
      array('0X30147141', 0),                     // X NOT allowed in any other positions
      array('01X0147141', 0),                     // X NOT allowed in any other positions
      array('013X147141', 0),                     // X NOT allowed in any other positions
      array('0130X47141', 0),                     // X NOT allowed in any other positions
      array('01301X7141', 0),                     // X NOT allowed in any other positions
      array('013014X141', 0),                     // X NOT allowed in any other positions
      array('0130147X41', 0),                     // X NOT allowed in any other positions
      array('01301471X1', 0),                     // X NOT allowed in any other positions
      array('0x30147141', 0),                     // x NOT allowed in any other positions
      array('01x0147141', 0),                     // x NOT allowed in any other positions
      array('013x147141', 0),                     // x NOT allowed in any other positions
      array('0130x47141', 0),                     // x NOT allowed in any other positions
      array('01301x7141', 0),                     // x NOT allowed in any other positions
      array('013014x141', 0),                     // x NOT allowed in any other positions
      array('0130147x41', 0),                     // x NOT allowed in any other positions
      array('01301471x1', 0),                     // x NOT allowed in any other positions
      array('013014715X', '013014715X'),          // X also possible in checksum - here with correct checksum
      array('013014711X', 0),                     // ... and here with incorrect checksum
      array('013014715x', '013014715x'),          // x also possible in checksum - here with correct checksum
      array('013014711x', 0),                     // ... and here with incorrect checksum
    );
  }

  /**
  * @dataProvider validateISBNProvider
  */
  public function testValidateISBN($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::validateISBN($input));
  }


  // ---------------------------------------------------------------------------
  // Test normalizeEAN
  // ---------------------------------------------------------------------------

  public static function normalizeEANProvider() {
    return array(
      array('', ''),                              // Normalize empty EAN number
      array('0', '0'),                            // Normalize single character EAN number
      array('9-781565-927094', '9781565927094'),  // Normalize correct EAN number with dashes
      array('9-781565-927091', '9781565927091'),  // Normalize correct EAN number (with checksum error) with dashes
      array('abe 0 - 83', '083'),                 // Normalize EAN number with dummy characters
    );
  }

  /**
  * @dataProvider normalizeEANProvider
  */
  public function testNormalizeEAN($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::normalizeEAN($input));
  }


  // ---------------------------------------------------------------------------
  // Test validateEAN
  // ---------------------------------------------------------------------------

  public static function validateEANProvider() {
    return array(
      array('', 0),                            // Incorrect length
      array('0', 0),                           // Incorrect length
      array('083', 0),                         // Incorrect length
      array('01234567890', 0),                 // Incorrect length
      array('9-781565-927094', 0),             // Dashes not allowed (removed by normalization)
      array('9A781565927094', 0),              // Letters not allowed
      array('9781565927094', '9781565927094'),     // Valid EAN number with correct checksum
      array('9781565927091', 0),               // Valid EAN number with incorrect checksum
    );
  }

  /**
  * @dataProvider validateEANProvider
  */
  public function testValidateEAN($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::validateEAN($input));
  }


  // ---------------------------------------------------------------------------
  // Test normalizeISSN
  // ---------------------------------------------------------------------------

  public static function normalizeISSNProvider() {
    return array(
      array('', ''),                             // Normalize empty ISSN number
      array('0', '0'),                           // Normalize single character ISSN number
      array('0378-5955', '03785955'),            // Normalize correct ISSN number with dashes
      array('0378-5951', '03785951'),            // Normalize correct ISSN number (with checksum error) with dashes
      array('abe 0 - 83', '083'),                // Normalize ISSN number with dummy characters
      array('1234567', '01234567'),              // Normalize 7 digit ISSN number
      array('123-4567', '01234567'),             // Normalize 7 digit ISSN number with dashes
      array('X3785955', '3785955'),              // X will be removed in all positions except last
      array('0X785955', '0785955'),              // X will be removed in all positions except last
      array('03X85955', '0385955'),              // X will be removed in all positions except last
      array('037X5955', '0375955'),              // X will be removed in all positions except last
      array('0378X955', '0378955'),              // X will be removed in all positions except last
      array('03785X55', '0378555'),              // X will be removed in all positions except last
      array('037859X5', '0378595'),              // X will be removed in all positions except last
      array('0378595X', '0378595X'),             // X will be removed in all positions except last
      array('x3785955', '3785955'),              // x will be removed in all positions except last
      array('0x785955', '0785955'),              // x will be removed in all positions except last
      array('03x85955', '0385955'),              // x will be removed in all positions except last
      array('037x5955', '0375955'),              // x will be removed in all positions except last
      array('0378x955', '0378955'),              // x will be removed in all positions except last
      array('03785x55', '0378555'),              // x will be removed in all positions except last
      array('037859x5', '0378595'),              // x will be removed in all positions except last
      array('0378595x', '0378595X'),             // x will be removed in all positions except last
    );
  }

  /**
  * @dataProvider normalizeISSNProvider
  */
  public function testNormalizeISSN($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::normalizeISSN($input));
  }


  // ---------------------------------------------------------------------------
  // Test validateISSN
  // ---------------------------------------------------------------------------

  public static function validateISSNProvider() {
    return array(
      array('', 0),                               // Incorrect length
      array('0', 0),                              // Incorrect length
      array('083', 0),                            // Incorrect length
      array('01234567890', 0),                    // Incorrect length
      array('0378-5955', 0),                      // Dashes not allowed (removed by normalization)
      array('0378A5955', 0),                      // Letters not allowed
      array('03785955', '03785955'),              // Valid ISSN number with correct checksum
      array('03785951', 0),                       // Valid ISSN number with incorrect checksum
      array('0378195X', '0378195X'),              // Valid ISSN number with X as correct checksum
      array('0378295X', 0),                       // Valid ISSN number with X as incorrect checksum
      array('0378195x', 0),                       // Valid ISSN number with x as incorrect checksum
      array('0378295x', 0),                       // Valid ISSN number with x as incorrect checksum
    );
  }

  /**
  * @dataProvider validateISSNProvider
  */
  public function testValidateISSN($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::validateISSN($input));
  }


  // ---------------------------------------------------------------------------
  // Test normalizeFaust
  // ---------------------------------------------------------------------------

  public static function normalizeFaustProvider() {
    return array(
      array('28562098', '28562098'),            // Valid Faust number - already validated
      array('2 856 209 8', '28562098'),         // Valid Faust number with spaces
      array('2-856-209-8', '28562098'),         // Valid Faust number with spaces
      array('2-8a5b6f-e2h0j9-8', '28562098'),   // Valid Faust number with spaces
      array('20689911', '20689911'),            // Invalid Faust number
    );
  }

  /**
  * @dataProvider normalizeFaustProvider
  */
  public function testNormalizeFaust($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::normalizeFaust($input));
  }


  // ---------------------------------------------------------------------------
  // Test validateFaust
  // ---------------------------------------------------------------------------

  public static function validateFaustProvider() {
    return array(
      array('28562098', '28562098'),             // Valid Faust number
      array('28567782', '28567782'),             // Valid Faust number
      array('28567677', '28567677'),             // Valid Faust number
      array('21321753', '21321753'),             // Valid Faust number
      array('20689919', '20689919'),             // Valid Faust number
      array('20689911', 0),                      // Invalid Faust number - wrong checksum
    );
  }

  /**
  * @dataProvider validateFaustProvider
  */
  public function testValidateFaust($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::validateFaust($input));
  }


  // ---------------------------------------------------------------------------
  // Test convertISBNToEAN
  // ---------------------------------------------------------------------------

  public static function convertISBNToEANProvider() {
    return array(
      array('', 0),                           // Empty string
      array('0', 0),                          // Single digit
      array('083', 0),                        // 3 digits
      array('0-13-014714-1', 0),              // With dashes
      array('0130147141', '9780130147141'),   // Valid ISBN number - correct checksum
      array('0130147142', '9780130147141'),   // Valid ISBN number - incorrect checksum (is ignored)
      array('1565927095', '9781565927094'),   // Valid ISBN number - correct checksum
    );
  }

  /**
  * @dataProvider convertISBNToEANProvider
  */
  public function testConvertISBNToEAN($input, $expected_result) {
    $this->assertEquals($expected_result, materialId::convertISBNToEAN($input));
  }

}
?>