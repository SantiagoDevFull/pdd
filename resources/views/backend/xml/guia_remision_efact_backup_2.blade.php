<?xml version="1.0" encoding="UTF-8"?>
<DespatchAdvice xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2" 
xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
<cbc:UBLVersionID>2.1</cbc:UBLVersionID>
<cbc:CustomizationID>2.0</cbc:CustomizationID>
<cbc:ID>{{ $obj->referral_serie_number }}-{{ $obj->referral_voucher_number }}</cbc:ID>
<cbc:IssueDate>{{ $obj->issue_date }}</cbc:IssueDate>
<!--TIPO DE DOCUMENTO 09 = GUIA DE REMISION REMITENTE -->
<cbc:DespatchAdviceTypeCode>09</cbc:DespatchAdviceTypeCode>
<cbc:Note>{{ $obj->serie_number }}-{{ $obj->voucher_number }} </cbc:Note>
<cbc:LineCountNumeric>1</cbc:LineCountNumeric>
<cac:Signature>
<cbc:ID>IDSignature</cbc:ID>
<cac:SignatoryParty>
<cac:PartyIdentification>
<cbc:ID>{{ $obj->company_document_number }}</cbc:ID>
</cac:PartyIdentification>
<cac:PartyName>
<cbc:Name>{{ $obj->company_name }}</cbc:Name>
</cac:PartyName>
</cac:SignatoryParty>
<cac:DigitalSignatureAttachment>
<cac:ExternalReference>
<cbc:URI>IDSignature</cbc:URI>
</cac:ExternalReference>
</cac:DigitalSignatureAttachment>
</cac:Signature>
<cac:DespatchSupplierParty>
<cac:Party>
<cac:PartyIdentification>
<cbc:ID schemeID="6">{{ $obj->company_document_number }}</cbc:ID>
</cac:PartyIdentification>
<!-- Datos del remitente -->
<cac:PostalAddress>
<cbc:ID>{{ $obj->company_ubigeo }}</cbc:ID>
<cbc:StreetName>{{ $obj->company_addresses }}</cbc:StreetName>
<cbc:CitySubdivisionName>URBANIZACIÓN</cbc:CitySubdivisionName>
<cbc:CityName>{{ $obj->company_province }}</cbc:CityName>
<cbc:CountrySubentity>{{ $obj->company_department }}</cbc:CountrySubentity>
<cbc:District>{{ $obj->company_district }}</cbc:District>
<cac:Country>
<cbc:IdentificationCode>PE</cbc:IdentificationCode>
</cac:Country>
</cac:PostalAddress>
<cac:PartyLegalEntity>
<cbc:RegistrationName>{{ $obj->company_name }}</cbc:RegistrationName>
</cac:PartyLegalEntity>
</cac:Party>
</cac:DespatchSupplierParty>
<cac:DeliveryCustomerParty>
<cac:Party>
<cac:PartyIdentification>
    <!-- aqui -->
<cbc:ID schemeID="0">12345678</cbc:ID>
</cac:PartyIdentification>
<cac:PostalAddress>
<cbc:ID>15122</cbc:ID>
<cbc:StreetName>Lima</cbc:StreetName>
<cbc:CitySubdivisionName>Lima</cbc:CitySubdivisionName>
<cbc:CityName>Lima</cbc:CityName>
<cbc:CountrySubentity>Lima</cbc:CountrySubentity>
<cbc:District>Lima</cbc:District>
<cac:Country>
<cbc:IdentificationCode>PE</cbc:IdentificationCode>
</cac:Country>
</cac:PostalAddress>
<cac:PartyLegalEntity>
<cbc:RegistrationName>Emisor Itinerante</cbc:RegistrationName>
</cac:PartyLegalEntity>
<cac:Contact>
<cbc:ElectronicMail>enviofacturacion@puntodedistribucion.com</cbc:ElectronicMail>
</cac:Contact>
</cac:Party>
</cac:DeliveryCustomerParty>
<!-- SUNAT MOTIVO DE TRASLADO -->
<cac:Shipment>
<cbc:ID>SUNAT_Envio</cbc:ID>
<cbc:HandlingCode>18</cbc:HandlingCode>
<cbc:HandlingInstructions>Traslado emisor itinerante CP</cbc:HandlingInstructions>
<cbc:GrossWeightMeasure unitCode="KGM">{{ $kg }}</cbc:GrossWeightMeasure>
<cac:ShipmentStage>
<cbc:TransportModeCode>02</cbc:TransportModeCode>
<cac:TransitPeriod>
<cbc:StartDate>{{ $obj->guides_traslate_date }}</cbc:StartDate>
</cac:TransitPeriod>
    <!-- Datos del CONDUCTOR -->
