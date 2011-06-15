<?php

require_once('PHPUnit/Framework.php');
require_once('ncip_class.php');

 
class ncipClassTest extends PHPUnit_Framework_TestCase
{

  // ---------------------------------------------------------------------------
  // Test NCIP Requests
  // ---------------------------------------------------------------------------

  public static function parseRequests() {
    return array(

//------------------------------------------------------------------------------
// LookupUser
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupUser>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>
				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>
				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<AuthenticationInput>
			<AuthenticationInputData>1231231230</AuthenticationInputData>
			<AuthenticationDataFormatType>
				<Scheme>http://www.iana.org/assignments/media-types</Scheme>
				<Value>text/plain</Value>
			</AuthenticationDataFormatType>
			<AuthenticationInputType>
				<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/authenticationinputtype/authenticationinputtype.scm</Scheme>
				<Value>User ID</Value>
			</AuthenticationInputType>
		</AuthenticationInput>
		<AuthenticationInput>
			<AuthenticationInputData>1234</AuthenticationInputData>
			<AuthenticationDataFormatType>
				<Scheme>http://www.iana.org/assignments/media-types</Scheme>
				<Value>text/plain</Value>
			</AuthenticationDataFormatType>
			<AuthenticationInputType>
				<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/authenticationinputtype/authenticationinputtype.scm</Scheme>
				<Value>PIN</Value>
			</AuthenticationInputType>
		</AuthenticationInput>
		<UserElementType>
			<Scheme>http://www.niso.org/ncip/v1_0/schemes/userelementtype/userelementtype.scm</Scheme>
			<Value>Name Information</Value>
		</UserElementType>
		<LoanedItemsDesired/>
		<RequestedItemsDesired/>

	</LookupUser>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupUser',
  'FromAgencyId' => 'DK-190101',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-710100',
  'UserId' => '',
  'UserPIN' => '1234',
  'UserElementType' => 'Name Information',
  'LoanedItemsDesired' => 1,
  'RequestedItemsDesired' => 1,
)),

//------------------------------------------------------------------------------
// LookupItem
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItem>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>07197195</ItemIdentifierValue>
		</UniqueItemId>
		<ItemElementType>
			<Scheme>http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm</Scheme>
			<Value>Bibliographic Description</Value>
		</ItemElementType>
	</LookupItem>

</NCIPMessage>
',
array(
  'Ncip' => 'LookupItem',
  'FromAgencyId' => 'DK-190101',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-710100',
  'UniqueItemId' => Array (
    'ItemIdentifierValue' => '07197195',
    'UniqueAgencyId' => 'DK-710100',
  ),
  'ItemElementType' => 'Bibliographic Description',
)),

//------------------------------------------------------------------------------
// LookupRequest
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupRequest>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<RequestIdentifierValue>87654321</RequestIdentifierValue>
		</UniqueRequestId>
		<ItemElementType>
			<Scheme>http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm</Scheme>
			<Value>Bibliographic Description</Value>
		</ItemElementType>
	</LookupRequest>

</NCIPMessage>
',
array(
  'Ncip' => 'LookupRequest',
  'FromAgencyId' => 'DK-190101',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-710100',
  'UniqueRequestId' => Array (
    'RequestIdentifierValue' => '87654321',
    'UniqueAgencyId' => 'DK-710100',
  ),
  'ItemElementType' => 'Bibliographic Description',
)),

//------------------------------------------------------------------------------
// RenewItem
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItem>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190103</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueUserId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-715700</Value>
			</UniqueAgencyId>

			<UserIdentifierValue>1231231230</UserIdentifierValue>
		</UniqueUserId>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-715700</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>27175953</ItemIdentifierValue>
		</UniqueItemId>
	</RenewItem>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItem',
  'FromAgencyId' => 'DK-190103',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-715700',
  'UniqueUserId' => Array (
         'UserIdentifierValue' => '1231231230',
         'UniqueAgencyId' => 'DK-715700',
     ),
  'UniqueItemId' => Array (
         'ItemIdentifierValue' => '27175953',
         'UniqueAgencyId' => 'DK-715700',
     ),
)),

//------------------------------------------------------------------------------
// CancelRequestItem
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<CancelRequestItem>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-870970</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueUserId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<UserIdentifierValue>1231231230</UserIdentifierValue>
		</UniqueUserId>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<RequestIdentifierValue>87654321</RequestIdentifierValue>
		</UniqueRequestId>
		<RequestType>
			<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
			<Value>Hold</Value>
		</RequestType>
	</CancelRequestItem>

</NCIPMessage>
',
array(
  'Ncip' => 'CancelRequestItem',
  'FromAgencyId' => 'DK-870970',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueUserId' => Array (
    'UserIdentifierValue' => '1231231230',
    'UniqueAgencyId' => 'DK-710100',
  ),
  'UniqueRequestId' => Array (
    'RequestIdentifierValue' => '87654321',
    'UniqueAgencyId' => 'DK-710100',
  ),
  'RequestType' => 'Hold',
)),

//------------------------------------------------------------------------------
// CancelRequestItem Combined Public School Libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <CancelRequestItem>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <UserIdentifierValue>{34944008-44e1-4469-8768-8d0506ca4b62}</UserIdentifierValue>
    </UniqueUserId>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>{3DE8CBA5-6F59-403D-8BA1-4D5535557D50}</RequestIdentifierValue>
    </UniqueRequestId>
    <RequestType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/requesttype/requesttype.scm</Scheme>
      <Value>Hold</Value>
    </RequestType>

  </CancelRequestItem>
</NCIPMessage>',
array(
  'Ncip' => 'CancelRequestItem',
  'FromAgencyId' => 'DK-775100',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-675143',
  'UniqueUserId' => Array (
    'UserIdentifierValue' => '{34944008-44e1-4469-8768-8d0506ca4b62}',
    'UniqueAgencyId' => 'DK-675143',
  ),
  'UniqueRequestId' => Array (
    'RequestIdentifierValue' => '{3DE8CBA5-6F59-403D-8BA1-4D5535557D50}',
    'UniqueAgencyId' => 'DK-675143',
  ),
  'RequestType' => 'Hold',
)),

//------------------------------------------------------------------------------
// LookupItem Combined Public School Libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupItem>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <VisibleItemId>
      <VisibleItemIdentifierType>
        <Scheme>http://www.niso.org/ncip/v1_0/schemes/visibleitemidentifiertype/visibleitemidentifiertype.scm</Scheme>
        <Value>Barcode</Value>

      </VisibleItemIdentifierType>
      <VisibleItemIdentifier>3349908882</VisibleItemIdentifier>
    </VisibleItemId>
    <ItemElementType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm</Scheme>
      <Value>Bibliographic Description</Value>
    </ItemElementType>

    <ItemElementType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm</Scheme>
      <Value>Item Description</Value>
    </ItemElementType>
    <ItemElementType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm</Scheme>
      <Value>Circulation Status</Value>

    </ItemElementType>
  </LookupItem>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItem',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'Problem' => Array (
         'Error' => 'MessagingError',
         'Type' => 'Invalid Message Syntax Error',
         'Element' => 'UniqueItemId',
         'Scheme' => 'NCIP Messaging Error Type Scheme',
     ),
)),

//------------------------------------------------------------------------------
// LookupRequest Combined Public School Libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupRequest>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>{E90E8B77-5482-43ED-81B6-2E5487FA3073}</RequestIdentifierValue>
    </UniqueRequestId>
  </LookupRequest>
