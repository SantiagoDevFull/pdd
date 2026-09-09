<DespatchAdvice xmlns="urn:oasis:names:specification:ubl:schema:xsd:DespatchAdvice-2" xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <cbc:UBLVersionID>2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2.0</cbc:CustomizationID>
    <cbc:ID>{{ $obj->serie_number }}-{{ $obj->voucher_number }}</cbc:ID>
    <cbc:IssueDate>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</cbc:IssueDate>
    <cbc:IssueTime>{{ \Carbon\Carbon::now()->format('H:i:s') }}</cbc:IssueTime>
    <cbc:DespatchAdviceTypeCode>31</cbc:DespatchAdviceTypeCode>
    <cbc:Note>{{ $total_text }}</cbc:Note>
    <cbc:LineCountNumeric>1</cbc:LineCountNumeric>

    <cac:Signature>
    <cbc:ID>IDSignKG</cbc:ID>
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
    <cbc:URI>#SignST</cbc:URI>
    </cac:ExternalReference>
    </cac:DigitalSignatureAttachment>
    </cac:Signature>

    <cac:DespatchSupplierParty>
    <cac:Party>
    <cac:PartyIdentification>
    <cbc:ID schemeID="6">{{ $obj->company_document_number }}</cbc:ID>
    </cac:PartyIdentification>
    <cac:PostalAddress>
    <cbc:ID>{{ $obj->company_ubigeo }}</cbc:ID>
    <cbc:StreetName>{{ $obj->company_addresses }}</cbc:StreetName>
    <cbc:CitySubdivisionName>URBANIZACION</cbc:CitySubdivisionName>
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
    <cbc:ID schemeID="{{ $obj->client_type_doc }}">{{ $obj->client_doc_number }}</cbc:ID>
    </cac:PartyIdentification>
    <cac:PostalAddress>
    <cbc:ID>{{ $obj->client_ubigeo_id }}</cbc:ID>
    <cbc:StreetName>{{ $obj->client_address }}</cbc:StreetName>
    <cbc:CitySubdivisionName>URBANIZACION</cbc:CitySubdivisionName>
    <cbc:CityName>{{ $obj->client_province }}</cbc:CityName>
    <cbc:CountrySubentity>{{ $obj->client_country }}</cbc:CountrySubentity>
    <cbc:District>{{ $obj->client_district }}</cbc:District>
    <cac:Country>
    <cbc:IdentificationCode>PE</cbc:IdentificationCode>
    </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
    <cbc:RegistrationName>{{ $obj->client_name }}</cbc:RegistrationName>
    </cac:PartyLegalEntity>
    <cac:Contact>
    <cbc:ElectronicMail>enviofacturacion@puntodedistribucion.com</cbc:ElectronicMail>
    </cac:Contact>
    </cac:Party>
    </cac:DeliveryCustomerParty>

    <cac:Shipment>
    <cbc:ID>SUNAT_Envio</cbc:ID>
    <cbc:GrossWeightMeasure unitCode="KGM">{{ $kg }}</cbc:GrossWeightMeasure>
    <cbc:SpecialInstructions>SUNAT_Envio_IndicadorTrasladoTotal</cbc:SpecialInstructions>
    <cbc:SpecialInstructions>SUNAT_Envio_IndicadorRetornoVehiculoVacio</cbc:SpecialInstructions>
    <cbc:SpecialInstructions>SUNAT_Envio_IndicadorPagadorFlete_Remitente</cbc:SpecialInstructions>

    <cac:ShipmentStage>
    <cac:TransitPeriod>
    <cbc:StartDate>{{ $obj->guides_traslate_date }}</cbc:StartDate>
    </cac:TransitPeriod>

    <cac:DriverPerson>
    <cbc:ID schemeID="{{ $obj->document_type }}">{{ $obj->employe_document_number }}</cbc:ID>
    <cbc:FirstName>{{ $obj->employe_last_name }}</cbc:FirstName>
    <cbc:FamilyName>{{ $obj->employe_first_name }}</cbc:FamilyName>
    <cbc:JobTitle>Principal</cbc:JobTitle>
    <cac:IdentityDocumentReference>
    <cbc:ID>{{ $obj->employe_license }}</cbc:ID>
    </cac:IdentityDocumentReference>
    </cac:DriverPerson>

    </cac:ShipmentStage>

    <cac:Delivery>
    <cac:DeliveryAddress>
    <cbc:ID>{{ $obj->ubigeo_ubigeo }}</cbc:ID>
    <cac:AddressLine>
    <cbc:Line>AV. CAJAMARCA NRO. 586 APV. EL DORADO URB.ZAPALLAL LIMA - LIMA - PUENTE PIEDRA</cbc:Line>
    </cac:AddressLine>
    </cac:DeliveryAddress>
    <cac:Despatch>
    <cac:DespatchAddress>
    <cbc:ID>{{ $obj->company_ubigeo }}</cbc:ID>
    <cac:AddressLine>
    <cbc:Line>{{ $obj->company_addresses }}</cbc:Line>
    </cac:AddressLine>
    </cac:DespatchAddress>
    
    <!-- TRANSPORTISTA -->
    <cac:DespatchParty>
    <cac:PartyIdentification>
    <cbc:ID schemeID="6">20293658146</cbc:ID>
    </cac:PartyIdentification>
    <cac:PostalAddress>
    <cbc:ID>150125</cbc:ID>
    <cbc:StreetName>AV. CAJAMARCA NRO. 586 APV. EL DORADO -URB ZAPALLAL</cbc:StreetName>
    <cbc:CitySubdivisionName>URBANIZACION</cbc:CitySubdivisionName>
    <cbc:CityName>LIMA</cbc:CityName>
    <cbc:CountrySubentity>LIMA</cbc:CountrySubentity>
    <cbc:District>PUENTE PIEDRA</cbc:District>
    <cac:Country>
    <cbc:IdentificationCode>PE</cbc:IdentificationCode>
    </cac:Country>
    </cac:PostalAddress>
    <cac:PartyLegalEntity>
    <cbc:RegistrationName>PUNTO DE DISTRIBUCION S.A.C</cbc:RegistrationName>
    </cac:PartyLegalEntity>
    <cac:Contact>
    <cbc:ElectronicMail>enviofacturacion@puntodedistribucion.com</cbc:ElectronicMail>
    </cac:Contact>
    </cac:DespatchParty>

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
    <cbc:Note>CILINDRO</cbc:Note>
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