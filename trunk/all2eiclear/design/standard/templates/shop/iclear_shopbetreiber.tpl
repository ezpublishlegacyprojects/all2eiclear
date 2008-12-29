{def $user=fetch( 'user', 'current_user' )}
<div style="background: #FFFFFF;">

<h1>{"Register Shop Customer"|i18n("")}</h1>
    {if $errors.text}{$errors.text|wash()}{/if}            
    <form method="post" action={"/iclear_soap/register"|ezurl}>
        <p>{"Please fill in the fields below"|i18n("")}</p>
        <div>
            <label><input name="FirmaName" type="text" value="{if $parameters.FirmaName}{$parameters.FirmaName}{/if}" />Firmenname</label><br />
            <label><input name="BetreiberVorname" type="text" value="{if $parameters.BetreiberVorname}{$parameters.BetreiberVorname}{/if}" />Betreiber Vorname</label><br />
            <label><input name="BetreiberNachname" type="text" value="{if $parameters.BetreiberNachname}{$parameters.BetreiberNachname}{/if}" />Betreiber Nachname</label><br />
            <label><input name="FirmaStrasse" type="text" value="{if $parameters.FirmaStrasse}{$parameters.FirmaStrasse}{/if}" />Strasse</label><br />
            <label><input name="FirmaHausNr" type="text" value="{if $parameters.FirmaHausNr}{$parameters.FirmaHausNr}{/if}" />Hausnummer</label><br />
            <label><input name="FirmaPLZ" type="text" value="{if $parameters.FirmaPLZ}{$parameters.FirmaPLZ}{/if}" />PLZ</label><br />
            <label><input name="FirmaOrt" type="text" value="{if $parameters.FirmaOrt}{$parameters.FirmaOrt}{/if}" />Ort</label><br />
            <label><input name="FirmaLand" type="text" value="{if $parameters.FirmaLand}{$parameters.FirmaLand}{/if}" />Land</label><br />
            <label><input name="FirmaEmail" type="text" value="{if $parameters.FirmaEmail}{$parameters.FirmaEmail}{/if}" />Email</label><br />
            <label><input name="FirmaFon" type="text" value="{if $parameters.FirmaFon}{$parameters.FirmaFon}{/if}" />Fon</label><br />
            <label><input name="FirmaFax" type="text" value="{if $parameters.FirmaFax}{$parameters.FirmaFax}{/if}" />Fax</label><br />
            <label><input name="FirmaBankName" type="text" value="{if $parameters.FirmaBankName}{$parameters.FirmaBankName}{/if}" />Bankname</label><br />
            <label><input name="FirmaBLZ" type="text" value="{if $parameters.FirmaBLZ}{$parameters.FirmaBLZ}{/if}" />BLZ</label><br />
            <label><input name="FirmaKto" type="text" value="{if $parameters.FirmaKto}{$parameters.FirmaKto}{/if}" />Kontonummer</label><br />
            <label><input name="FirmaKtoInhaber" type="text" value="{if $parameters.FirmaKtoInhaber}{$parameters.FirmaKtoInhaber}{/if}" />Kontoinhaber</label><br />
            <label><input name="FirmaUSTID" type="text" value="{if $parameters.FirmaUSTID}{$parameters.FirmaUSTID}{/if}" />Umsatzsteuer ID</label><br />
            <label><input name="FirmaStNr" type="text" value="{if $parameters.FirmaStNr}{$parameters.FirmaStNr}{/if}" />Steuernummer</label><br />
            <label><input name="FirmaFA" type="text" value="{if $parameters.FirmaFA}{$parameters.FirmaFA}{/if}" />Finanzamt</label><br />
            <label><input name="FirmaFALand" type="text" value="{if $parameters.FirmaFALand}{$parameters.FirmaFALand}{/if}" />Finanzamt Bundesland</label><br />
            <label><input name="FirmaHRBNr" type="text" value="{if $parameters.FirmaName}{$parameters.FirmaHRBNr}{/if}" />HRB Nummer</label><br />
        </div> 
    
        <div class="space_top">
            <input class="button" type="submit" name="Submit"  value="Register" />
            <input class="button" type="submit" name="Discard"  value="Discard" />
        </div>
        <input type="hidden" name="RedirectUriAfterDiscard" value="/Produkte" />
    </form>

</div>

