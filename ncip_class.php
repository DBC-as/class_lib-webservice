<?php
/**
 *
 * This file is part of Open Library System.
 * Copyright © 2009, Dansk Bibliotekscenter a/s,
 * Tempovej 7-11, DK-2750 Ballerup, Denmark. CVR: 15149043
 *
 * Open Library System is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Open Library System is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with Open Library System.  If not, see <http://www.gnu.org/licenses/>.
*/

//==============================================================================


/**
 * ncip - Håndterer kommunication til og fra lokalsystemer, ved brug af ncip protokollen
 *
 * @author Steen L. Frederiksen
 */

require_once("http_wrapper_class.php"); 

/** \brief ncip klasse
*
* ncip klassen håndterer opbygning og fortolkning af 5 typer ncip requests:
*   LookupUser
*   LookupItem
*   LookupRequest
*   CancelRequestItem
*   RenewItem
* ... samt opbygning og fortolkning af 5 typer ncip responses:
*   LookupUserResponse
*   LookupItemResponse
*   LookupRequestResponse
*   CancelRequestItemResponse
*   RenewItemResponse
*
* Ved opbygning af en request/response angives en parameterblok, som angivet herunder.
* Nøjagtig de samme parameterblokke returneres ved fortolkingen af requests/responses.
*
*
* LookupUserRequest:
* ------------------
* Array
* (
*     [Ncip] => LookupUser
*     [FromAgencyId] => DK-190101
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-710100
*     [UserId] => 1231231230
*     [UserPIN] => 1234
*     [UserElementType] => Name Information
*     [LoanedItemsDesired] => 1
*     [RequestedItemsDesired] => 1
* )
*
*
* LookupItemRequest:
* ------------------
* Array
* (
*     [Ncip] => LookupItem
*     [FromAgencyId] => DK-190101
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-710100
*     [UniqueItemId] => Array
*         (
*             [ItemIdentifierValue] => 07197195
*             [UniqueAgencyId] => DK-719999
*         )
*     [ItemElementType] => Bibliographic Description
* )
* 
*   
* LookupRequestRequest:
* ---------------------
* Array
* (
*     [Ncip] => LookupRequest
*     [FromAgencyId] => DK-190101
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-710100
*     [UniqueRequestId] => Array
*         (
*             [RequestIdentifierValue] => 87654321
*             [UniqueAgencyId] => DK-719999
*         )
*     [ItemElementType] => Bibliographic Description
* )
*
*   
* CancelRequestItemRequest:
* -------------------------
* Array
* (
*     [Ncip] => CancelRequestItem
*     [FromAgencyId] => DK-190101
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-710100
*     [UniqueUserId] => Array
*         (
*             [UserIdentifierValue] => 1231231230
*             [UniqueAgencyId] => DK-719999
*         )
*     [UniqueRequestId] => Array
*         (
*             [RequestIdentifierValue] => 87654321
*             [UniqueAgencyId] => DK-718888
*         )
*     [RequestType] => Hold
* )
*
*   
* RenewItemRequest:
* -----------------
* Array
* (
*     [FromAgencyId] => DK-190101
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-710100
*     [UniqueUserId] => Array
*         (
*             [UserIdentifierValue] => 1231231230
*             [UniqueAgencyId] => DK-719999
*         )
*     [UniqueItemId] => Array
*         (
*             [ItemIdentifierValue] => 27175953
*             [UniqueAgencyId] => DK-718888
*         )
* )
*
*
*
*
*
* LookupUserResponse:
* -------------------
* Array
* (
*     [Ncip] => LookupUserResponse
*     [FromAgencyId] => DK-710100
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-190101
*     [UniqueUserId] => Array
*         (
*             [UserIdentifierValue] => 1231231230
*             [UniqueAgencyId] => DK-710100
*         )
*     [RequestedItem] => Array
*         (
*             [0] => Array
*                 (
*                     [UniqueRequestId] => Array
*                         (
*                             [RequestIdentifierValue] => 87654321
*                             [UniqueAgencyId] => DK-710100
*                         )
*                     [RequestType] => Hold
*                     [RequestStatusType] => In Process
*                     [DatePlaced] => 1199972100
*                     [PickupDate] => 1199972100
*                     [PickupExpiryDate] => 1199972100
*                     [ReminderLevel] => 1
*                     [HoldQueuePosition] => 2
*                 )
*         )
*     [LoanedItem] => Array
*         (
*             [0] => Array
*                 (
*                     [UniqueItemId] => Array
*                         (
*                             [ItemIdentifierValue] => 12345678
*                             [UniqueAgencyId] => DK-710100
*                         )
*                     [ReminderLevel] => 1
*                     [DateDue] => 1205694000
*                     [CurrencyCode] => DKK
*                     [MonetaryValue] => 0
*                 )
*         )
*     [GivenName] => Givenname
*     [Surname] => Surname
*     [UnstructuredPersonalUserName] => UnstructuredPersonalUserName
* )
*
*
* LookupItemResponse:
* -------------------
* Array
* (
*     [Ncip] => LookupItemResponse
*     [FromAgencyId] => DK-710100
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-190101
*     [UniqueItemId] => Array
*         (
*             [ItemIdentifierValue] => 12345678
*             [UniqueAgencyId] => DK-710100
*         )
*     [HoldPickupDate] = HoldPickupDate
*     [DateRecalled] = DateRecalled
*     [Author] = Author
*     [AuthorOfComponent] = AuthorOfComponent
*     [BibliographicItemId] = BibliographicItemId
*     [BibliographicRecordId] = BibliographicRecordId
*     [ComponentId] = ComponentId
*     [Edition] = Edition
*     [Pagination] = Pagination
*     [PlaceOfPublication] = PlaceOfPublication
*     [PublicationDate] = PublicationDate
*     [PublicationDateOfComponent] = PublicationDateOfComponent
*     [Publisher] = Publisher
*     [SeriesTitleNumber] = SeriesTitleNumber
*     [Title] = Title
*     [TitleOfComponent] = TitleOfComponent
*     [BibliographicLevel] = BibliographicLevel
*     [SponsoringBody] = SponsoringBody
*     [ElectronicDataFormatType] = ElectronicDataFormatType
*     [Language] = Language
*     [MediumType] = MediumType
* )
* 
* 
* LookupRequestResponse:
* ----------------------
* Array
* (
*     [Ncip] => LookupRequestResponse
*     [FromAgencyId] => DK-710100
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-190101
*     [UniqueRequestId] => Array
*         (
*             [RequestIdentifierValue] => 87654321
*             [UniqueAgencyId] => DK-710100
*         )
*     [HoldQueuePosition] => 2
*     [Author] = Author
*     [AuthorOfComponent] = AuthorOfComponent
*     [BibliographicItemId] = BibliographicItemId
*     [BibliographicRecordId] = BibliographicRecordId
*     [ComponentId] = ComponentId
*     [Edition] = Edition
*     [Pagination] = Pagination
*     [PlaceOfPublication] = PlaceOfPublication
*     [PublicationDate] = PublicationDate
*     [PublicationDateOfComponent] = PublicationDateOfComponent
*     [Publisher] = Publisher
*     [SeriesTitleNumber] = SeriesTitleNumber
*     [Title] = Title
*     [TitleOfComponent] = TitleOfComponent
*     [BibliographicLevel] = BibliographicLevel
*     [SponsoringBody] = SponsoringBody
*     [ElectronicDataFormatType] = ElectronicDataFormatType
*     [Language] = Language
*     [MediumType] = MediumType
* )
* 
* 
* CancelRequestItemResponse:
* --------------------------
* Array
* (
*     [Ncip] => CancelRequestItemResponse
*     [FromAgencyId] => DK-710100
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-190101
*     [UniqueUserId] => Array
*         (
*             [UserIdentifierValue] => 1231231230
*             [UniqueAgencyId] => DK-710100
*         )
*     [UniqueRequestId] => Array
*         (
*             [RequestIdentifierValue] => 87654321
*             [UniqueAgencyId] => DK-710100
*         )
* )
* 
* 
* RenewItemResponse:
* ------------------
* Array
* (
*     [Ncip] => RenewItemResponse
*     [FromAgencyId] => DK-190103
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-715700
*     [UniqueItemId] => Array
*         (
*             [ItemIdentifierValue] => 27175953
*             [UniqueAgencyId] => DK-715700
*         )
*     [DateDue] => 1221901200
* )
* 
* Array
* (
*     [Ncip] => RenewItemResponse
*     [FromAgencyId] => DK-190103
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-715700
*     [DateOfExpectedReply] => 1221901200
* )
* 
* 
* Fejl:
* -----
* Array
* (
*     [Ncip] => LookupUserResponse
*     [FromAgencyId] => DK-710100
*     [FromAgencyAuthentication] => [PASSWORD]
*     [ToAgencyId] => DK-190101
*     [Problem] => ProcessingError eller MessagingError
*     [ErrorType] => User Authentication Failed
*     [ErrorElement] => AuthenticationInput
* )
* 
*
*
*   
*/

