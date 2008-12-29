<?php

header('Content-Type: text/xml; charset=utf-8');
//header('Cache-Control: must-revalidate, pre-check=0, no-store, no-cache, max-age=0, post-check=0');

echo '
<definitions xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/" xmlns:tns="urn:ICShopServices" xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/" xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" xmlns="http://schemas.xmlsoap.org/wsdl/" targetNamespace="urn:ICShopServices">
	<wsdl:types>
		<xsd:schema targetNamespace="urn:ICShopServices">
			<xsd:import namespace="http://schemas.xmlsoap.org/soap/encoding/"/>
			<xsd:import namespace="http://schemas.xmlsoap.org/wsdl/"/>
			<xsd:complexType name="Address">
				<xsd:all>
					<xsd:element name="addrAnrede" type="xsd:string"/>
					<xsd:element name="addrFirstname" type="xsd:string"/>
					<xsd:element name="addrLastname" type="xsd:string"/>
					<xsd:element name="addrOrgname" type="xsd:string"/>
					<xsd:element name="addrStrasse" type="xsd:string"/>
					<xsd:element name="addrHausNr" type="xsd:string"/>
					<xsd:element name="addrPLZ" type="xsd:string"/>
					<xsd:element name="addrOrt" type="xsd:string"/>
					<xsd:element name="addrLand" type="xsd:string"/>
				</xsd:all>
			</xsd:complexType>
			<xsd:complexType name="BasketItem">
				<xsd:all>
					<xsd:element name="itemNr" type="xsd:string"/>
					<xsd:element name="title" type="xsd:string"/>
					<xsd:element name="numOfArtikel" type="xsd:long"/>
					<xsd:element name="priceN" type="xsd:long"/>
					<xsd:element name="priceB" type="xsd:long"/>
					<xsd:element name="ustSatz" type="xsd:float"/>
					<xsd:element name="Status" type="xsd:long"/>
				</xsd:all>
			</xsd:complexType>
			<xsd:complexType name="BasketItemList">
				<xsd:complexContent>
					<xsd:restriction base="SOAP-ENC:Array">
						<xsd:attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="tns:BasketItem[]"/>
					</xsd:restriction>
				</xsd:complexContent>
			</xsd:complexType>
		</xsd:schema>
	</wsdl:types>
	<message name="acceptOrderRequest">
		<part name="sessionID" type="xsd:string"/>
		<part name="basketID" type="xsd:string"/>
		<part name="currency" type="xsd:string"/>
		<part name="orderStatus" type="xsd:long"/>
		<part name="orderStatusMessage" type="xsd:string"/>
		<part name="BasketItemList" type="tns:BasketItemList"/>
		<part name="deliveryAddress" type="tns:Address"/>
		<part name="requestID" type="xsd:string"/>
	</message>
	<message name="acceptOrderResponse">
		<part name="requestID" type="xsd:string"/>
		<part name="status" type="xsd:long"/>
		<part name="statusMessage" type="xsd:string"/>
		<part name="shopURL" type="xsd:string"/>
	</message>
	<portType name="ICShopServicesPortType">
		<operation name="acceptOrder">
			<documentation>iclear shop side order web service</documentation>
			<input message="tns:acceptOrderRequest"/>
			<output message="tns:acceptOrderResponse"/>
		</operation>
	</portType>
	<binding name="ICShopServicesBinding" type="tns:ICShopServicesPortType">
		<soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
		<operation name="acceptOrder">
			<soap:operation soapAction="urn:ICShopServices#acceptOrder" style="rpc"/>
			<input>
				<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="urn:ICShopServices"/>
			</input>
			<output>
				<soap:body use="encoded" encodingStyle="http://schemas.xmlsoap.org/soap/encoding/" namespace="urn:ICShopServices"/>
			</output>
		</operation>
	</binding>
	<service name="ICShopServices">
		<port name="ICShopServicesPort" binding="tns:ICShopServicesBinding">
			<soap:address location="http://iclear.all2e.com/iclear_soap/accept_order"/>
		</port>
	</service>
</definitions>';

eZExecution::cleanExit();
?>

