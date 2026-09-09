<?xml version="1.0" encoding="UTF-8"?>
<SummaryDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:SummaryDocuments-1" 
xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1">
<cbc:UBLVersionID>2.0</cbc:UBLVersionID>
<cbc:CustomizationID>1.1</cbc:CustomizationID>
<cbc:ID>RC-{{ date('Ymd') }}-{{ $correlativo }}</cbc:ID>
<cbc:ReferenceDate>{{ $emision }}</cbc:ReferenceDate>
<cbc:IssueDate>{{ date('Y-m-d') }}</cbc:IssueDate>
<cac:Signature>
<cbc:ID>IDSignKG</cbc:ID>
<cac:SignatoryParty>
<cac:PartyIdentification>
<cbc:ID>{{ $ruc_emisor }}</cbc:ID>
</cac:PartyIdentification>
<cac:PartyName>
<cbc:Name>{{ $razon_emisor }}</cbc:Name>
</cac:PartyName>
</cac:SignatoryParty>
<cac:DigitalSignatureAttachment>
<cac:ExternalReference>
<cbc:URI>#SignST</cbc:URI>
</cac:ExternalReference> 
</cac:DigitalSignatureAttachment>
</cac:Signature>
<cac:AccountingSupplierParty>
<cbc:CustomerAssignedAccountID>{{ $ruc_emisor }}</cbc:CustomerAssignedAccountID>
<cbc:AdditionalAccountID>6</cbc:AdditionalAccountID>
<cac:Party>
<cac:PartyLegalEntity>
<cbc:RegistrationName>{{ $razon_emisor }}</cbc:RegistrationName>
</cac:PartyLegalEntity>
</cac:Party>
</cac:AccountingSupplierParty>

@foreach($vouchers as  $index => $obj)
<sac:SummaryDocumentsLine>
    <cbc:LineID>{{ ++$index }}</cbc:LineID>
    <cbc:DocumentTypeCode>03</cbc:DocumentTypeCode>
    <cbc:ID>{{ $obj->serie_number }}-{{ $obj->voucher_number }}</cbc:ID>
    <cac:AccountingCustomerParty>
    <cbc:CustomerAssignedAccountID>{{ $obj->client_document_number }}</cbc:CustomerAssignedAccountID>
    <cbc:AdditionalAccountID>1</cbc:AdditionalAccountID>
    </cac:AccountingCustomerParty>
    <cac:Status>
    <cbc:ConditionCode>1</cbc:ConditionCode>
    </cac:Status>
    <sac:TotalAmount currencyID="{{ $obj->currency_short_name }}">{{ $obj->total }}</sac:TotalAmount>
    <sac:BillingPayment>
    <cbc:PaidAmount currencyID="{{ $obj->currency_short_name }}">{{ number_format($obj->taxed_operation, 2, '.', '') }}</cbc:PaidAmount>
    <cbc:InstructionID>01</cbc:InstructionID>
    </sac:BillingPayment>
    <cac:TaxTotal>
    <cbc:TaxAmount currencyID="{{ $obj->currency_short_name }}">{{ $obj->igv }}</cbc:TaxAmount>
    <cac:TaxSubtotal>
    <cbc:TaxAmount currencyID="{{ $obj->currency_short_name }}">{{ $obj->igv }}</cbc:TaxAmount>
    <cac:TaxCategory>
    <cac:TaxScheme>
    <cbc:ID>1000</cbc:ID>
    <cbc:Name>IGV</cbc:Name>
    <cbc:TaxTypeCode>VAT</cbc:TaxTypeCode>
    </cac:TaxScheme>
    </cac:TaxCategory>
    </cac:TaxSubtotal>
    </cac:TaxTotal>
    </sac:SummaryDocumentsLine>
@endforeach


</SummaryDocuments>