<cac:DriverPerson>
<cbc:ID schemeID="4">{{ $obj->employe_document_number }}</cbc:ID>
<cbc:FirstName>{{ $obj->employe_last_name }}</cbc:FirstName>
<cbc:FamilyName>{{ $obj->employe_first_name }}</cbc:FamilyName>
<cbc:JobTitle>Principal</cbc:JobTitle>
<cac:IdentityDocumentReference>
<cbc:ID>{{ $obj->employe_license }}</cbc:ID>
</cac:IdentityDocumentReference>
</cac:DriverPerson>
</cac:ShipmentStage>
<!-- Datos del PUNTO DE LLEGADA -->
<cac:Delivery>
<cac:DeliveryAddress>
<cbc:ID>{{ $obj->ubigeo_ubigeo }}</cbc:ID>
<cbc:CitySubdivisionName>URBANIZACIÓN</cbc:CitySubdivisionName>
<cbc:CityName>{{ $obj->ubigeo_province }}</cbc:CityName>
<cbc:CountrySubentity>{{ $obj->ubigeo_department }}</cbc:CountrySubentity>
<cbc:District>{{ $obj->ubigeo_district }}</cbc:District>
<cac:AddressLine>
<cbc:Line>AV. CAJAMARCA NRO. 586 APV. EL DORADO URB.ZAPALLAL LIMA - LIMA - PUENTE PIEDRA</cbc:Line>
</cac:AddressLine>
<cac:Country>
<cbc:IdentificationCode>PE</cbc:IdentificationCode>
</cac:Country>
</cac:DeliveryAddress>
<cac:Despatch>
<cac:DespatchAddress>
<cbc:ID>{{ $obj->company_ubigeo }}</cbc:ID>
<cbc:CitySubdivisionName>URBANIZACIÓN</cbc:CitySubdivisionName>
<cbc:CityName>{{ $obj->company_province }}</cbc:CityName>
<cbc:CountrySubentity>{{ $obj->company_department }}</cbc:CountrySubentity>
<cbc:District>{{ $obj->company_district }}</cbc:District>
<cac:AddressLine>
<cbc:Line>{{ $obj->company_addresses }}</cbc:Line>
</cac:AddressLine>
<cac:Country>
<cbc:IdentificationCode>PE</cbc:IdentificationCode>
</cac:Country>
</cac:DespatchAddress>
</cac:Despatch>
</cac:Delivery>
<cac:TransportHandlingUnit>
<cac:TransportEquipment>
<cbc:ID>{{ str_replace('-','',$obj->placa,$contador) }}</cbc:ID>
</cac:TransportEquipment>
</cac:TransportHandlingUnit>
</cac:Shipment>
@foreach ($guides_detail as $index => $detail)
    <cac:DespatchLine>
    <cbc:ID>{{ ++$index }}</cbc:ID>
    <cbc:Note>UNIDAD</cbc:Note>
    <cbc:DeliveredQuantity unitCode="{{ $detail->unit_short_name }}">{{ number_format($detail->quantity, 2,'.','') }}</cbc:DeliveredQuantity>
    <cac:OrderLineReference>
    <cbc:LineID>1</cbc:LineID>
    </cac:OrderLineReference>
    <cac:Item>
    <cbc:Description>{{ $detail->name }}</cbc:Description>
    <cac:SellersItemIdentification>
    <cbc:ID>{{ $detail->article_id }}</cbc:ID>
    </cac:SellersItemIdentification>
    </cac:Item>
    </cac:DespatchLine>
@endforeach
</DespatchAdvice>