</NCIPMessage>',
array(
  'Ncip' => 'LookupRequest',
  'FromAgencyId' => 'DK-775100',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-675143',
  'UniqueRequestId' => Array (
          'RequestIdentifierValue' => '{E90E8B77-5482-43ED-81B6-2E5487FA3073}',
          'UniqueAgencyId' => 'DK-675143',
      ),
  'ItemElementType' => '',
)),

//------------------------------------------------------------------------------
// LookupUser Combined Public School Libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupUser>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <VisibleUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <VisibleUserIdentifierType>
        <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/visibleuseridentifiertype/visibleuseridentifiertype.scm</Scheme>
        <Value>Barcode</Value>
      </VisibleUserIdentifierType>
      <VisibleUserIdentifier>0010295009</VisibleUserIdentifier>
    </VisibleUserId>

    <UserElementType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/userelementtype/userelementtype.scm</Scheme>
      <Value>Date Of Birth</Value>
    </UserElementType>
    <UserElementType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/userelementtype/userelementtype.scm</Scheme>
      <Value>Name Information</Value>

    </UserElementType>
    <UserElementType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/userelementtype/userelementtype.scm</Scheme>
      <Value>User Privilege</Value>
    </UserElementType>
    <UserElementType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/userelementtype/userelementtype.scm</Scheme>

      <Value>Visible User Id</Value>
    </UserElementType>
  </LookupUser>
</NCIPMessage>',
array(
  'Ncip' => 'LookupUser',
  'FromAgencyId' => 'DK-775100',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-675143',
  'UserId' => '',
  'UserPIN' => '',
  'UserElementType' => 'Date Of Birth',
)),

//------------------------------------------------------------------------------
// RenewItem Combined Public School Libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <RenewItem>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <UserIdentifierValue>{34944008-44e1-4469-8768-8d0506ca4b62}</UserIdentifierValue>
    </UniqueUserId>
    <UniqueItemId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-775100</Value>

      </UniqueAgencyId>
      <ItemIdentifierValue>{1A57239B-13FD-40FF-B3EE-9F34237D6924}</ItemIdentifierValue>
    </UniqueItemId>
    <DesiredDateDue>2008-10-23T00:00:00</DesiredDateDue>
  </RenewItem>
