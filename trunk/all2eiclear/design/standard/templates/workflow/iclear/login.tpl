{def $user=fetch( 'user', 'current_user' )}
<div style="background: #FFFFFF;">

<h1 class="h_sub strong">{"Please log into your iclear account"|i18n("payment/iclear")}</h1>

{if $error}
<div class="warning">
<h2>{"Could not login"|i18n("design/standard/user")}</h2>
<ul>
    <li>{"A valid username and password is required to login."|i18n("design/standard/user")}</li>
</ul>
</div>
{/if}

<form method="post" action={"shop/checkout"|ezurl}>
    <p>{"Please log into your iclear account"|i18n("payment/iclear")}</p>
    <div>
        <label>Account name:</label><input name="iclearAccount" type="text" /><br />
        <br />
        <label>Password</label><input name="iclearAccountPW" type="password" /><br />
        <br />
    </div> 

    <div class="space_top">
        <input class="button" type="submit" name="CancelButton"  value="{'Cancel'|i18n('design/standard/workflow')}" />
        <input class="defaultbutton" type="submit" name="SelectButton"  value="{'Select'|i18n('design/standard/workflow')}" />  
    </div>
</form>

</div>