define("NCIP_DATE_FORMAT", "d.m.Y");

class ncip extends http_wrapper {
  private $parameters;   // Parametre som php array
  private $dom;          // DomDocument
  private $dom_xml="";   // The XML ncip structure
  
  private $createMessages =
    array( "LookupUser"                => "_create_lookup_user_request",
           "LookupUserResponse"        => "_create_lookup_user_response",
           "LookupItem"                => "_create_lookup_item_request",
           "LookupItemResponse"        => "_create_lookup_item_response",
           "LookupRequest"             => "_create_lookup_request_request",
           "LookupRequestResponse"     => "_create_lookup_request_response",
           "CancelRequestItem"         => "_create_cancel_request_item_request",
           "CancelRequestItemResponse" => "_create_cancel_request_item_response",
           "RenewItem"                 => "_create_renew_item_request",
           "RenewItemResponse"         => "_create_renew_item_response" );
  private $parseMessages =
    array( "LookupUser"                => "_parse_lookup_user_request",
           "LookupUserResponse"        => "_parse_lookup_user_response",
           "LookupItem"                => "_parse_lookup_item_request",
           "LookupItemResponse"        => "_parse_lookup_item_response",
           "LookupRequest"             => "_parse_lookup_request_request",
           "LookupRequestResponse"     => "_parse_lookup_request_response",
           "CancelRequestItem"         => "_parse_cancel_request_item_request",
           "CancelRequestItemResponse" => "_parse_cancel_request_item_response",
           "RenewItem"                 => "_parse_renew_item_request",
           "RenewItemResponse"         => "_parse_renew_item_response" );


/** \brief Constructor
*
* Constructoren er tom
*
*/
  public function __construct() {}


/** \brief Destructor
*
* Destructoren er tom
*
*/
  public function __destruct() {}



/** \brief build
*
* Denne klasse opbygger en ncip request eller en ncip response
* Følgende 5 request/response typer understøttes:
*   LookupUser / LookupUserResponse
*   LookupItem / LookupItemResponse
*   LookupRequest / LookupRequestResponse
*   CancelRequestItem / CancelRequestItemResponse
*   RenewItem / RenewItemResponse
* Request/Response typen lægges i $parameters["Ncip"]
* Typen skal defineres, og være understøttet
* 
* @param array  $parameters Associativt array indeholdene parametre for requesten/responsen
* @return string Den opbyggede ncip request/response som ren tekst
*/
  public function build($parameters) {
    $this->parameters = $parameters;
    $message = $parameters["Ncip"];
    if (empty($parameters["Ncip"]) or
        !in_array($message, array_keys($this->createMessages)) or 
        ($this->parameters["Problem"]["Error"] == "UnknownError")) {
          return $this->_create_version_message();
        }
    $impl = new DomImplementation;
    $dtd = $impl->createDocumentType("NCIPMessage", "-//NISO//NCIP DTD Version 1//EN", "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd");
    $this->dom = $impl->createDocument(null, null, $dtd);
    $this->dom->version = "1.0";
    $this->dom->encoding = "UTF-8";
    $ncipMessage = $this->dom->createElement("NCIPMessage");
    $ncipMessage->setAttribute("version", "http://ncip.envisionware.com/documentation/ncip_v1_0.dtd");
    $ncip_message_element = $this->dom->createElement($message);
    if ( method_exists($this, $this->createMessages[$message]) )
      call_user_func(array($this, $this->createMessages[$message]), $ncip_message_element);  // Kald den aktuelle build metode
    $ncipMessage->appendChild( $ncip_message_element );
    $this->dom->appendChild( $ncipMessage );
    $this->dom->formatOutput = true;
    $this->dom_xml = $this->dom->saveXML();
    return $this->dom_xml;
  }




/** \brief parse
*
* Læser og fortolker input teksten som en ncip request eller response, og opbygger et php array, med de læste data.
*
* @param string $ncip_str Ncip responsen som xml tekst
* @return array Fortolkede værdier, udtrukket fra ncip responsen
*
*/
  public function parse(&$ncip_str) {
    $this->parameters = array();
    $this->dom = DOMDocument::loadXML($ncip_str,  LIBXML_NOERROR);

    $rootTags = $this->_get_child_elements($this->dom);
    $ncipMessage = $rootTags[0];  // Der bør kun være ét tag som rod-tag, og det skal være NCIPMessage
    if ((empty($ncipMessage)) or ($ncipMessage->nodeName != "NCIPMessage")) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "NCIPMessage", "NCIP Messaging Error Type Scheme");

    $ncipMessageElements = $this->_get_child_elements($ncipMessage);
    $message = $ncipMessageElements[0];  // Det første tag i NCIPMessage skal være message type tagget - såhhh det regner vi med at det er
    $tag = $message->nodeName;

    $this->parameters["Ncip"] = $tag;
    if ( !in_array($tag, array_keys($this->parseMessages)) ) return $this->_problem("MessagingError", "Unsupported Service", $tag, "NCIP General Processing Error Scheme");
    if ( !method_exists($this, $this->parseMessages[$tag]) ) return $this->_problem("MessagingError", "Unsupported Service", $tag, "NCIP General Processing Error Scheme");
    $this->parameters = array_merge($this->parameters, call_user_func(array($this, $this->parseMessages[$tag]), $message));  // Kald den aktuelle parsing metode
    return $this->parameters;
  }


/** \brief parse_file
*
* Læser og fortolker indholdet af input filen som en ncip response, og opbygger et php array, med de læste data.
*
* @param string $ncip_file Fil navnet på den fil, der indeholder ncip responsen
* @return array Fortolkede værdier, udtrukket fra ncip responsen
*
*/
  public function parse_file($ncip_file) {
    $file_content = file_get_contents($ncip_file);
    return $this->parse($file_content);
  }




//------------------------------------------------------------------------------
// Private metoder
//------------------------------------------------------------------------------



/** \brief _create_version_message
*
* Opbyg en fuldstændig "NCIPVersionMessage" besked
* 
* @return string Den opbyggede "NCIPVersionMessage" som ren tekst
*
*/
  private function _create_version_message() {
    $impl = new DomImplementation;
    $dtd = $impl->createDocumentType("NCIPVersionMessage", "-//NISO//NCIP DTD Version 1//EN", "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd");
    $this->dom = $impl->createDocument(null, null, $dtd);
    $this->dom->version = "1.0";
    $this->dom->encoding = "UTF-8";
    $ncip_version_message = $this->dom->createElement("NCIPVersionMessage");
    $ncip_version_message->setAttribute("version", "http://ncip.envisionware.com/documentation/ncip_v1_0.dtd");
    $lookup_version_response = $this->dom->createElement("LookupVersionResponse");
    $lookup_version_response->appendChild($this->_create_agency_id("From", $this->parameters["FromAgencyId"]));
    $lookup_version_response->appendChild($this->_create_agency_id("To", $this->parameters["ToAgencyId"]));
    $lookup_version_response->appendChild($this->dom->createElement("VersionSupported", "http://ncip.envisionware.com/documentation/ncip_v1_0.dtd"));
    $ncip_version_message->appendChild( $lookup_version_response );
    $this->dom->appendChild($ncip_version_message);
    $this->dom->formatOutput = true;
    $this->dom_xml = $this->dom->saveXML();
    return $this->dom_xml;
  }