</NCIPMessage>',
array(
  'Ncip' => 'RenewItem',
  'FromAgencyId' => 'DK-775100',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-675143',
  'UniqueUserId' => Array (
          'UserIdentifierValue' => '{34944008-44e1-4469-8768-8d0506ca4b62}',
          'UniqueAgencyId' => 'DK-675143',
      ),
  'UniqueItemId' => Array (
          'ItemIdentifierValue' => '{1A57239B-13FD-40FF-B3EE-9F34237D6924}',
          'UniqueAgencyId' => 'DK-775100',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
  <RequestItem>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-190101</Value>

        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-715700</Value>

        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-715700</Value>
      </UniqueAgencyId>

      <UserIdentifierValue>1231231230</UserIdentifierValue>
    </UniqueUserId>
    <UniqueBibliographicId>
      <BibliographicRecordId>
        <BibliographicRecordIdentifier>44397315</BibliographicRecordIdentifier>
        <BibliographicRecordIdentifierCode>
          <Scheme>http://biblstandard.dk/ncip/schemes/faust/1.0/</Scheme>

          <Value>FAUST</Value>
        </BibliographicRecordIdentifierCode>
      </BibliographicRecordId>
    </UniqueBibliographicId>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-190101</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>23770051</RequestIdentifierValue>
    </UniqueRequestId>
    <RequestType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
      <Value>Hold</Value>
    </RequestType>

    <RequestScopeType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requestscopetype/requestscopetype.scm</Scheme>
      <Value>Bibliographic Item</Value>
    </RequestScopeType>
    <NeedBeforeDate>2008-09-29T00:00+01:00</NeedBeforeDate>
  </RequestItem>
</NCIPMessage>',
array(
  'Problem' => Array (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'RequestItem',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
  <RequestItem>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-190101</Value>

        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-715700</Value>

        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-715700</Value>
      </UniqueAgencyId>

      <UserIdentifierValue>1231231230</UserIdentifierValue>
    </UniqueUserId>
    <UniqueBibliographicId>
      <BibliographicRecordId>
        <BibliographicRecordIdentifier>1468410</BibliographicRecordIdentifier>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-820030</Value>
        </UniqueAgencyId>
      </BibliographicRecordId>
    </UniqueBibliographicId>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-190101</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>23770052</RequestIdentifierValue>
    </UniqueRequestId>
    <RequestType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
      <Value>Hold</Value>
    </RequestType>

    <RequestScopeType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requestscopetype/requestscopetype.scm</Scheme>
      <Value>Bibliographic Item</Value>
    </RequestScopeType>
    <NeedBeforeDate>2008-09-29T00:00+01:00</NeedBeforeDate>
  </RequestItem>
</NCIPMessage>
',
array(
  'Problem' => Array (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'RequestItem',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
  <RequestItem>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-190101</Value>

        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-715702</Value>

        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-715700</Value>
      </UniqueAgencyId>

      <UserIdentifierValue>1231231230</UserIdentifierValue>
    </UniqueUserId>
    <UniqueBibliographicId>
      <BibliographicRecordId>
        <BibliographicRecordIdentifier>44397315</BibliographicRecordIdentifier>
        <BibliographicRecordIdentifierCode>
          <Scheme>http://biblstandard.dk/ncip/schemes/faust/1.0/</Scheme>

          <Value>FAUST</Value>
        </BibliographicRecordIdentifierCode>
      </BibliographicRecordId>
    </UniqueBibliographicId>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-190101</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>23770053</RequestIdentifierValue>
    </UniqueRequestId>
    <RequestType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
      <Value>Hold</Value>
    </RequestType>

    <RequestScopeType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requestscopetype/requestscopetype.scm</Scheme>
      <Value>Bibliographic Item</Value>
    </RequestScopeType>
    <NeedBeforeDate>2008-09-29T00:00+01:00</NeedBeforeDate>
  </RequestItem>
</NCIPMessage>
',
array(
  'Problem' => Array
      (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'RequestItem',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
  <RequestItem>
    <InitiationHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-190101</Value>

        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/ncip/schemes/agencysubdiv/1.0/</Scheme>
          <Value>DK-737606_Alstrup</Value>

        </UniqueAgencyId>
      </ToAgencyId>
    </InitiationHeader>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-737606</Value>
      </UniqueAgencyId>

      <UserIdentifierValue>1231231230</UserIdentifierValue>
    </UniqueUserId>
    <UniqueBibliographicId>
      <BibliographicRecordId>
        <BibliographicRecordIdentifier>44397315</BibliographicRecordIdentifier>
        <BibliographicRecordIdentifierCode>
          <Scheme>http://biblstandard.dk/ncip/schemes/faust/1.0/</Scheme>

          <Value>FAUST</Value>
        </BibliographicRecordIdentifierCode>
      </BibliographicRecordId>
    </UniqueBibliographicId>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-190101</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>23770053</RequestIdentifierValue>
    </UniqueRequestId>
    <RequestType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
      <Value>Hold</Value>
    </RequestType>

    <RequestScopeType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requestscopetype/requestscopetype.scm</Scheme>
      <Value>Bibliographic Item</Value>
    </RequestScopeType>
    <NeedBeforeDate>2008-04-29T00:00+01:00</NeedBeforeDate>
  </RequestItem>
</NCIPMessage>
',
array(
  'Problem' => Array
      (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'RequestItem',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RequestItem>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueUserId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-715700</Value>
			</UniqueAgencyId>

			<UserIdentifierValue>1231231230</UserIdentifierValue>
		</UniqueUserId>
		<UniqueBibliographicId>
			<BibliographicRecordId>
				<BibliographicRecordIdentifier>44397315</BibliographicRecordIdentifier>
				<BibliographicRecordIdentifierCode>
					<Scheme>http://biblstandard.dk/ncip/schemes/faust/1.0/</Scheme>

					<Value>FAUST</Value>
				</BibliographicRecordIdentifierCode>
			</BibliographicRecordId>
		</UniqueBibliographicId>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-190101</Value>

			</UniqueAgencyId>
			<RequestIdentifierValue>23770051</RequestIdentifierValue>
		</UniqueRequestId>
		<RequestType>
			<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
			<Value>Hold</Value>
		</RequestType>

		<RequestScopeType>
			<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requestscopetype/requestscopetype.scm</Scheme>
			<Value>Bibliographic Item</Value>
		</RequestScopeType>
		<ShippingInformation>
			<PhysicalAddress>
				<StructuredAddress>
					<District></District>

					<Locality>hb</Locality>  
				</StructuredAddress>
				<PhysicalAddressType>
					<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/physicaladdresstype/physicaladdresstype.scm</Scheme>
					<Value>Postal Address</Value>
				</PhysicalAddressType>
			</PhysicalAddress>
		</ShippingInformation>

		<NeedBeforeDate>2008-09-29T00:00:00+01:00</NeedBeforeDate>
	</RequestItem>
</NCIPMessage>
',
array(
  'Problem' => Array
      (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'RequestItem',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItem>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>07197195</ItemIdentifierValue>
		</UniqueItemId>
		<ItemElementType>
			<Scheme>http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm</Scheme>
			<Value>Bibliographic Description</Value>
		</ItemElementType>
	</LookupItem>

</NCIPMessage>
',
array(
  'Ncip' => 'LookupItem',
  'FromAgencyId' => 'DK-190101',
  'FromAgencyAuthentication' =>  '[PASSWORD]',
  'ToAgencyId' => 'DK-710100',
  'UniqueItemId' => Array (
          'ItemIdentifierValue' => '07197195',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'ItemElementType' => 'Bibliographic Description',
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupRequest>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<RequestIdentifierValue>87654321</RequestIdentifierValue>
		</UniqueRequestId>
		<ItemElementType>
			<Scheme>http://www.niso.org/ncip/v1_0/schemes/itemelementtype/itemelementtype.scm</Scheme>
			<Value>Bibliographic Description</Value>
		</ItemElementType>
	</LookupRequest>

</NCIPMessage>
',
array(
  'Ncip' => 'LookupRequest',
  'FromAgencyId' => 'DK-190101',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-710100',
  'UniqueRequestId' => Array (
          'RequestIdentifierValue' => '87654321',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'ItemElementType' => 'Bibliographic Description',
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupUser>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<AuthenticationInput>
			<AuthenticationInputData>1231231230</AuthenticationInputData>
			<AuthenticationDataFormatType>
				<Scheme>http://www.iana.org/assignments/media-types</Scheme>
				<Value>text/plain</Value>

			</AuthenticationDataFormatType>
			<AuthenticationInputType>
				<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/authenticationinputtype/authenticationinputtype.scm</Scheme>
				<Value>User ID</Value>
			</AuthenticationInputType>
		</AuthenticationInput>
		<AuthenticationInput>
			<AuthenticationInputData>1234</AuthenticationInputData>

			<AuthenticationDataFormatType>
				<Scheme>http://www.iana.org/assignments/media-types</Scheme>
				<Value>text/plain</Value>
			</AuthenticationDataFormatType>
			<AuthenticationInputType>
				<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/authenticationinputtype/authenticationinputtype.scm</Scheme>
				<Value>PIN</Value>

			</AuthenticationInputType>
		</AuthenticationInput>
		<UserElementType>
			<Scheme>http://www.niso.org/ncip/v1_0/schemes/userelementtype/userelementtype.scm</Scheme>
			<Value>Name Information</Value>
		</UserElementType>
		<LoanedItemsDesired/>
		<RequestedItemsDesired/>

	</LookupUser>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupUser',
  'FromAgencyId' => 'DK-190101',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-710100',
  'UserId' => '',
  'UserPIN' => '1234',
  'UserElementType' => 'Name Information',
  'LoanedItemsDesired' => 1,
  'RequestedItemsDesired' => 1,
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItem>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190103</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueUserId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-715700</Value>
			</UniqueAgencyId>

			<UserIdentifierValue>1231231230</UserIdentifierValue>
		</UniqueUserId>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-715700</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>27175953</ItemIdentifierValue>
		</UniqueItemId>
	</RenewItem>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItem',
  'FromAgencyId' => 'DK-190103',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-715700',
  'UniqueUserId' => Array (
          'UserIdentifierValue' => '1231231230',
          'UniqueAgencyId' => 'DK-715700',
      ),
  'UniqueItemId' => Array (
          'ItemIdentifierValue' => '27175953',
          'UniqueAgencyId' => 'DK-715700',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<CreateUser>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<VisibleUserId>
			<VisibleUserIdentifierType>
				<Scheme>http://biblstandard.dk/ncip/schemes/CPR/1.0/</Scheme>
				<Value>CPR</Value>
			</VisibleUserIdentifierType>

			<VisibleUserIdentifier>0212611237</VisibleUserIdentifier>
		</VisibleUserId>
		<NameInformation>
			<PersonalNameInformation>
				<StructuredPersonalUserName>
					<GivenName>Givenname</GivenName>
					<Surname>Surname</Surname>

				</StructuredPersonalUserName>
			</PersonalNameInformation>
		</NameInformation>
		<UserAddressInformation>
			<UserAddressRoleType>
				<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/useraddressroletype/useraddressroletype.scm</Scheme>
				<Value>Home</Value>
			</UserAddressRoleType>

			<PhysicalAddress>
				<StructuredAddress>
					<Street>Humlebakken 17</Street>
					<District>851</District>
					<PostalCode>9220  Aalborg Øst</PostalCode>
				</StructuredAddress>
				<PhysicalAddressType>

					<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/physicaladdresstype/physicaladdresstype.scm</Scheme>
					<Value>Postal Address</Value>
				</PhysicalAddressType>
			</PhysicalAddress>
		</UserAddressInformation>
		<DateOfBirth>1961-02-12T00:00:00.0Z</DateOfBirth>
	</CreateUser>

</NCIPMessage>
',
array(
  'Problem' => Array (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'CreateUser',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<UpdateRequestItem>
		<InitiationHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</InitiationHeader>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-190101</Value>
			</UniqueAgencyId>

			<RequestIdentifierValue>23770051</RequestIdentifierValue>
		</UniqueRequestId>
		<DeleteRequestFields>
			<ShippingInformation>
				<PhysicalAddress>
					<StructuredAddress>
						<District></District>
						<Locality>hb</Locality>  
					</StructuredAddress>

					<PhysicalAddressType>
						<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/physicaladdresstype/physicaladdresstype.scm</Scheme>
						<Value>Postal Address</Value>
					</PhysicalAddressType>
				</PhysicalAddress>
			</ShippingInformation>
		</DeleteRequestFields>
		<AddRequestFields>

			<ShippingInformation>
				<PhysicalAddress>
					<StructuredAddress>
						<District></District>
						<Locality>kl</Locality>  
					</StructuredAddress>
					<PhysicalAddressType>
						<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/physicaladdresstype/physicaladdresstype.scm</Scheme>

						<Value>Postal Address</Value>
					</PhysicalAddressType>
				</PhysicalAddress>
			</ShippingInformation>
		</AddRequestFields>
	</UpdateRequestItem>
</NCIPMessage>
',
array(
  'Problem' => Array (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'UpdateRequestItem',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
    );
  }

  /**
  * @dataProvider parseRequests
  */
  public function testParseRequests($input, $expected_result) {
    $ncip = new ncip();
    $this->assertEquals($expected_result, $ncip->parse(trim($input)));
  }



//==============================================================================
//==============================================================================

  // ---------------------------------------------------------------------------
  // Test NCIP Responses
  // ---------------------------------------------------------------------------

  public static function parseResponses() {
    return array(

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>12345678</ItemIdentifierValue>
		</UniqueItemId>
		<ItemOptionalFields>
			<BibliographicDescription>
				<Author>Author</Author>
				<PublicationDate>Year</PublicationDate>
				<Title>Title</Title>

			</BibliographicDescription>
		</ItemOptionalFields>
	</LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueItemId' => Array
      (
          'ItemIdentifierValue' => '12345678',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'Author' => 'Author',
  'PublicationDate' => 'Year',
  'Title' => 'Title',
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupitemprocessingerror.scm</Scheme>
					<Value>Unknown Item</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>UniqueItemId</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupitemprocessingerror.scm',
          'Type' => 'Unknown Item',
          'Element' => 'UniqueItemId',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupRequestResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<RequestIdentifierValue>87654321</RequestIdentifierValue>
		</UniqueRequestId>
		<ItemOptionalFields>
			<BibliographicDescription>
				<Author>Author</Author>
				<PublicationDate>Year</PublicationDate>
				<Title>Title</Title>

			</BibliographicDescription>
		</ItemOptionalFields>
	</LookupRequestResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupRequestResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueRequestId' => Array
      (
          'RequestIdentifierValue' => '87654321',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'Author' => 'Author',
  'PublicationDate' => 'Year',
  'Title' => 'Title',
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_01/schemes/processingerrortype/lookuprequestprocessingerror.scm</Scheme>
					<Value>Unknown Request</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>UniqueRequestId</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_01/schemes/processingerrortype/lookuprequestprocessingerror.scm',
          'Type' => 'Unknown Request',
          'Element' => 'UniqueRequestId',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupUserResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueUserId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<UserIdentifierValue>1231231230</UserIdentifierValue>
		</UniqueUserId>
		<UserTransaction>
			<RequestedItem>
				<UniqueRequestId>
					<UniqueAgencyId>
						<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
						<Value>DK-710100</Value>

					</UniqueAgencyId>
					<RequestIdentifierValue>87654321</RequestIdentifierValue>
				</UniqueRequestId>
				<RequestType>
					<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
					<Value>Hold</Value>
				</RequestType>

				<RequestStatusType>
					<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requeststatustype/requeststatustype.scm</Scheme>
					<Value>In Process</Value>
				</RequestStatusType>
				<DatePlaced>2008-01-10T14:35+01:00</DatePlaced>
			</RequestedItem>
			<LoanedItem>

				<UniqueItemId>
					<UniqueAgencyId>
						<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
						<Value>DK-710100</Value>
					</UniqueAgencyId>
					<ItemIdentifierValue>12345678</ItemIdentifierValue>
				</UniqueItemId>

				<ReminderLevel>1</ReminderLevel>
				<DateDue>2008-03-16T20:00+01:00</DateDue>
				<Amount>
					<CurrencyCode>
						<Scheme>http://www.bsi-global.com/Technical+Information/Publications/_Publications/tig90x.doc</Scheme>
						<Value>DKK</Value>
					</CurrencyCode>

					<MonetaryValue>0</MonetaryValue>
				</Amount>
			</LoanedItem>
		</UserTransaction>
		<UserOptionalFields>
			<NameInformation>
				<PersonalNameInformation>
					<StructuredPersonalUserName>

						<GivenName>Givenname</GivenName>
						<Surname>Surname</Surname>
					</StructuredPersonalUserName>
				</PersonalNameInformation>
			</NameInformation>
		</UserOptionalFields>
	</LookupUserResponse>
</NCIPMessage>

',
array(
  'Ncip' => 'LookupUserResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueUserId' => Array
      (
          'UserIdentifierValue' => '1231231230',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'RequestedItem' => Array
      (
          '0' => Array
              (
                  'UniqueRequestId' => Array
                      (
                          'RequestIdentifierValue' => '87654321',
                          'UniqueAgencyId' => 'DK-710100',
                      ),
                  'RequestType' => 'Hold',
                  'RequestStatusType' => 'In Process',
                  'DatePlaced' => '1199972100',
              ),
      ),
  'LoanedItem' => Array
      (
          '0' => Array
              (
                  'UniqueItemId' => Array
                      (
                          'ItemIdentifierValue' => '12345678',
                          'UniqueAgencyId' => 'DK-710100',
                      ),
                  'ReminderLevel' => 1,
                  'DateDue' => '1205694000',
                  'CurrencyCode' => 'DKK',
                  'MonetaryValue' => 0,
              ),
      ),
  'GivenName' => 'Givenname',
  'Surname' => 'Surname',
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupUserResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm</Scheme>
					<Value>User Authentication Failed</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>AuthenticationInput</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</LookupUserResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupUserResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm',
          'Type' => 'User Authentication Failed',
          'Element' => 'AuthenticationInput',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190103</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-715700</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>27175953</ItemIdentifierValue>
		</UniqueItemId>
		<DateDue>2008-09-20T10:00+01:00</DateDue>
	</RenewItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-190103',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-715700',
  'UniqueItemId' => Array
      (
          'ItemIdentifierValue' => '27175953',
          'UniqueAgencyId' => 'DK-715700',
      ),
  'DateDue' => '1221901200',
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190103</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Pending>
			<DateOfExpectedReply>2008-09-20T10:00+01:00</DateOfExpectedReply>
		</Pending>
	</RenewItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-190103',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-715700',
  'DateOfExpectedReply' => '1221901200',
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm</Scheme>
					<Value>Maximum Renewals Exceeded</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>UniqueItemId</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</RenewItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm',
          'Type' => 'Maximum Renewals Exceeded',
          'Element' => 'UniqueItemId',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<CancelRequestItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<RequestIdentifierValue>87654321</RequestIdentifierValue>
		</UniqueRequestId>
		<UniqueUserId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<UserIdentifierValue>1231231230</UserIdentifierValue>
		</UniqueUserId>
	</CancelRequestItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'CancelRequestItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueUserId' => Array
      (
          'UserIdentifierValue' => '1231231230',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'UniqueRequestId' => Array
      (
          'RequestIdentifierValue' => '87654321',
          'UniqueAgencyId' => 'DK-710100',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between bibliotek.dk and local library systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<CancelRequestItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/cancelrequestitemprocessingerror.scm</Scheme>
					<Value>Request Already Processed</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>UniqueRequestId</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</CancelRequestItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'CancelRequestItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/cancelrequestitemprocessingerror.scm',
          'Type' => 'Request Already Processed',
          'Element' => 'UniqueRequestId',
      ),
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <CancelRequestItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>{3DE8CBA5-6F59-403D-8BA1-4D5535557D50}</RequestIdentifierValue>
    </UniqueRequestId>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <UserIdentifierValue>{34944008-44e1-4469-8768-8d0506ca4b62}</UserIdentifierValue>
    </UniqueUserId>
    <UserOptionalFields>
      <VisibleUserId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-675143</Value>

        </UniqueAgencyId>
        <VisibleUserIdentifierType>
          <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/visibleuseridentifiertype/visibleuseridentifiertype.scm</Scheme>
          <Value>Barcode</Value>
        </VisibleUserIdentifierType>
        <VisibleUserIdentifier>0010295009</VisibleUserIdentifier>
      </VisibleUserId>

      <NameInformation>
        <PersonalNameInformation>
          <StructuredPersonalUserName>
            <GivenName>John</GivenName>
            <Surname>Smith</Surname>
          </StructuredPersonalUserName>
        </PersonalNameInformation>
      </NameInformation>

      <DateOfBirth>1980-11-23T00:00:00</DateOfBirth>
    </UserOptionalFields>
  </CancelRequestItemResponse>
</NCIPMessage>',
array(
  'Ncip' => 'CancelRequestItemResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'UniqueUserId' => Array
      (
          'UserIdentifierValue' => '{34944008-44e1-4469-8768-8d0506ca4b62}',
          'UniqueAgencyId' => 'DK-675143',
      ),
  'UniqueRequestId' => Array
      (
          'RequestIdentifierValue' => '{3DE8CBA5-6F59-403D-8BA1-4D5535557D50}',
          'UniqueAgencyId' => 'DK-675143',
      ),
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <CancelRequestItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <Problem>
      <ProcessingError>
        <ProcessingErrorType>
          <Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/cancelrequestitemprocessingerror.scm</Scheme>

          <Value>Unknown Request</Value>
        </ProcessingErrorType>
        <ProcessingErrorElement>
          <ElementName>UniqueRequestId</ElementName>
          <ProcessingErrorElement>
            <ElementName>RequestIdentifierValue</ElementName>
            <ProcessingErrorValue>{3DE8CBA5-6F59-403D-8BA1-4D5535557D50}</ProcessingErrorValue>

          </ProcessingErrorElement>
        </ProcessingErrorElement>
      </ProcessingError>
    </Problem>
  </CancelRequestItemResponse>
</NCIPMessage>',
array(
  'Ncip' => 'CancelRequestItemResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/cancelrequestitemprocessingerror.scm',
          'Type' => 'Unknown Request',
          'Element' => 'UniqueRequestId',
          'Value' => '{3DE8CBA5-6F59-403D-8BA1-4D5535557D50}',
      ),
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
/* Her er der en fejl i den gamle ncip, idet jeg fortolker et BibliographicItemId og et BibliographicRecordId som PCDATA, selvom det indeholder nestede tags
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <UniqueItemId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-775100</Value>

      </UniqueAgencyId>
      <ItemIdentifierValue>{c8641421-20ba-4d1c-8a26-0e7c6efb6455}</ItemIdentifierValue>
    </UniqueItemId>
    <ItemOptionalFields>
      <BibliographicDescription>
        <Author>Haugaard, Erik Christian</Author>
        <BibliographicItemId>
          <BibliographicItemIdentifier>87-14-18458-3</BibliographicItemIdentifier>

          <BibliographicItemIdentifierCode>
            <Scheme>http://www.niso.org/ncip/v1_0/schemes/bibliographicitemidentifiercode/bibliographicitemidentifiercode.scm</Scheme>
            <Value>ISBN</Value>
          </BibliographicItemIdentifierCode>
        </BibliographicItemId>
        <BibliographicRecordId>
          <BibliographicRecordIdentifier>0 614 248 6</BibliographicRecordIdentifier>

          <BibliographicRecordIdentifierCode>
            <Scheme>http://biblstandard.dk/ncip/schemes/faust/1.0/</Scheme>
            <Value>FAUST</Value>
          </BibliographicRecordIdentifierCode>
        </BibliographicRecordId>
        <PublicationDate>1984</PublicationDate>
        <Publisher>Høst</Publisher>

        <Title>Samuraiens søn</Title>
      </BibliographicDescription>
      <CirculationStatus>
        <Scheme>http://www.niso.org/ncip/v1_0/schemes/circulationstatus/circulationstatus.scm</Scheme>
        <Value>Circulation Status Undefined</Value>
      </CirculationStatus>
      <ItemDescription>

        <VisibleItemId>
          <VisibleItemIdentifierType>
            <Scheme>http://www.niso.org/ncip/v1_0/schemes/visibleitemidentifiertype/visibleitemidentifiertype.scm</Scheme>
            <Value>Barcode</Value>
          </VisibleItemIdentifierType>
          <VisibleItemIdentifier>3349908882</VisibleItemIdentifier>
        </VisibleItemId>

      </ItemDescription>
    </ItemOptionalFields>
  </LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-775100',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-675143',
  'UniqueItemId' => Array
      (
          'ItemIdentifierValue' => '{c8641421-20ba-4d1c-8a26-0e7c6efb6455}',
          'UniqueAgencyId' => 'DK-775100',
      ),
  'Author' => 'Haugaard, Erik Christian',
  'BibliographicItemId' => '
        87-14-18458-3

        
          http://www.niso.org/ncip/v1_0/schemes/bibliographicitemidentifiercode/bibliographicitemidentifiercode.scm
          ISBN
        
      ',
  'BibliographicRecordId' => '
        0 614 248 6

        
          http://biblstandard.dk/ncip/schemes/faust/1.0/
          FAUST
        
      ',
  'PublicationDate' => '1984',
  'Publisher' => 'Høst',
  'Title' => 'Samuraiens søn',
)),
*/

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <Problem>
      <ProcessingError>
        <ProcessingErrorType>
          <Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupitemprocessingerror.scm</Scheme>

          <Value>Unknown Item</Value>
        </ProcessingErrorType>
        <ProcessingErrorElement>
          <ElementName>VisibleItemId</ElementName>          
          <ProcessingErrorElement>
            <ElementName>VisibleItemIdentifier</ElementName>
            <ProcessingErrorValue>3349908882</ProcessingErrorValue>

          </ProcessingErrorElement>
        </ProcessingErrorElement>
      </ProcessingError>
    </Problem>
  </LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-775100',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-675143',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupitemprocessingerror.scm',
          'Type' => 'Unknown Item',
          'Element' => 'VisibleItemId',
          'Value' => '3349908882',
      ),
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupRequestResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <RequestIdentifierValue>{E90E8B77-5482-43ED-81B6-2E5487FA3073}</RequestIdentifierValue>
    </UniqueRequestId>    
    <UniqueItemId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-775100</Value>
      </UniqueAgencyId>

      <ItemIdentifierValue>{2F9AD5AC-D4CF-488D-90EF-18BE6B86B221}</ItemIdentifierValue>
    </UniqueItemId>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>
      </UniqueAgencyId>

      <UserIdentifierValue>{3199cfb9-f361-47af-96d8-eb23ba577768}</UserIdentifierValue>
    </UniqueUserId>
    <RequestType>
      <Scheme>http://www.niso.org/ncip/v1_0/schemes/requesttype/requesttype.scm</Scheme>
      <Value>Hold</Value>
    </RequestType>
    <RequestScopeType>

      <Scheme>http://www.niso.org/ncip/v1_0/schemes/requestscopetype/requestscopetype.scm</Scheme>
      <Value>Bibliographic Item</Value>
    </RequestScopeType>
    <RequestStatusType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requeststatustype/requeststatustype.scm</Scheme>
      <Value>Available For Pickup</Value>
    </RequestStatusType>

    <HoldQueuePosition>1</HoldQueuePosition>
    <ShippingInformation>
      <PhysicalAddress>
        <StructuredAddress>
          <Line1>Egå Havevej 5</Line1>
          <Locality>Egå</Locality>
          <PostalCode>8250</PostalCode>

        </StructuredAddress>
        <PhysicalAddressType>
          <Scheme>http://www.niso.org/ncip/v1_0/schemes/physicaladdresstype/physicaladdresstype.scm</Scheme>
          <Value>Street Address</Value>
        </PhysicalAddressType>
      </PhysicalAddress>
    </ShippingInformation>
    <NeedBeforeDate>2008-10-16T00:00:00</NeedBeforeDate>

    <UserOptionalFields>
      <VisibleUserId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-675143</Value>
        </UniqueAgencyId>
        <VisibleUserIdentifierType>
          <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/visibleuseridentifiertype/visibleuseridentifiertype.scm</Scheme>

          <Value>Barcode</Value>
        </VisibleUserIdentifierType>
        <VisibleUserIdentifier>0010295009</VisibleUserIdentifier>
      </VisibleUserId>
      <NameInformation>
        <PersonalNameInformation>
          <StructuredPersonalUserName>
            <GivenName>John</GivenName>

            <Surname>Smith</Surname>
          </StructuredPersonalUserName>
        </PersonalNameInformation>
      </NameInformation>
      <DateOfBirth>1980-11-23T00:00:00</DateOfBirth>
    </UserOptionalFields>
  </LookupRequestResponse>
</NCIPMessage>

',
array(
  'Ncip' => 'LookupRequestResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'UniqueRequestId' => Array
      (
          'RequestIdentifierValue' => '{E90E8B77-5482-43ED-81B6-2E5487FA3073}',
          'UniqueAgencyId' => 'DK-675143',
      ),
  'HoldQueuePosition' => 1,
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupRequestResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <Problem>
      <ProcessingError>
        <ProcessingErrorType>
          <Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/generalprocessingerror.scm</Scheme>

          <Value>Agency Authentication Failed</Value>
        </ProcessingErrorType>
        <ProcessingErrorElement>
          <ElementName>InitiationHeader</ElementName>
          <ProcessingErrorElement>
            <ElementName>FromAgencyAuthentication</ElementName>
            <ProcessingErrorValue>xxxxx</ProcessingErrorValue>

          </ProcessingErrorElement>
        </ProcessingErrorElement>
      </ProcessingError>
    </Problem>
  </LookupRequestResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupRequestResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/generalprocessingerror.scm',
          'Type' => 'Agency Authentication Failed',
          'Element' => 'InitiationHeader',
          'Value' => 'xxxxx',
      ),
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupUserResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <UserIdentifierValue>{34944008-44e1-4469-8768-8d0506ca4b62}</UserIdentifierValue>
    </UniqueUserId>
    <UserOptionalFields>
      <VisibleUserId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-675143</Value>

        </UniqueAgencyId>
        <VisibleUserIdentifierType>
          <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/visibleuseridentifiertype/visibleuseridentifiertype.scm</Scheme>
          <Value>Barcode</Value>
        </VisibleUserIdentifierType>
        <VisibleUserIdentifier>0010295009</VisibleUserIdentifier>
      </VisibleUserId>

      <NameInformation>
        <PersonalNameInformation>
          <StructuredPersonalUserName>
            <GivenName>John</GivenName>
            <Surname>Smith</Surname>
          </StructuredPersonalUserName>
        </PersonalNameInformation>
      </NameInformation>

      <DateOfBirth>1980-11-23T00:00:00</DateOfBirth>
    </UserOptionalFields>
  </LookupUserResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupUserResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'UniqueUserId' => Array
      (
          'UserIdentifierValue' => '{34944008-44e1-4469-8768-8d0506ca4b62}',
          'UniqueAgencyId' => 'DK-675143',
      ),
  'GivenName' => 'John',
  'Surname' => 'Smith',
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <LookupUserResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <Problem>
      <ProcessingError>
        <ProcessingErrorType>
          <Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm</Scheme>

          <Value>Unknown User</Value>
        </ProcessingErrorType>
        <ProcessingErrorElement>
          <ElementName>VisibleUserId</ElementName>         
          <ProcessingErrorElement>
            <ElementName>VisibleUserIdentifier</ElementName>
            <ProcessingErrorValue>001029500</ProcessingErrorValue>

          </ProcessingErrorElement>
        </ProcessingErrorElement>
      </ProcessingError>
    </Problem>
  </LookupUserResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupUserResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm',
          'Type' => 'Unknown User',
          'Element' => 'VisibleUserId',
          'Value' => '001029500',
      ),
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <RenewItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <UniqueItemId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-775100</Value>

      </UniqueAgencyId>
      <ItemIdentifierValue>{1A57239B-13FD-40FF-B3EE-9F34237D6924}</ItemIdentifierValue>
    </UniqueItemId>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-675143</Value>

      </UniqueAgencyId>
      <UserIdentifierValue>{34944008-44e1-4469-8768-8d0506ca4b62}</UserIdentifierValue>
    </UniqueUserId>
    <DateDue>2008-10-23T00:00:00</DateDue>
    <UserOptionalFields>
      <VisibleUserId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
        <VisibleUserIdentifierType>
          <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/visibleuseridentifiertype/visibleuseridentifiertype.scm</Scheme>
          <Value>Barcode</Value>
        </VisibleUserIdentifierType>
        <VisibleUserIdentifier>0010295009</VisibleUserIdentifier>

      </VisibleUserId>
      <NameInformation>
        <PersonalNameInformation>
          <StructuredPersonalUserName>
            <GivenName>John</GivenName>
            <Surname>Smith</Surname>
          </StructuredPersonalUserName>
        </PersonalNameInformation>

      </NameInformation>
      <DateOfBirth>1980-11-23T00:00:00</DateOfBirth>
    </UserOptionalFields>
  </RenewItemResponse>
</NCIPMessage>',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'UniqueItemId' => Array
      (
          'ItemIdentifierValue' => '{1A57239B-13FD-40FF-B3EE-9F34237D6924}',
          'UniqueAgencyId' => 'DK-775100',
      ),
  'DateDue' => '1224712800',
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <RenewItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <Problem>
      <ProcessingError>
        <ProcessingErrorType>
          <Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm</Scheme>

          <Value>Maximum Renewals Exceeded</Value>
        </ProcessingErrorType>
        <ProcessingErrorElement>
          <ElementName>UniqueItemId</ElementName>
          <ProcessingErrorElement>
            <ElementName>ItemIdentifierValue</ElementName>
            <ProcessingErrorValue>{1A57239B-13FD-40FF-B3EE-9F34237D6924}</ProcessingErrorValue>

          </ProcessingErrorElement>
        </ProcessingErrorElement>
      </ProcessingError>
    </Problem>
  </RenewItemResponse>
</NCIPMessage>',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm',
          'Type' => 'Maximum Renewals Exceeded',
          'Element' => 'UniqueItemId',
          'Value' => '{1A57239B-13FD-40FF-B3EE-9F34237D6924}',
      ),
)),

//------------------------------------------------------------------------------
//Combined public-school libraries
array('
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.0//EN" "http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
<!--..................................................................-->
<!--Created by Dantek Sp. z o.o. on 22/08/2008                        -->
<!--..................................................................-->
<NCIPMessage version="http://www.niso.org/ncip/v1_0/imp1/dtd/ncip_v1_0.dtd">
  <RenewItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-675143</Value>
        </UniqueAgencyId>
      </FromAgencyId>
      <FromAgencyAuthentication>xxxxx</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>

          <Value>DK-775100</Value>
        </UniqueAgencyId>
      </ToAgencyId>
    </ResponseHeader>
    <Problem>
      <ProcessingError>
        <ProcessingErrorType>
          <Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm</Scheme>

          <Value>Renewal Not Allowed - Item Has Outstanding Requests</Value>
        </ProcessingErrorType>
        <ProcessingErrorElement>
          <ElementName>UniqueItemId</ElementName>
          <ProcessingErrorElement>
            <ElementName>ItemIdentifierValue</ElementName>
            <ProcessingErrorValue>{1A57239B-13FD-40FF-B3EE-9F34237D6924}</ProcessingErrorValue>

          </ProcessingErrorElement>
        </ProcessingErrorElement>
      </ProcessingError>
    </Problem>
  </RenewItemResponse>
</NCIPMessage>',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-675143',
  'FromAgencyAuthentication' => 'xxxxx',
  'ToAgencyId' => 'DK-775100',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm',
          'Type' => 'Renewal Not Allowed - Item Has Outstanding Requests',
          'Element' => 'UniqueItemId',
          'Value' => '{1A57239B-13FD-40FF-B3EE-9F34237D6924}',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
  <RequestItemResponse>
    <ResponseHeader>
      <FromAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-715700</Value>
        </UniqueAgencyId>

      </FromAgencyId>
      <FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
      <ToAgencyId>
        <UniqueAgencyId>
          <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
          <Value>DK-190101</Value>
        </UniqueAgencyId>

      </ToAgencyId>
    </ResponseHeader>
    <UniqueRequestId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-715700</Value>
      </UniqueAgencyId>
      <RequestIdentifierValue>23770053</RequestIdentifierValue>

    </UniqueRequestId>
    <UniqueUserId>
      <UniqueAgencyId>
        <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
        <Value>DK-715700</Value>
      </UniqueAgencyId>
      <UserIdentifierValue>1231231230</UserIdentifierValue>

    </UniqueUserId>
    <RequestType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
      <Value>Hold</Value>
    </RequestType>
    <RequestScopeType>
      <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requestscopetype/requestscopetype.scm</Scheme>

      <Value>Bibliographic Item</Value>
    </RequestScopeType>
    <DateAvailable>2008-02-12T00:00+01:00</DateAvailable>
  </RequestItemResponse>
</NCIPMessage>
',
array(
  'Problem' => Array
      (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'RequestItemResponse',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RequestItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/requestitemprocessingerror.scm</Scheme>
					<Value>Item Not Available By Need Before Date</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>NeedBeforeDate</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</RequestItemResponse>
</NCIPMessage>
',
array(
  'Problem' => Array
      (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'RequestItemResponse',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>12345678</ItemIdentifierValue>
		</UniqueItemId>
		<ItemOptionalFields>
			<BibliographicDescription>
				<Author>Author</Author>
				<PublicationDate>Year</PublicationDate>
				<Title>Title</Title>

			</BibliographicDescription>
		</ItemOptionalFields>
	</LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueItemId' => Array
      (
          'ItemIdentifierValue' => '12345678',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'Author' => 'Author',
  'PublicationDate' => 'Year',
  'Title' => 'Title',
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupitemprocessingerror.scm</Scheme>
					<Value>Unknown Item</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>UniqueItemId</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupitemprocessingerror.scm',
          'Type' => 'Unknown Item',
          'Element' => 'UniqueItemId',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupRequestResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueRequestId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<RequestIdentifierValue>87654321</RequestIdentifierValue>
		</UniqueRequestId>
		<ShippingInformation>
			<PhysicalAddress>
				<StructuredAddress>
					<LocationWithinBuilding>451</LocationWithinBuilding>
					<District></District>
					<Locality>hb</Locality>  
				</StructuredAddress>

				<PhysicalAddressType>
					<Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/physicaladdresstype/physicaladdresstype.scm</Scheme>
					<Value>Postal Address</Value>
				</PhysicalAddressType>
			</PhysicalAddress>
		</ShippingInformation>
		<ItemOptionalFields>
			<BibliographicDescription>

				<Author>Author</Author>
				<PublicationDate>Year</PublicationDate>
				<Title>Title</Title>
			</BibliographicDescription>
		</ItemOptionalFields>
	</LookupRequestResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupRequestResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueRequestId' => Array
      (
          'RequestIdentifierValue' => '87654321',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'Author' => 'Author',
  'PublicationDate' => 'Year',
  'Title' => 'Title',
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_01/schemes/processingerrortype/lookuprequestprocessingerror.scm</Scheme>
					<Value>Unknown Request</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>UniqueRequestId</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</LookupItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_01/schemes/processingerrortype/lookuprequestprocessingerror.scm',
          'Type' => 'Unknown Request',
          'Element' => 'UniqueRequestId',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
   <LookupUserResponse>
      <ResponseHeader>
         <FromAgencyId>
            <UniqueAgencyId>
               <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
               <Value>DK-710100</Value>

            </UniqueAgencyId>
         </FromAgencyId>
         <FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
         <ToAgencyId>
            <UniqueAgencyId>
               <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
               <Value>DK-190101</Value>

            </UniqueAgencyId>
         </ToAgencyId>
      </ResponseHeader>
      <UniqueUserId>
         <UniqueAgencyId>
            <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
            <Value>DK-710100</Value>
         </UniqueAgencyId>

         <UserIdentifierValue>1231231230</UserIdentifierValue>
      </UniqueUserId>
      <UserTransaction>
         <RequestedItem>
            <UniqueRequestId>
               <UniqueAgencyId>
                  <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
                  <Value>DK-710100</Value>

               </UniqueAgencyId>
               <RequestIdentifierValue>87654321</RequestIdentifierValue>
            </UniqueRequestId>
            <RequestType>
               <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requesttype/requesttype.scm</Scheme>
               <Value>Hold</Value>
            </RequestType>

            <RequestStatusType>
               <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/requeststatustype/requeststatustype.scm</Scheme>
               <Value>In Process</Value>
            </RequestStatusType>
            <DatePlaced>2008-01-10T14:35:00+01:00</DatePlaced>
         </RequestedItem>
         <LoanedItem>

            <UniqueItemId>
               <UniqueAgencyId>
                  <Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
                  <Value>DK-710100</Value>
               </UniqueAgencyId>
               <ItemIdentifierValue>12345678</ItemIdentifierValue>
            </UniqueItemId>

            <ReminderLevel>1</ReminderLevel>
            <DateDue>2008-03-16T20:00:00+01:00</DateDue>
            <Amount>
               <CurrencyCode>
                  <Scheme>http://www.bsi-global.com/Technical+Information/Publications/_Publications/tig90x.doc</Scheme>
                  <Value>DKK</Value>
               </CurrencyCode>

               <MonetaryValue>0</MonetaryValue>
            </Amount>
         </LoanedItem>
      </UserTransaction>
      <UserOptionalFields>
         <VisibleUserId>
            <VisibleUserIdentifierType>
               <Scheme>http://biblstandard.dk/ncip/schemes/CPR/1.0/</Scheme>

               <Value>CPR</Value>
            </VisibleUserIdentifierType>
            <VisibleUserIdentifier>0212611237</VisibleUserIdentifier>
         </VisibleUserId>
         <NameInformation>
            <PersonalNameInformation>
               <StructuredPersonalUserName>
                  <GivenName>Givenname</GivenName>

                  <Surname>Surname</Surname>
               </StructuredPersonalUserName>
            </PersonalNameInformation>
         </NameInformation>
         <UserAddressInformation>
            <UserAddressRoleType>
               <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/useraddressroletype/useraddressroletype.scm</Scheme>
               <Value>Home</Value>

            </UserAddressRoleType>
            <PhysicalAddress>
               <StructuredAddress>
                  <Street>Humlebakken 17</Street>
                  <District>851</District>
                  <PostalCode>9000  Aalborg</PostalCode>
               </StructuredAddress>

               <PhysicalAddressType>
                  <Scheme>http://www.niso.org/ncip/v1_0/imp1/schemes/physicaladdresstype/physicaladdresstype.scm</Scheme>
                  <Value>Postal Address</Value>
               </PhysicalAddressType>
            </PhysicalAddress>
         </UserAddressInformation>
         <DateOfBirth>1961-02-12T00:00:00</DateOfBirth>

      </UserOptionalFields>
   </LookupUserResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupUserResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'UniqueUserId' => Array
      (
          'UserIdentifierValue' => '1231231230',
          'UniqueAgencyId' => 'DK-710100',
      ),
  'RequestedItem' => Array
      (
          '0' => Array
              (
                  'UniqueRequestId' => Array
                      (
                          'RequestIdentifierValue' => '87654321',
                          'UniqueAgencyId' => 'DK-710100',
                      ),
                  'RequestType' => 'Hold',
                  'RequestStatusType' => 'In Process',
                  'DatePlaced' => '1199972100',
              ),
      ),
  'LoanedItem' => Array
      (
          '0' => Array
              (
                  'UniqueItemId' => Array
                      (
                          'ItemIdentifierValue' => '12345678',
                          'UniqueAgencyId' => 'DK-710100',
                      ),
                  'ReminderLevel' => 1,
                  'DateDue' => '1205694000',
                  'CurrencyCode' => 'DKK',
                  'MonetaryValue' => 0,
              ),
      ),
  'GivenName' => 'Givenname',
  'Surname' => 'Surname',
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<LookupUserResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm</Scheme>
					<Value>User Authentication Failed</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>AuthenticationInput</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</LookupUserResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'LookupUserResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/lookupuserprocessingerror.scm',
          'Type' => 'User Authentication Failed',
          'Element' => 'AuthenticationInput',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190103</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueItemId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-715700</Value>
			</UniqueAgencyId>

			<ItemIdentifierValue>27175953</ItemIdentifierValue>
		</UniqueItemId>
		<DateDue>2008-09-20T10:00+01:00</DateDue>
	</RenewItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-190103',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-715700',
  'UniqueItemId' => Array
      (
          'ItemIdentifierValue' => '27175953',
          'UniqueAgencyId' => 'DK-715700',
      ),
  'DateDue' => '1221901200',
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190103</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-715700</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Pending>
			<DateOfExpectedReply>2008-09-20T10:00+01:00</DateOfExpectedReply>
		</Pending>
	</RenewItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-190103',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-715700',
  'DateOfExpectedReply' => '1221901200',
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<RenewItemResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm</Scheme>
					<Value>Maximum Renewals Exceeded</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>UniqueItemId</ElementName>
				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</RenewItemResponse>
</NCIPMessage>
',
array(
  'Ncip' => 'RenewItemResponse',
  'FromAgencyId' => 'DK-710100',
  'FromAgencyAuthentication' => '[PASSWORD]',
  'ToAgencyId' => 'DK-190101',
  'Problem' => Array
      (
          'Error' => 'ProcessingError',
          'Scheme' => 'http://www.niso.org/ncip/v1_0/schemes/processingerrortype/renewitemprocessingerror.scm',
          'Type' => 'Maximum Renewals Exceeded',
          'Element' => 'UniqueItemId',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<CreateUserResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<UniqueUserId>
			<UniqueAgencyId>
				<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
				<Value>DK-710100</Value>
			</UniqueAgencyId>

			<UserIdentifierValue>1231231230</UserIdentifierValue>
		</UniqueUserId>
	</CreateUserResponse>
</NCIPMessage>
',
array(
  'Problem' => Array
      (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'CreateUserResponse',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
// Collaboration between General User Interfaces being Brokers and Local Library Systems
array('
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE NCIPMessage PUBLIC "-//NISO//NCIP DTD Version 1.01//EN" "http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
<NCIPMessage version="http://ncip.envisionware.com/documentation/ncip_v1_01.dtd">
	<CreateUserResponse>
		<ResponseHeader>
			<FromAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-710100</Value>

				</UniqueAgencyId>
			</FromAgencyId>
			<FromAgencyAuthentication>[PASSWORD]</FromAgencyAuthentication>
			<ToAgencyId>
				<UniqueAgencyId>
					<Scheme>http://biblstandard.dk/isil/schemes/1.1/</Scheme>
					<Value>DK-190101</Value>

				</UniqueAgencyId>
			</ToAgencyId>
		</ResponseHeader>
		<Problem>
			<ProcessingError>
				<ProcessingErrorType>
					<Scheme>http://www.niso.org/ncip/v1_0/schemes/processingerrortype/createuserprocessingerror.scm</Scheme>
					<Value>Duplicate User</Value>

				</ProcessingErrorType>
				<ProcessingErrorElement>
					<ElementName>VisibleUserId</ElementName>
					<ProcessingErrorElement>
						<ElementName>VisibleUserIdentifier</ElementName>
						<ProcessingErrorValue>0212611237</ProcessingErrorValue>
					</ProcessingErrorElement>

				</ProcessingErrorElement>
			</ProcessingError>
		</Problem>
	</CreateUserResponse>
</NCIPMessage>
',
array(
  'Problem' => Array
      (
          'Error' => 'MessagingError',
          'Type' => 'Unsupported Service',
          'Element' => 'CreateUserResponse',
          'Scheme' => 'NCIP General Processing Error Scheme',
      ),
)),

//------------------------------------------------------------------------------
    );
  }

  /**
  * @dataProvider parseResponses
  */
  public function testParseResponses($input, $expected_result) {
    $ncip = new ncip();
    $this->assertEquals($expected_result, $ncip->parse(trim($input)));
  }




}
?>