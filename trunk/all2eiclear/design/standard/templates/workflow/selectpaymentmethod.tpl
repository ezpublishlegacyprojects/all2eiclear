{def $user=fetch( 'user', 'current_user' )}
<div style="background: #FFFFFF;">

<h1 class="h_sub strong">{"Select payment method:"|i18n("spocomm/shop")}</h1>
                
<form method="post" action={"shop/checkout"|ezurl}>
    <p>{"Please select your preferred payment method"|i18n("spocomm/shop")}</p>
    <div>
        <label><input name="SelectedPaymentMethod" type="radio" value="iclear" checked="checked" />iClear</label><br />
        <br />
        <label><input name="SelectedPaymentMethod" type="radio" value="prepayment" />Vorkasse</label><br />
        <br />
    </div> 

    <div class="space_top">
        <input class="button" type="submit" name="CancelButton"  value="{'Cancel'|i18n('design/standard/workflow')}" />
        <input class="defaultbutton" type="submit" name="SelectButton"  value="{'Select'|i18n('design/standard/workflow')}" />  
    </div>
</form>

</div>