/** \brief _create_lookup_user_request
*
* Opbyg en "LookupUser" ncip request
* 
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_lookup_user_request($xml) {
    if (!isset($this->parameters["UserElementType"])) $this->parameters["UserElementType"] = "Name Information";
    if (!isset($this->parameters["LoanedItemsDesired"])) $this->parameters["LoanedItemsDesired"] = 1;
    if (!isset($this->parameters["RequestedItemsDesired"])) $this->parameters["RequestedItemsDesired"] = 1;
    $xml->appendChild($this->_create_header("InitiationHeader"));
    $xml->appendChild($this->_create_authentication_input($this->parameters["UserId"], "text/plain", "User Id"));
    $xml->appendChild($this->_create_authentication_input($this->parameters["UserPIN"], "text/plain", "PIN"));
    $xml->appendChild($this->_create_scheme_value_pair("UserElementType", "http://www.niso.org/ncip/v1_0/schemes/userelementtype/userelementtype.scm", $this->parameters["UserElementType"]));
    if (!empty($this->parameters["LoanedItemsDesired"])) $xml->appendChild($this->dom->createElement("LoanedItemsDesired"));
    if (!empty($this->parameters["RequestedItemsDesired"])) $xml->appendChild($this->dom->createElement("RequestedItemsDesired"));
  }

/** \brief _create_lookup_user_response
*
* Opbyg en "LookupUser" ncip response
* 
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_lookup_user_response($xml) {
    $xml->appendChild($this->_create_header("ResponseHeader"));
    $xml_problem = $this->_create_problem();
    if (!empty($xml_problem)) {
      $xml->appendChild($xml_problem);
      return;
    }
    $xml->appendChild($this->_create_unique_id("User", $this->parameters));
    $xml_user_transaction = $this->dom->createElement("UserTransaction");
    if (is_array($this->parameters["RequestedItem"]))
      foreach ($this->parameters["RequestedItem"] as $item) {
        $xml_requested_item = $this->dom->createElement("RequestedItem");
        $xml_requested_item->appendChild($this->_create_unique_id("Request", $item));
        if (!empty($item["RequestType"])) {
          $xml_requested_item->appendChild($this->_create_scheme_value_pair("RequestType", "http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm", $item["RequestType"]));
        }
        if (!empty($item["RequestStatusType"])) {
          $xml_requested_item->appendChild($this->_create_scheme_value_pair("RequestStatusType", "http://www.niso.org/ncip/v1_0/imp1/schemes/requeststatustype/requeststatustype.scm", $item["RequestStatusType"]));
        }
        if (!empty($item["DatePlaced"])) {
          $xml_requested_item->appendChild($this->dom->createElement("DatePlaced", date(NCIP_DATE_FORMAT, $item["DatePlaced"])));
        }
        if (!empty($item["PickupDate"])) {
          $xml_requested_item->appendChild($this->dom->createElement("PickupDate", date(NCIP_DATE_FORMAT, $item["PickupDate"])));
        }
        if (!empty($item["PickupExpiryDate"])) {
          $xml_requested_item->appendChild($this->dom->createElement("PickupExpiryDate", date(NCIP_DATE_FORMAT, $item["PickupExpiryDate"])));
        }
        if (!empty($item["ReminderLevel"])) {
          $xml_requested_item->appendChild($this->dom->createElement("ReminderLevel", date(NCIP_DATE_FORMAT, $item["ReminderLevel"])));
        }
        if (!empty($item["HoldQueuePosition"])) {
          $xml_requested_item->appendChild($this->dom->createElement("HoldQueuePosition", $item["HoldQueuePosition"]));
        }
        $xml_user_transaction->appendChild($xml_requested_item);
      }
    if (is_array($this->parameters["LoanedItem"]))
      foreach ($this->parameters["LoanedItem"] as $item) {
        $xml_loaned_item = $this->dom->createElement("LoanedItem");
        $xml_loaned_item->appendChild($this->_create_unique_id("Item", $item));
        if (!empty($item["ReminderLevel"])) {
          $xml_loaned_item->appendChild($this->dom->createElement("ReminderLevel", $item["ReminderLevel"]));
        }
        if (!empty($item["DateDue"])) {
          $xml_loaned_item->appendChild($this->dom->createElement("DateDue", date(NCIP_DATE_FORMAT, $item["DateDue"])));
        }
        if (isset($item["MonetaryValue"])) {
          $xml_amount = $this->dom->createElement("Amount");
          if (!empty($item["CurrencyCode"])) {
            $xml_amount->appendChild($this->_create_scheme_value_pair("CurrencyCode", "http://www.bsi-global.com/Technical+Information/Publications/_Publications/tig90x.doc", $item["CurrencyCode"]));
          }
          $xml_amount->appendChild($this->dom->createElement("MonetaryValue", $item["MonetaryValue"]));
          $xml_loaned_item->appendChild($xml_amount);
        }
        $xml_user_transaction->appendChild($xml_loaned_item);
      }
    $xml->appendChild($xml_user_transaction);
    if (!empty($this->parameters["GivenName"]) or !empty($this->parameters["Surname"]) or !empty($this->parameters["UnstructuredPersonalUserName"])) {
      $xml_user_optional_fields = $this->dom->createElement("UserOptionalFields");
      $xml_name_information = $this->dom->createElement("NameInformation");
      $xml_personal_name_information = $this->dom->createElement("PersonalNameInformation");
      if (!empty($this->parameters["GivenName"]) or !empty($this->parameters["Surname"])) {
        $xml_structured_personal_user_name = $this->dom->createElement("StructuredPersonalUserName");
        $xml_structured_personal_user_name->appendChild($this->dom->createElement("GivenName", utf8_encode($this->parameters["GivenName"])));
        $xml_structured_personal_user_name->appendChild($this->dom->createElement("Surname", utf8_encode($this->parameters["Surname"])));
        $xml_personal_name_information->appendChild($xml_structured_personal_user_name);
      }
      if (!empty($this->parameters["UnstructuredPersonalUserName"])) {
        $xml_unstructured_personal_user_name = $this->dom->createElement("UnstructuredPersonalUserName", $this->parameters["UnstructuredPersonalUserName"]);
        $xml_personal_name_information->appendChild($xml_unstructured_personal_user_name);
      }
      $xml_name_information->appendChild($xml_personal_name_information);
      $xml_user_optional_fields->appendChild($xml_name_information);
      $xml->appendChild($xml_user_optional_fields);
    }
  }

/** \brief _create_lookup_item_request
*
* Opbyg en "LookupItem" ncip request
* 
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_lookup_item_request($xml) {
    if (!isset($this->parameters["ItemElementType"])) $this->parameters["ItemElementType"] = "Bibliographic Description";
    $xml->appendChild($this->_create_header("InitiationHeader"));
    $xml->appendChild($this->_create_unique_id("Item", $this->parameters));
    $xml->appendChild($this->_create_scheme_value_pair("ItemElementType", "http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm", $this->parameters["ItemElementType"]));
  }

/** \brief _create_lookup_item_response
*
* Opbyg en "LookupItem" ncip response
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_lookup_item_response($xml) {
    $xml->appendChild($this->_create_header("ResponseHeader"));
    $xml_problem = $this->_create_problem();
    if (!empty($xml_problem)) {
      $xml->appendChild($xml_problem);
      return;
    }
    $xml->appendChild($this->_create_unique_id("Item", $this->parameters));
    if (!empty($this->parameters["HoldPickupDate"]))
      $xml->appendChild($this->dom->createElement("HoldPickupDate", $this->parameters["HoldPickupDate"]));
    if (!empty($this->parameters["DateRecalled"]))
      $xml->appendChild($this->dom->createElement("DateRecalled", $this->parameters["DateRecalled"]));
    $this->_create_item_optional_fields($xml);
  }

/** \brief _create_lookup_request_request
*
* Opbyg en "LookupRequest" ncip request
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_lookup_request_request($xml) {
    if (!isset($this->parameters["ItemElementType"])) $this->parameters["ItemElementType"] = "Bibliographic Description";
    $xml->appendChild($this->_create_header("InitiationHeader"));
    $xml->appendChild($this->_create_unique_id("Request", $this->parameters));
    $xml->appendChild($this->_create_scheme_value_pair("ItemElementType", "http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm", $this->parameters["ItemElementType"]));
  }

/** \brief _create_lookup_request_response
*
* Opbyg en "LookupRequest" ncip response
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_lookup_request_response($xml) {
    $xml->appendChild($this->_create_header("ResponseHeader"));
    $xml_problem = $this->_create_problem();
    if (!empty($xml_problem)) {
      $xml->appendChild($xml_problem);
      return;
    }
    $xml->appendChild($this->_create_unique_id("Request", $this->parameters));
    if (!empty($this->parameters["HoldQueuePosition"])) {
      $xml->appendChild($this->dom->createElement("HoldQueuePosition", $this->parameters["HoldQueuePosition"]));
    }
    $this->_create_item_optional_fields($xml);
  }

/** \brief _create_cancel_request_item_request
*
* Opbyg en "CancelRequestItem" ncip request
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_cancel_request_item_request($xml) {
    $xml->appendChild($this->_create_header("InitiationHeader"));
    $xml->appendChild($this->_create_unique_id("User", $this->parameters));
    $xml->appendChild($this->_create_unique_id("Request", $this->parameters));
    $xml->appendChild($this->_create_scheme_value_pair("RequestType", "http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm", $this->parameters["RequestType"]));
 }

/** \brief _create_cancel_request_item_response
*
* Opbyg en "CancelRequestItem" ncip response
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_cancel_request_item_response($xml) {
    $xml->appendChild($this->_create_header("ResponseHeader"));
    $xml_problem = $this->_create_problem();
    if (!empty($xml_problem)) {
      $xml->appendChild($xml_problem);
      return;
    }
    $xml->appendChild($this->_create_unique_id("User", $this->parameters));
    $xml->appendChild($this->_create_unique_id("Request", $this->parameters));
 }

/** \brief _create_renew_item_request
*
* Opbyg en "RenewItem" ncip request
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_renew_item_request($xml) {
    $xml->appendChild($this->_create_header("InitiationHeader"));
    $xml->appendChild($this->_create_unique_id("User", $this->parameters));
    $xml->appendChild($this->_create_unique_id("Item", $this->parameters));
  }

/** \brief _create_renew_item_response
*
* Opbyg en "RenewItem" ncip response
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_renew_item_response($xml) {
    $xml->appendChild($this->_create_header("ResponseHeader"));
    $xml_problem = $this->_create_problem();
    if (!empty($xml_problem)) {
      $xml->appendChild($xml_problem);
      return;
    }
    if (!empty($this->parameters["UniqueItemId"])) {
      $xml->appendChild($this->_create_unique_id("Item", $this->parameters));
    }
    if (!empty($this->parameters["DateDue"])) {
      $xml->appendChild($this->dom->createElement("DateDue", date(NCIP_DATE_FORMAT, $this->parameters["DateDue"])));
    }
    if (!empty($this->parameters["DateOfExpectedReply"])) {
      $xml_pending = $this->dom->createElement("Pending");
      $xml_pending->appendChild($this->dom->createElement("DateOfExpectedReply", date(NCIP_DATE_FORMAT, $this->parameters["DateOfExpectedReply"])));
      $xml->appendChild($xml_pending);
    }
  }

/** \brief _create_scheme_value_pair
*
* Opbygger et <scheme> / <value> par
*
* @param string $tag Tag navnet på det scheme/value par, der skal opbygges
* @param string $scheme Skema navnet
* @param string $scheme Væredien
* @return DOMElement Elementet, indeholdende scheme/value parret
*
*/
  private function _create_scheme_value_pair($tag, $scheme, $value) {
    $xml = $this->dom->createElement($tag);
    if (!empty($scheme)) $xml->appendChild($this->dom->createElement("Scheme", $scheme));
    if (!empty($value)) $xml->appendChild($this->dom->createElement("Value", $value));
    return $xml;
  }
  
/** \brief _create_agency_id
*
* Opbygger et element indeholdende et Unique Agency ID
*
* @param string $what Hvilket XXXAgencyId element, der skal opbygges, hvor XXX erstattes af $what
* @param string $agencyId Værdien for UniqueAgencyId, der puttes ned i det resulterende element
* @return DOMElement Elementet, indeholdende UniqueAgencyId
*
*/
  private function _create_agency_id($what, $agencyId) {
    $xml = $this->dom->createElement($what . "AgencyId");
    $xml->appendChild($this->_create_scheme_value_pair("UniqueAgencyId", "http://biblstandard.dk/isil/schemes/1.1/", $agencyId));
    return $xml;
  }
  
/** \brief _create_header
*
* Opbygger et header element med tagget: <$header_tag>
* Napper parametrene "FromAgencyId", "FromAgencyAuthentication" og "ToAgencyId" fra $this->parameters
*
* @param Tag navnet for headeren
* @return DOMElement Det resulterende Initiation Header element
*
*/
  private function _create_header($header_tag) {
    $xml = $this->dom->createElement($header_tag);
    $xml->appendChild($this->_create_agency_id("From", $this->parameters["FromAgencyId"]));
    if (!empty($this->parameters["FromAgencyAuthentication"])) {
      $xml->appendChild($this->dom->createElement("FromAgencyAuthentication", $this->parameters["FromAgencyAuthentication"]));
    }
    $xml->appendChild($this->_create_agency_id("To", $this->parameters["ToAgencyId"]));
    return $xml;
  }

/** \brief _create_item_optional_fields
*
* Opbyg en "ItemOptionalFields" blok
*
* Parametrene til ncip beskeden hentes fra $this->parameters. Se formatet herover.
* 
* @param DOMElement $xml Det element, hvortil den aktuelle xml info skal tilføjes
*
*/
  private function _create_item_optional_fields($xml) {
    if (!empty($this->parameters["Author"]) or
        !empty($this->parameters["AuthorOfComponent"]) or
        !empty($this->parameters["BibliographicItemId"]) or
        !empty($this->parameters["BibliographicRecordId"]) or
        !empty($this->parameters["ComponentId"]) or
        !empty($this->parameters["Edition"]) or
        !empty($this->parameters["Pagination"]) or
        !empty($this->parameters["PlaceOfPublication"]) or
        !empty($this->parameters["PublicationDate"]) or
        !empty($this->parameters["PublicationDateOfComponent"]) or
        !empty($this->parameters["Publisher"]) or
        !empty($this->parameters["SeriesTitleNumber"]) or
        !empty($this->parameters["Title"]) or
        !empty($this->parameters["TitleOfComponent"]) or
        !empty($this->parameters["BibliographicLevel"]) or
        !empty($this->parameters["SponsoringBody"]) or
        !empty($this->parameters["ElectronicDataFormatType"]) or
        !empty($this->parameters["Language"]) or
        !empty($this->parameters["MediumType"])) {
      $xml_item_optional_fields = $this->dom->createElement("ItemOptionalFields");
      $xml_bibliographic_description = $this->dom->createElement("BibliographicDescription");
      if (!empty($this->parameters["Author"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("Author", $this->parameters["Author"]));
      if (!empty($this->parameters["AuthorOfComponent"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("AuthorOfComponent", $this->parameters["AuthorOfComponent"]));
      if (!empty($this->parameters["BibliographicItemId"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("BibliographicItemId", $this->parameters["BibliographicItemId"]));
      if (!empty($this->parameters["BibliographicRecordId"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("BibliographicRecordId", $this->parameters["BibliographicRecordId"]));
      if (!empty($this->parameters["ComponentId"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("ComponentId", $this->parameters["ComponentId"]));
      if (!empty($this->parameters["Edition"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("Edition", $this->parameters["Edition"]));
      if (!empty($this->parameters["Pagination"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("Pagination", $this->parameters["Pagination"]));
      if (!empty($this->parameters["PlaceOfPublication"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("PlaceOfPublication", $this->parameters["PlaceOfPublication"]));
      if (!empty($this->parameters["PublicationDate"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("PublicationDate", $this->parameters["PublicationDate"]));
      if (!empty($this->parameters["PublicationDateOfComponent"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("PublicationDateOfComponent", $this->parameters["PublicationDateOfComponent"]));
      if (!empty($this->parameters["Publisher"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("Publisher", $this->parameters["Publisher"]));
      if (!empty($this->parameters["SeriesTitleNumber"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("SeriesTitleNumber", $this->parameters["SeriesTitleNumber"]));
      if (!empty($this->parameters["Title"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("Title", $this->parameters["Title"]));
      if (!empty($this->parameters["TitleOfComponent"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("TitleOfComponent", $this->parameters["TitleOfComponent"]));
      if (!empty($this->parameters["BibliographicLevel"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("BibliographicLevel", $this->parameters["BibliographicLevel"]));
      if (!empty($this->parameters["SponsoringBody"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("SponsoringBody", $this->parameters["SponsoringBody"]));
      if (!empty($this->parameters["ElectronicDataFormatType"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("ElectronicDataFormatType", $this->parameters["ElectronicDataFormatType"]));
      if (!empty($this->parameters["Language"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("Language", $this->parameters["Language"]));
      if (!empty($this->parameters["MediumType"]))
        $xml_bibliographic_description->appendChild($this->dom->createElement("MediumType", $this->parameters["MediumType"]));
      $xml_item_optional_fields->appendChild($xml_bibliographic_description);
      $xml->appendChild($xml_item_optional_fields);
    }
  }

/** \brief _create_problem
*
* Opbygger et element med tagget: <Problem>
* Napper parameteren "Problem" fra $this->parameters
*
*  [Problem] array
*     [Error] => ProcessingError eller MessagingError
*     [Type] => User Authentication Failed
*     [Element] => AuthenticationInput
*     [Scheme] => "http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm"
*     [Value] => Value
*
* @return DOMElement Det resulterende Problem element
*
*/
  private function _create_problem() {
    if (empty($this->parameters["Problem"]) or empty($this->parameters["Problem"]["Error"])) {
      return null;
    }
    $xml = $this->dom->createElement("Problem");
    $xml_error = $this->dom->createElement($this->parameters["Problem"]["Error"]);
    $xml_error->appendChild($this->_create_scheme_value_pair($this->parameters["Problem"]["Error"] . "Type", $this->parameters["Problem"]["Scheme"], $this->parameters["Problem"]["Type"]));
    $xml_error_element = $this->dom->createElement($this->parameters["Problem"]["Error"] . "Element");
    if (!empty($this->parameters["Problem"]["Element"]))
      $xml_error_element->appendChild($this->dom->createElement("ElementName", $this->parameters["Problem"]["Element"]));
    if (!empty($this->parameters["Problem"]["Value"]))
      $xml_error_element->appendChild($this->dom->createElement($this->parameters["Problem"]["Error"] . "Value", $this->parameters["Problem"]["Value"]));
    $xml_error->appendChild($xml_error_element);
    $xml->appendChild($xml_error);
    return $xml;
  }
  
/** \brief _problem
*
* Simpel utility rutine, der opbygger et Problem array med de rigtige parametre
*
*  [Problem] array
*     [Error] => ProcessingError eller MessagingError
*     [Type] => User Authentication Failed
*     [Element] => AuthenticationInput
*     [Scheme] => "http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm"
*     [Value] => Value
*
* @param string $error Error: Enten "ProcessingError" eller "MessagingError"
* @param string $type Typen på fejlen - 'fejl beskrivelsen'
* @param string $element Elementet, der fejler
* @param string $scheme Scheme't på fejlen
* @param string $value Hvis der er en værdi, angives den her
* 
* @return array Det resulterende Problem array
*
*/
  private function _problem($error, $type, $element, $scheme, $value="") {
    if ( ($error != "ProcessingError") and ($error != "MessagingError") )
      return array( "Problem" => array("Error" => "MessagingError", "Type" => "Invalid Message Syntax Error", "Element" => $element, "Scheme" => "NCIP Messaging Error Type Scheme"));
    $ret_array = array( "Error" => $error, "Type" => $type, "Element" => $element, "Scheme" => $scheme);
    if (!empty($value)) $ret_array["Value"] = $value;
    return array( "Problem" => $ret_array);
  }


/** \brief _create_authentication_input
*
* Opbygger et <AuthenticationInput> element
*
* @param string $inputData Authentication Input string
* @param string dataFormatType Data Format Typen for Authentication inputtet
* @param string $inputType Input Typen for Authentication inputtet
* @return DOMElement Det resulterende AuthenticationInput element
*
*/
  private function _create_authentication_input($inputData, $dataFormatType, $inputType) {
    $xml = $this->dom->createElement("AuthenticationInput");
    $xml->appendChild($this->dom->createElement("AuthenticationInputData", $inputData));
    $xml->appendChild($this->_create_scheme_value_pair("AuthenticationDataFormatType", "http://www.iana.org/assignments/media-types", $dataFormatType));
    $xml->appendChild($this->_create_scheme_value_pair("AuthenticationInputType", "http://www.niso.org/ncip/v1_0/imp1/schemes/authenticationinputtype/authenticationinputtype.scm", $inputType));
    return $xml;
  }
  
/** \brief _create_unique_id
*
* Opbygger et <UniqueXXXId> element
*
* @param string $what Hvilket UniqueXXXId element, der skal opbygges, hvor XXX erstattes af $what
* @param array $uniqueXxxId hvorfra parametrene hentes på formen:
*                $uniqueXxxId["UniqueXXXId"]["XXXIdentifierValue"] og
*                $uniqueXxxId["UniqueXXXId"]["UniqueAgencyId"]
*         
* @return DOMElement Det resulterende UniqueXXXId element
*
*/
  private function _create_unique_id($what, $uniqueXxxId) {
    $xml = $this->dom->createElement("Unique" . $what . "Id");
    $xml->appendChild($this->_create_scheme_value_pair("UniqueAgencyId", "http://biblstandard.dk/isil/schemes/1.1/", $uniqueXxxId["Unique" . $what . "Id"]["UniqueAgencyId"]));
    $xml->appendChild($this->dom->createElement($what . "IdentifierValue", $uniqueXxxId["Unique" . $what . "Id"][$what . "IdentifierValue"]));
    return $xml;
  }


//==============================================================================


/** \brief _parse_lookup_user_request
*
* Fortolker LookupUser request, og returnerer et array med resultatet
*
* @param DOMElement $lookupRequest DOM Elementet med indholdet af LookupUserRequest
* @return array De fortolkede værdier
*
*/
  private function _parse_lookup_user_request($lookupRequest) {
    $user = $this->_parse_header("InitiationHeader", $lookupRequest);
    if (!empty($user["Problem"])) return $user;
    $user["UserId"] = $this->_parse_authentication_input("User Id", $lookupRequest);
    $user["UserPIN"] = $this->_parse_authentication_input("PIN", $lookupRequest);
    $user["UserElementType"] = $this->_parse_scheme_value_pair($lookupRequest, "UserElementType");
    if (empty($user["UserElementType"])) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "UserElementType", "NCIP Messaging Error Type Scheme");
    $loaned_items = $lookupRequest->getElementsByTagName("LoanedItemsDesired");
    if (is_object($loaned_items) and ($loaned_items->length>0)) $user["LoanedItemsDesired"] = 1;
    $requested_items = $lookupRequest->getElementsByTagName("RequestedItemsDesired");
    if (is_object($requested_items) and ($requested_items->length>0)) $user["RequestedItemsDesired"] = 1;
    return $user;
  }
  
  
/** \brief _parse_lookup_user_response
*
* Fortolker LookupUser response, og returnerer et array med resultatet
*
* @param DOMElement $lookupResponse DOM Elementet med indholdet af LookupUserResponse
* @return array De fortolkede værdier
*
*/
  private function _parse_lookup_user_response($lookupResponse) {
    $user = $this->_parse_header("ResponseHeader", $lookupResponse);
    if (!empty($user["Problem"])) return $user;
    $user = array_merge($user, $this->_parse_unique_id_header($lookupResponse, "User"));
    unset($user["Problem"]);  // UniqueIdHeader behover ikke at vaere der - saa undertryk fejl
    $userTransaction = $lookupResponse->getElementsByTagName("UserTransaction")->item(0);
    if (!empty($userTransaction)) {
      foreach ( $userTransaction->getElementsByTagName("RequestedItem") as $item ) {
        $req = $this->_parse_unique_id_header($item, "Request");  // Parse "UniqueRequestId"
        if (isset($req)) {
          $req["RequestType"] = $item->getElementsByTagName("RequestType")->item(0)->getElementsByTagName("Value")->item(0)->nodeValue;
          $req["RequestStatusType"] = $item->getElementsByTagName("RequestStatusType")->item(0)->getElementsByTagName("Value")->item(0)->nodeValue;
          $req["DatePlaced"] = strtotime($item->getElementsByTagName("DatePlaced")->item(0)->nodeValue);
          $pickupDate = $item->getElementsByTagName("PickupDate")->item(0)->nodeValue;
          if (isset($pickupDate)) $req["PickupDate"] = strtotime($pickupDate);
          $pickupExpiryDate = $item->getElementsByTagName("PickupExpiryDate")->item(0)->nodeValue;
          if (isset($pickupExpiryDate)) $req["PickupExpiryDate"] = strtotime($pickupExpiryDate);
          $reminderLevel = $item->getElementsByTagName("ReminderLevel")->item(0)->nodeValue;
          if (isset($reminderLevel)) $req["PickupReminderLevelDate"] = $reminderLevel;
          $holdQueuePosition = $item->getElementsByTagName("HoldQueuePosition")->item(0)->nodeValue;
          if (isset($holdQueuePosition)) $req["HoldQueuePosition"] = $holdQueuePosition;
          $user["RequestedItem"][] = $req;
        }
      }
      foreach ( $userTransaction->getElementsByTagName("LoanedItem") as $item ) {
        $loan = $this->_parse_unique_id_header($item, "Item");  // Parse "UniqueItemId"
        if (isset($loan)) {
          $loan["ReminderLevel"] = $item->getElementsByTagName("ReminderLevel")->item(0)->nodeValue;
          $dateDue = $item->getElementsByTagName("DateDue")->item(0)->nodeValue;
          if (isset($dateDue)) $loan["DateDue"] = strtotime($dateDue);
          $amount = $item->getElementsByTagName("Amount")->item(0);
          if (isset($amount)) {
            $currencyCode = $amount->getElementsByTagName("CurrencyCode")->item(0);
            if (isset($currencyCode)) {
              $loan["CurrencyCode"] = $currencyCode->getElementsByTagName("Value")->item(0)->nodeValue;
            }
            $loan["MonetaryValue"] = $amount->getElementsByTagName("MonetaryValue")->item(0)->nodeValue;
          }
          $user["LoanedItem"][] = $loan;                                                                                                                           
        }
      }
    }
    $userOptionalFields = $lookupResponse->getElementsByTagName("UserOptionalFields")->item(0);
    if (isset($userOptionalFields)) {
      $structuredPersonalUserName = $userOptionalFields->getElementsByTagName("StructuredPersonalUserName")->item(0);
      if (isset($structuredPersonalUserName)) {
        $user["GivenName"] = htmlentities($structuredPersonalUserName->getElementsByTagName("GivenName")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        $user["Surname"] = htmlentities($structuredPersonalUserName->getElementsByTagName("Surname")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
      }
      $unstructuredPersonalUserName = $userOptionalFields->getElementsByTagName("UnstructuredPersonalUserName")->item(0);
      if (isset($unstructuredPersonalUserName)) {
        $user["UnstructuredPersonalUserName"] = htmlentities($unstructuredPersonalUserName->nodeValue, ENT_COMPAT, $this->dom->encoding);
      }
    }
    return $user;
  }
  
  
/** \brief _parse_lookup_item_request
*
* Fortolker LookupItem request, og returnerer et array med resultatet
*
* @param DOMElement $lookupRequest DOM Elementet med indholdet af LookupItemRequest
* @return array De fortolkede værdier
*
*/
  private function _parse_lookup_item_request($lookupRequest) {
    $item = $this->_parse_header("InitiationHeader", $lookupRequest);
    if (!empty($item["Problem"])) return $item;
    $item = array_merge($item, $this->_parse_unique_id_header($lookupRequest, "Item"));
    if (!empty($item["Problem"])) return $item;
    $item["ItemElementType"] = $this->_parse_scheme_value_pair($lookupRequest, "ItemElementType");
    if (empty($item["ItemElementType"])) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "ItemElementType", "NCIP Messaging Error Type Scheme");
    return $item;
  }
  
  
/** \brief _parse_lookup_item_response
*
* Fortolker LookupItem response, og returnerer et array med resultatet
*
* @param DOMElement $lookupResponse DOM Elementet med indholdet af LookupItemResponse
* @return array De fortolkede værdier
*
*/
  private function _parse_lookup_item_response($lookupResponse) {
    $item = $this->_parse_header("ResponseHeader", $lookupResponse);
    if (!empty($item["Problem"])) return $item;
    $item = array_merge($item, $this->_parse_unique_id_header($lookupResponse, "Item"));
    if (!empty($item["Problem"])) return $item;
    $holdPickupDate = $lookupResponse->getElementsByTagName("HoldPickupDate")->item(0)->nodeValue;
    if (isset($holdPickupDate)) $item = array_merge($item, strtotime($holdPickupDate));
    $dateRecalled = $lookupResponse->getElementsByTagName("DateRecalled")->item(0)->nodeValue;
    if (isset($dateRecalled)) $item = array_merge($item, strtotime($dateRecalled));
    // <ItemTransaction> er ikke understøttet i denne version
    $item = array_merge($item, $this->_parse_item_optional_fields($lookupResponse));
    return $item;
  }
  
  
/** \brief _parse_lookup_request_request
*
* Fortolker LookupRequest request, og returnerer et array med resultatet
*
* @param DOMElement $lookupRequest DOM Elementet med indholdet af LookupRequestRequest
* @return array De fortolkede værdier
*
*/
  private function _parse_lookup_request_request($lookupRequest) {
    $request = $this->_parse_header("InitiationHeader", $lookupRequest);
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($lookupRequest, "Request"));
    if (!empty($request["Problem"])) return $request;
    $request["ItemElementType"] = $this->_parse_scheme_value_pair($lookupRequest, "ItemElementType");
    return $request;
  }
  
  
/** \brief _parse_lookup_request_response
*
* Fortolker LookupRequest response, og returnerer et array med resultatet
*
* @param DOMElement $lookupResponse DOM Elementet med indholdet af LookupRequestResponse
* @return array De fortolkede værdier
*
*/
  private function _parse_lookup_request_response($lookupResponse) {
    $request = $this->_parse_header("ResponseHeader", $lookupResponse);
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($lookupResponse, "Request"));
    if (!empty($request["Problem"])) return $request;
    $holdQueuePosition = $lookupResponse->getElementsByTagName("HoldQueuePosition")->item(0)->nodeValue;
    if (isset($holdQueuePosition)) $request["HoldQueuePosition"] = $holdQueuePosition;
    $request = array_merge($request, $this->_parse_item_optional_fields($lookupResponse));
    return $request;
  }
  
  
/** \brief _parse_cancel_request_item_request
*
* Fortolker CancelRequestItem request, og returnerer et array med resultatet
*
* @param DOMElement $cancelRequest DOM Elementet med indholdet af CancelRequestItemRequest
* @return array De fortolkede værdier
*
*/
  private function _parse_cancel_request_item_request($cancelRequest) {
    $request = $this->_parse_header("InitiationHeader", $cancelRequest);
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($cancelRequest, "User"));
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($cancelRequest, "Request"));
    $request["RequestType"] = $this->_parse_scheme_value_pair($cancelRequest, "RequestType");
    return $request;
  }
  
  
/** \brief _parse_cancel_request_item_response
*
* Fortolker CancelRequestItem response, og returnerer et array med resultatet
*
* @param DOMElement $cancelResponse DOM Elementet med indholdet af CancelRequestItemResponse
* @return array De fortolkede værdier
*
*/
  private function _parse_cancel_request_item_response($cancelResponse) {
    $request = $this->_parse_header("ResponseHeader", $cancelResponse);
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($cancelResponse, "User"));
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($cancelResponse, "Request"));
    return $request;
  }
  
  
/** \brief _parse_renew_item_request
*
* Fortolker RenewItem request, og returnerer et array med resultatet
*
* @param DOMElement $renewRequest DOM Elementet med indholdet af RenewItemRequest
* @return array De fortolkede værdier
*
*/
  private function _parse_renew_item_request($renewRequest) {
    $request = $this->_parse_header("InitiationHeader", $renewRequest);
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($renewRequest, "User"));
    if (!empty($request["Problem"])) return $request;
    $request = array_merge($request, $this->_parse_unique_id_header($renewRequest, "Item"));
    return $request;
  }


/** \brief _parse_renew_item_response
*
* Fortolker RenewItem response, og returnerer et array med resultatet
*
* @param DOMElement $renewResponse DOM Elementet med indholdet af RenewItemResponse
* @return array De fortolkede værdier
*
*/
  private function _parse_renew_item_response($renewResponse) {
    $request = $this->_parse_header("ResponseHeader", $renewResponse);
    if (!empty($request["Problem"])) return $request;

    $pending = $renewResponse->getElementsByTagName("Pending")->item(0);
    if (isset($pending)) {
      $dateOfExpectedReply = $pending->getElementsByTagName("DateOfExpectedReply")->item(0)->nodeValue;
      if (isset($dateOfExpectedReply)) $request["DateOfExpectedReply"] = strtotime($dateOfExpectedReply);
      return $request;
    }
    $request = array_merge($request, $this->_parse_unique_id_header($renewResponse, "Item"));
    if (!empty($request["Problem"])) return $request;
    $dateDue = $renewResponse->getElementsByTagName("DateDue")->item(0)->nodeValue;
    if (isset($dateDue)) $request["DateDue"] = strtotime($dateDue);
    return $request;
  }


/** \brief _get_child_elements
*
* Ren XML forespørgsels metode, der henter listen af child - DOMElements
*
* @param DOMNode $node Parent node, hvor alle børne elementer ønskes
* @return DOMNode array af DOMElement's
*
*/
  private function _get_child_elements($node) {
    $tags = array();
    for ($i=0; $i<$node->childNodes->length; $i++) {
      if ($node->childNodes->item($i)->nodeType == XML_ELEMENT_NODE) {
        $tags[] = $node->childNodes->item($i);
      }
    }
  return $tags;
  }

/** \brief _parse_header
*
* Fortolker et DOMElement, og henter response header info ud af den
* Tjekker osse om der findes et Problem tag i elementet, og returnerer dette sammen med header info
*
* @param string $header_tag Navnet på header tag elementet
* @param DOMElement $xml Det element, hvorfra header info ønskes læst
* @return array Header info
*
*/
  private function _parse_header($header_tag, $xml) {
    $ret = array();
    if (!isset($xml)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", $header_tag, "NCIP Messaging Error Type Scheme");
    $header = $xml->getElementsByTagName($header_tag)->item(0);
    if (!isset($header)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", $header_tag, "NCIP Messaging Error Type Scheme");

    $fromAgencyId = $header->getElementsByTagName("FromAgencyId")->item(0);
    if (!isset($fromAgencyId)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "FromAgencyId", "NCIP Messaging Error Type Scheme");
    $ret["FromAgencyId"] = $this->_parse_scheme_value_pair($fromAgencyId, "UniqueAgencyId");
    if (empty($ret["FromAgencyId"])) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "UniqueAgencyId", "NCIP Messaging Error Type Scheme");

    $ret["FromAgencyAuthentication"] = $header->getElementsByTagName("FromAgencyAuthentication")->item(0)->nodeValue;
//    if (empty($ret["FromAgencyAuthentication"])) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "FromAgencyAuthentication", "NCIP Messaging Error Type Scheme");

    $toAgencyId = $header->getElementsByTagName("ToAgencyId")->item(0);
    if (!isset($toAgencyId)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "ToAgencyId", "NCIP Messaging Error Type Scheme");
    $ret["ToAgencyId"] = $this->_parse_scheme_value_pair($toAgencyId, "UniqueAgencyId");
    if (empty($ret["ToAgencyId"])) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "UniqueAgencyId", "NCIP Messaging Error Type Scheme");

    return array_merge($ret, $this->_parse_problem($xml));
  }
  
  
/** \brief _parse_unique_id_header
*
* Fortolker et DOMElement, og henter Unique ID Headeren info ud af det
*
* @param DOMElement $response Det element, hvorfra response header info ønskes læst
* @param string $par Navnet på den Unique ID, der ønskes info om ("UniqueXXXID")
* @return array Unique ID info
*
*/
  private function _parse_unique_id_header($response, $par) {
    if (!isset($response)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "Unique" . $par . "Id", "NCIP Messaging Error Type Scheme");
    $uniqueId = $response->getElementsByTagName("Unique" . $par . "Id")->item(0);
    if (!isset($uniqueId)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "Unique" . $par . "Id", "NCIP Messaging Error Type Scheme");
    $identifierValue = $uniqueId->getElementsByTagName($par . "IdentifierValue")->item(0);
    if (!isset($identifierValue)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", $par . "IdentifierValue", "NCIP Messaging Error Type Scheme");
    $uniqueAgencyId = $uniqueId->getElementsByTagName("UniqueAgencyId")->item(0);
    if (!isset($uniqueAgencyId)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "UniqueAgencyId", "NCIP Messaging Error Type Scheme");
    $value = $uniqueAgencyId->getElementsByTagName("Value")->item(0);
    if (!isset($value)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "Value", "NCIP Messaging Error Type Scheme");
    return array( "Unique" . $par . "Id" => array($par . "IdentifierValue" => $identifierValue->nodeValue, "UniqueAgencyId" => $value->nodeValue) );
  }
  
  
/** \brief _parse_item_optional_fields
*
* Fortolker et DOMElement, og henter Item Optional Fields info ud af det
*
* @param DOMElement $lookupResponse Det element, hvorfra Item Optional Fields info ønskes læst 
* @return array Item Optional Fields info
*
*/
  private function _parse_item_optional_fields($lookupResponse) {
    $ret = array();
    $itemOptionalFields = $lookupResponse->getElementsByTagName("ItemOptionalFields")->item(0);
    if (isset($itemOptionalFields)) {
      $bibliographicDescription = $itemOptionalFields->getElementsByTagName("BibliographicDescription")->item(0);
      if (isset($bibliographicDescription)) {
        $author = htmlentities($bibliographicDescription->getElementsByTagName("Author")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($author)) $ret["Author"] = $author;
        $authorOfComponent = htmlentities($bibliographicDescription->getElementsByTagName("AuthorOfComponent")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($authorOfComponent)) $ret["AuthorOfComponent"] = $authorOfComponent;
        $bibliographicItemId = htmlentities($bibliographicDescription->getElementsByTagName("BibliographicItemId")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($bibliographicItemId)) $ret["BibliographicItemId"] = $bibliographicItemId;
        $bibliographicRecordId = htmlentities($bibliographicDescription->getElementsByTagName("BibliographicRecordId")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($bibliographicRecordId)) $ret["BibliographicRecordId"] = $bibliographicRecordId;
        $componentId = $bibliographicDescription->getElementsByTagName("ComponentId")->item(0)->nodeValue;
        if (isset($componentId)) $ret["ComponentId"] = $componentId;
        $edition = htmlentities($bibliographicDescription->getElementsByTagName("Edition")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($edition)) $ret["Edition"] = $edition;
        $pagination = $bibliographicDescription->getElementsByTagName("Pagination")->item(0)->nodeValue;
        if (isset($pagination)) $ret["Pagination"] = $pagination;
        $placeOfPublication = htmlentities($bibliographicDescription->getElementsByTagName("PlaceOfPublication")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($placeOfPublication)) $ret["PlaceOfPublication"] = $placeOfPublication;
        $publicationDate = $bibliographicDescription->getElementsByTagName("PublicationDate")->item(0)->nodeValue;
        if (isset($publicationDate)) $ret["PublicationDate"] = $publicationDate;
        $publicationDateOfComponent = $bibliographicDescription->getElementsByTagName("PublicationDateOfComponent")->item(0)->nodeValue;
        if (isset($publicationDateOfComponent)) $ret["PublicationDateOfComponent"] = $publicationDateOfComponent;
        $publisher = htmlentities($bibliographicDescription->getElementsByTagName("Publisher")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($publisher)) $ret["Publisher"] = $publisher;
        $seriesTitleNumber = htmlentities($bibliographicDescription->getElementsByTagName("SeriesTitleNumber")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($seriesTitleNumber)) $ret["SeriesTitleNumber"] = $seriesTitleNumber;
        $title = htmlentities($bibliographicDescription->getElementsByTagName("Title")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($title)) $ret["Title"] = $title;
        $titleOfComponent = htmlentities($bibliographicDescription->getElementsByTagName("TitleOfComponent")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($titleOfComponent)) $ret["TitleOfComponent"] = $titleOfComponent;
        $bibliographicLevel = htmlentities($bibliographicDescription->getElementsByTagName("BibliographicLevel")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($bibliographicLevel)) $ret["BibliographicLevel"] = $bibliographicLevel;
        $sponsoringBody = htmlentities($bibliographicDescription->getElementsByTagName("SponsoringBody")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($sponsoringBody)) $ret["SponsoringBody"] = $sponsoringBody;
        $electronicDataFormatType = $bibliographicDescription->getElementsByTagName("ElectronicDataFormatType")->item(0)->nodeValue;
        if (isset($electronicDataFormatType)) $ret["ElectronicDataFormatType"] = $electronicDataFormatType;
        $language = htmlentities($bibliographicDescription->getElementsByTagName("Language")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($language)) $ret["Language"] = $language;
        $mediumType = htmlentities($bibliographicDescription->getElementsByTagName("MediumType")->item(0)->nodeValue, ENT_COMPAT, $this->dom->encoding);
        if (isset($mediumType)) $ret["MediumType"] = $mediumType;
      }
      // Eneste understøttede tag i <ItemOptionalFields> er <BibliographicDescription>, alle resterende tags understøttes ikke i denne version
    }    
    return $ret;
  }
  


/** \brief _parse_authentication_input
*
* Fortolker et DOMElement, og henter Authentication Input ud af det
*
* @param $authentication_input_type_name Typen på det Authentication Input elementet, der ønskes udvalgt
* @param DOMElement $lookupResponse Det element, hvorfra Authentication Input ønskes læst 
* @return string Værdien af AuthenticationInputData tagget i det givne elementet
*
*/
  private function _parse_authentication_input($authentication_input_type_name, $xml) {
    if (empty($xml)) return null;
    $authentication_input_tags = $xml->getElementsByTagName("AuthenticationInput");
    if (empty($authentication_input_tags)) return null;
    foreach ($authentication_input_tags as $item) {
      $type = $this->_parse_scheme_value_pair($item, "AuthenticationInputType");
      if ($type == $authentication_input_type_name) {
        return $item->getElementsByTagName("AuthenticationInputData")->item(0)->nodeValue;
      }
    }
    return null;
  }



/** \brief _parse_scheme_value_pair
*
* Fortolker et DOMElement, og henter et Scheme / Value par ud af det
*
* @param DOMElement $xml Det element, hvorfra Scheme / Value parret ønskes læst 
* @param string $name Tag navnet på det element, der indeholder Scheme / Value parret
* @return string værdien af <Value> tagget i Scheme / Value parret
*
*/
  private function _parse_scheme_value_pair($xml, $name=null) {
    if (empty($xml)) return null;
    if (!empty($name)) {
      $xml = $xml->getElementsByTagName($name)->item(0);
    }
    if (empty($xml)) return null;
    return $xml->getElementsByTagName("Value")->item(0)->nodeValue;
  }
  
  
/** \brief _parse_problem
*
* Fortolker et DOMElement, og henter Problem info ud af det
*
*  [Problem] array
*     [Error] => ProcessingError eller MessagingError
*     [Type] => User Authentication Failed
*     [Element] => AuthenticationInput
*     [Scheme] => "http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm"
*     [Value] => Value
*     
* @param DOMElement $xml Det element, hvorfra Problem info ønskes læst 
* @return array Problem info 
*
*/
  private function _parse_problem($xml) {
    if (!isset($xml)) return $this->_problem();
    $problem = $xml->getElementsByTagName("Problem")->item(0);
    if (!isset($problem)) return array();  // No problem detected

    $ret_value = array();
    
    $processingError = $problem->getElementsByTagName("ProcessingError")->item(0);
    if (isset($processingError)) {
      $ret_value["Error"] = "ProcessingError";

      $processingErrorType = $processingError->getElementsByTagName("ProcessingErrorType")->item(0);
      if (!isset($processingErrorType)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "ProcessingErrorType", "NCIP Messaging Error Type Scheme");
      $ret_value["Scheme"] = $processingErrorType->getElementsByTagName("Scheme")->item(0)->nodeValue;
      if (empty($ret_value["Scheme"])) unset($ret_value["Scheme"]);
      $ret_value["Type"] = $processingErrorType->getElementsByTagName("Value")->item(0)->nodeValue;
      if (empty($ret_value["Type"])) unset($ret_value["Type"]);

      $processingErrorElement = $processingError->getElementsByTagName("ProcessingErrorElement")->item(0);
//      if (!isset($processingErrorElement)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "ProcessingErrorElement", "NCIP Messaging Error Type Scheme");
      if (isset($processingErrorElement)) {
        $ret_value["Element"] = $processingErrorElement->getElementsByTagName("ElementName")->item(0)->nodeValue;
        if (empty($ret_value["Element"])) unset($ret_value["Element"]);
        $ret_value["Value"] = $processingErrorElement->getElementsByTagName("ProcessingErrorValue")->item(0)->nodeValue;
        if (empty($ret_value["Value"])) unset($ret_value["Value"]);
      }
      
      return array("Problem" => $ret_value);
    }

    $messagingError = $problem->getElementsByTagName("MessagingError")->item(0);
    if (isset($messagingError)) {
      $ret_value["Error"] = "MessagingError";

      $messagingErrorType = $messagingError->getElementsByTagName("MessagingErrorType")->item(0);
      if (!isset($messagingErrorType)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "MessagingErrorType", "NCIP Messaging Error Type Scheme");
      $ret_value["Scheme"] = $messagingErrorType->getElementsByTagName("Scheme")->item(0)->nodeValue;
      if (empty($ret_value["Scheme"])) unset($ret_value["Scheme"]);
      $ret_value["Type"] = $messagingErrorType->getElementsByTagName("Value")->item(0)->nodeValue;
      if (empty($ret_value["Type"])) unset($ret_value["Type"]);

      $messagingErrorElement = $messagingError->getElementsByTagName("MessagingErrorElement")->item(0);
      if (!isset($messagingErrorElement)) return $this->_problem("MessagingError", "Invalid Message Syntax Error", "MessagingErrorElement", "NCIP Messaging Error Type Scheme");
      $ret_value["Element"] = $messagingErrorElement->getElementsByTagName("ElementName")->item(0)->nodeValue;
      if (empty($ret_value["Element"])) unset($ret_value["Element"]);
      $ret_value["Value"] = $messagingErrorElement->getElementsByTagName("MessagingErrorValue")->item(0)->nodeValue;
      if (empty($ret_value["Value"])) unset($ret_value["Value"]);

      return array("Problem" => $ret_value);
    }
  return $this->_problem("MessagingError", "Invalid Message Syntax Error", "", "NCIP Messaging Error Type Scheme");
  }
  
}

?>